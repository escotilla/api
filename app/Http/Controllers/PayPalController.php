<?php

namespace App\Http\Controllers;

use App\Application;
use App\Factories\UserFactory;
use App\Loan;
use App\LoanPayment;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\Details;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\OpenIdSession;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Payout;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Webhook;
use PayPal\Api\WebhookEventType;
use PayPal\Api\WebProfile;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PayPalController extends EscotillaController
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'total' => 'required',
            'application_id' => 'required'
        ]);

        $total = $request->input('total');
        $applicationId = $request->input('application_id');

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT'),
                env('PAYPAL_SECRET')
            )
        );

        $user = Auth::user();
        $application = Application::find($applicationId);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency('USD');

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        $redirectUrl = new RedirectUrls();
        $redirectUrl->setReturnUrl("http://localhost:8000/success?success=true")
            ->setCancelUrl("http://localhost:8000/fail?success=false");

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrl);

        try {
            $payment->create($apiContext);
        } catch (PayPalConnectionException $ex) {
            return $this->errorResponse($ex->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $loanPayment = new LoanPayment();
        $loanPayment
            ->setAmount($total)
            ->setPaymentId($payment->getId())
            ->setStatus('pending');

        $loanPayment->save();
        $user->payments()->save($loanPayment);
        $application->payments()->save($loanPayment);

        return $this->successResponse($payment->getApprovalLink());
    }

    public function execute(Request $request)
    {
        $this->validate($request, [
            'success' => 'required',
            'paymentId' => 'required',
            'token' => 'required',
            'PayerID' => 'required',
        ]);

        $paymentId = $request->input('paymentId');
        $token = $request->input('token');
        $success = $request->input('success');
        $payerId = $request->input('PayerID');

        $apiContext = new ApiContext(new OAuthTokenCredential(env('PAYPAL_CLIENT'), env('PAYPAL_SECRET')));

        $loanPayment = LoanPayment::where('payment_id', $paymentId)->first();
        $payment = Payment::get($paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setSubtotal($loanPayment->amount);
        $amount->setCurrency('USD');
        $amount->setTotal($loanPayment->amount);
        $amount->setDetails($details);
        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);
        $apiContext = new ApiContext(new OAuthTokenCredential(env('PAYPAL_CLIENT'), env('PAYPAL_SECRET')));

        try {
            $result = $payment->execute($execution, $apiContext);
            try {
                $payment = Payment::get($paymentId, $apiContext);
                $loanPayment->status = $payment->getState();
                $loanPayment->save();
            } catch (Exception $ex) {
                $this->errorResponse($ex->getMessage(), $ex->getCode());
            }
        } catch (Exception $ex) {
            $this->errorResponse($ex->getMessage(), $ex->getCode());
        }

        $this->successResponse([]);
    }

    public function createProfile(Request $request)
    {
        $apiContext = new ApiContext(new OAuthTokenCredential(env('PAYPAL_CLIENT'), env('PAYPAL_SECRET')));

        $flowConfig = new FlowConfig();
        $flowConfig->setLandingPageType("Billing");
        $flowConfig->setBankTxnPendingUrl("http://localhost:8000");
        $flowConfig->setUserAction("commit");
        $flowConfig->setReturnUriHttpMethod("GET");

        $presentation = new Presentation();
        $presentation->setLogoImage("https://www.escotillafinanciera.com/wp-content/uploads/2016/08/logo.png")
            ->setBrandName("Escotilla Financiera")
            ->setLocaleCode("US")
            ->setNoteToSellerLabel("thanks!");

        $inputFields = new InputFields();
        $inputFields->setAllowNote(true)
            ->setNoShipping(1)
            ->setAddressOverride(0);

        $webProfile = new WebProfile();
        $webProfile->setName("Escotilla" . uniqid())
            ->setFlowConfig($flowConfig)
            ->setPresentation($presentation)
            ->setInputFields($inputFields)
            ->setTemporary(false);

        var_dump("made it here");
        $request = clone $webProfile;
        try {
            $createProfileResponse = $webProfile->create($apiContext);
        } catch (PayPalConnectionException $ex) {
            return $this->errorResponse($ex->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        var_dump("winner");
        return $this->successResponse($createProfileResponse->toJSON(0));
    }

    public function createOffer(Request $request)
    {
        $this->validate($request, [
            'total' => 'required',
            'application_id' => 'required',
            'term' => 'required',
            'frequency' => 'required',
            'interest_rate' => 'required',
        ]);

        $applicationId = $request->input('application_id');
        $total = $request->input('total');
        $term = $request->input('term');
        $frequency = $request->input('frequency');
        $interest_rate = $request->input('interest_rate');

        $application = Application::find($applicationId);

        if (is_null($application)) {
            return $this->errorResponse('Application not found', Response::HTTP_NOT_FOUND);
        }

        $user = $application->user;

        if (is_null($user)) {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }

        $loan = new Loan();
        $loan->principal = $total;
        $loan->term = $term;
        $loan->frequency = $frequency;
        $loan->interest_rate = $interest_rate;
        $loan->status = 'offer';

        $application->loan()->save($loan);
        $user->loans()->save($loan);

        return $this->successResponse($loan->to_public_output());
    }

    public function createPayout(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required'
        ]);

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT'),
                env('PAYPAL_SECRET')
            )
        );

        $loanId = $request->input('loan_id');
        $loan = Loan::find($loanId);
        $user = $loan->user;
        $total = $loan->principal;

        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("Escotilla Loan received!");

        $amount = new Currency();
        $amount->setCurrency('USD')
            ->setValue($total);

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote('Your loan is here!')
            ->setReceiver($user->email)
            ->setSenderItemId(uniqid())
            ->setAmount($amount);

        $payout = new Payout();

        $payout->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $output = $payout->create(null, $apiContext);
        } catch (Exception $ex) {
            $this->errorResponse($ex->getMessage(), $ex->getCode());
        }

        $loan->status = 'funded';
        $loan->save();

        return $this->successResponse('Congrats, you\'ve been funded!');
    }

    public function getConsent()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT'),
                env('PAYPAL_SECRET')
            )
        );

        $redirectUrl = OpenIdSession::getAuthorizationUrl(
            'localhost:8000',
            [
                'openid',
                'profile',
                'address',
                'email',
                'phone',
                'https://uri.paypal.com/services/paypalattributes',
                'https://uri.paypal.com/services/expresscheckout',
                'https://uri.paypal.com/services/invoicing'
            ],
            null,
            null,
            null,
            $apiContext
        );

        return $this->successResponse($redirectUrl);
    }
}