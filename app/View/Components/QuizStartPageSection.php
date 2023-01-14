<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Quiz\Entities\QuizeSetup;
use Modules\Quiz\Entities\QuizTest;

class QuizStartPageSection extends Component
{
    public $course, $quiz_id;

    public function __construct($course, $quizId)
    {
        $this->course = $course;
        $this->quiz_id = $quizId;
    }


    public function render()
    {
        $quiz = OnlineQuiz::where('id', $this->quiz_id)->with('assign.questionBank', 'assign.questionBank.questionMu')->first();
        $quizSetup = QuizeSetup::getData();

        $alreadyJoin = 0;

        $givenQuiz = QuizTest::where('user_id', Auth::id())->where('course_id', $this->course->id)->where('quiz_id', $this->course->quiz->id)->count();
        if ($givenQuiz != 0) {
            $alreadyJoin = 1;
        }
        return view(theme('components.quiz-start-page-section'), compact('alreadyJoin', 'quiz', 'quizSetup',));
    }
}
