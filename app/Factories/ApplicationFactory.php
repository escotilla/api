<?php

namespace App\Factories;

use App\Answer;
use App\Application;
use App\Business;
use App\ChecklistItem;
use App\Question;
use App\User;

class ApplicationFactory
{
    public static function fromPayload(User $user, $payload)
    {
        $application = new Application(['status' => 'pending_completion']);
        $application = ChecklistFactory::createChecklist($application, ['review_profile', 'upload_documents', 'sign_agreement']);
        $application->save();

        foreach ($payload as $question_id => $value) {
            $answer = new Answer(['answer' => $value]);
            $question = Question::find($question_id);

            if (isset($question)) {
                $answer->question()->associate($question);
                $answer->application()->associate($application);
                $answer->save();

                $application->answers()->associate($answer);
                $application->save();

                $user->applications()->associate($application);
                $user->save();
            }
        }

        return $application;
    }

}