<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Quiz\Entities\QuizeSetup;
use Modules\Quiz\Entities\QuizTestDetails;

class QuizResultPreviewPageSection extends Component
{
    public $quizTest, $user, $course;

    public function __construct($quizTest, $user, $course)
    {
        $this->quizTest = $quizTest;
        $this->user = $user;
        $this->course = $course;
    }


    public function render()
    {
        $quizSetup = QuizeSetup::getData();

        $quiz = OnlineQuiz::with('assign.questionBank', 'assign.questionBank.questionMu')->findOrFail($this->quizTest->quiz_id);
        $questions = [];
        foreach (@$quiz->assign as $key => $assign) {
            $questions[$key]['qus'] = $assign->questionBank->question;
            $questions[$key]['type'] = $assign->questionBank->type;

            $test = QuizTestDetails::where('quiz_test_id', $this->quizTest->id)->where('qus_id', $assign->questionBank->id)->first();
            $questions[$key]['isSubmit'] = false;
            $questions[$key]['isWrong'] = false;

            if ($assign->questionBank->type != "M") {

                if ($test) {
                    $questions[$key]['isSubmit'] = true;
                    if ($test->status == 0) {
                        $questions[$key]['isWrong'] = true;
                    }
                    $questions[$key]['answer'] = $test->answer;
                }
            } else {
                foreach (@$assign->questionBank->questionMuInSerial as $key2 => $option) {
                    $questions[$key]['option'][$key2]['title'] = $option->title;
                    $questions[$key]['option'][$key2]['right'] = $option->status == 1 ? true : false;
                }

                if ($test) {
                    $questions[$key]['isSubmit'] = true;
                    if ($test->status == 0) {
                        $questions[$key]['option'][$key2]['wrong'] = $test->status == 0 ? true : false;
                        $questions[$key]['isWrong'] = true;
                    }
                }
            }

            if (!$questions[$key]['isSubmit']){
                $questions[$key]['isWrong'] = true;
            }

        }

        return view(theme('components.quiz-result-preview-page-section'), compact('questions', 'quizSetup'));
    }
}
