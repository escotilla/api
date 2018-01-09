<?php

namespace App\Http\Controllers;

use App\Question;

class QuestionController extends EscotillaController
{
    public function read()
    {
        $questions = Question::all();

        return $this->successResponse($questions->mapWithKeys(function($question) {
            return [$question->_id => $question->to_public_output()];
        }));
    }
}