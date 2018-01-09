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

        if (is_null($user)) {
            return $this->errorResponse('User not found', 404);
        }

        $payload = $request->get('payload');

        $application = ApplicationFactory::fromPayload($user, $payload);

        $this->updateUserFromPayload($user, $payload);

        $user->save();

        return $this->successResponse($user->to_application_output());
    }

    public function get(Request $request) {
        $this->validate($request, [
            'api_token' => 'required'
        ]);

        $user = Auth::user();

        if (is_null($user)) {
            $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user->to_application_output());
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