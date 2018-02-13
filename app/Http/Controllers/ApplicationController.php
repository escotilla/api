<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Application;
use App\Factories\ApplicationFactory;
use App\Factories\UserFactory;
use App\Question;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends EscotillaController
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required',
            'payload' => 'required'
        ]);

        $user = Auth::user();

        $payload = $request->get('payload');

        $application = ApplicationFactory::fromPayload($user, $payload);

        $this->updateUserFromPayload($user, $payload);

        $user->save();

        return $this->successResponse($user->to_auth_output());
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required',
            'payload' => 'required',
            'application_id' => 'required'
        ]);

        $user = Auth::user();
        $application = Application::find($request->get('application_id'));
        $payload = $request->get('payload');

        if (is_null($application)) {
            return $this->errorResponse('Application not found', Response::HTTP_NOT_FOUND);
        }

        foreach ($payload as $question_id => $value) {
            if ($question_id === Question::INFO_ACCURATE) {
                $application->updateChecklist('review_profile', 'complete');
            }

            if ($question_id === Question::AGREE_LOAN_CONTRACT) {
                $application->updateChecklist('sign_agreement', 'complete');
            }

            $savedAnswer = $application
                ->answers()
                ->first(function($answer) use ($question_id) {
                return $answer->question->id == $question_id;
            });

            if (isset($savedAnswer)) {
                if ($savedAnswer->answer === $value) {
                    continue;
                }

                $application->answers()->destroy($savedAnswer);
            }

            $newAnswer = new Answer(['answer' => $value]);
            $question = Question::find($question_id);

            if (isset($question)) {
                $newAnswer->question()->associate($question);
                $newAnswer->application()->associate($application);
                $newAnswer->save();

                $application->answers()->associate($newAnswer);
            }
        }

        $application->save();

        return $this->successResponse($user->to_auth_output());
    }

    public function get(Request $request) {
        $this->validate($request, [
            'api_token' => 'required'
        ]);

        $user = Auth::user();

        if (is_null($user)) {
            $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user->to_auth_output());
    }
    /**
     * @param $user
     * @param $payload
     */
    public function updateUserFromPayload($user, $payload)
    {
        $business = $user->business;

        foreach ($payload as $question_id => $answer) {
            switch ($question_id) {
                case Question::BUSINESS_DESCRIPTION:
                    $business->description = $answer;
                    break;
                case Question::BUSINESS_PRODUCT:
                    $business->product = $answer;
                    break;
                case Question::DOB:
                    $user->dob = $answer;

            }
        }

        $user->business()->associate($business);
    }
}