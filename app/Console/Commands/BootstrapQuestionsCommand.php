<?php
namespace App\Console\Commands;

use App\Question;
use Illuminate\Console\Command;
use MongoDB\BSON\ObjectID;

class BootstrapQuestionsCommand extends Command
{
    protected $signature = 'bootstrap:questions';

    protected $description = 'Bootstraps questions database with questions';

    public function handle() {
        $path = 'config/questions.json';
        $question_array = json_decode(file_get_contents($path), true);

        foreach ($question_array as $question) {

            if (!isset($question['question_id'])) {
                $newQuestion = new Question($question);
                $newQuestion->save();
                $this->output->writeln('Saved new question: ' . $newQuestion->short_question);
            } elseif (is_null(Question::find($question['question_id']))) {
                $newQuestion = new Question($question);
                $newQuestion->_id = new ObjectID($question['question_id']);
                $newQuestion->save();
                $this->output->writeln('Saved question: ' . $newQuestion->short_question);
            }
        }

        $all = Question::all();

        $qs = [];
        foreach ($all as $q) {
            $qs[] = $q->to_public_output();
        }

        if (file_put_contents($path, json_encode($qs, JSON_PRETTY_PRINT))) {
            $this->output->writeln('Bootstrap question success');
        };
    }
}