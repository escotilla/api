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
        $application = ChecklistFactory::createChecklist($application, [
            'review_profile',
            'upload_documents',
            'sign_agreement'
        ]);
        $application->name = $user->name;
        $application->email = $user->email;
        $application->save();

        foreach ($payload as $question_id => $value) {
            self::saveAnswerToApplication($value, $question_id, $application);
        }

        self::saveAnswerToApplication($user->name, Question::FULL_NAME, $application);
        self::saveAnswerToApplication($user->email, Question::EMAIL, $application);

        $application->save();
        $user->applications()->save($application);
        $user->save();

        return $application;
    }

    /**
     * @param $value
     * @param $question_id
     * @param $application
     */
    public static function saveAnswerToApplication($value, $question_id, $application)
    {
        $answer = new Answer(['answer' => $value]);
        $question = Question::find($question_id);

        if (isset($question)) {
            $answer->question()->associate($question);
            $answer->application()->associate($application);
            $answer->save();

            $application->answers()->associate($answer);
        }
    }
}