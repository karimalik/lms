<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseLevel;
use Modules\Localization\Entities\Language;

class QuizPageSection extends Component
{
    public $request, $categories, $languages;

    public function __construct($request, $categories, $languages)
    {
        $this->request = $request;
        $this->categories = $categories;
        $this->languages = $languages;
    }


    public function render()
    {
        $query = Course::orderBy('total_enrolled', 'desc')->with('enrollUsers','cartUsers','quiz', 'quiz.assign','user','reviews','courseLevel','BookmarkUsers')->where('scope', 1);


        $type = $this->request->type;
        if (empty($type)) {
            $type = '';
        } else {
            $types = explode(',', $type);
            if (count($types) == 1) {
                foreach ($types as $t) {
                    if ($t == 'free') {
                        $query->where('price', 0);
                    } elseif ($t == 'paid') {
                        $query = $query->where('price', '>', 0);
                    }
                }
            }
        }

        $language = $this->request->language;
        if (empty($language)) {
            $language = '';
        } else {
            $row_languages = explode(',', $language);
            $languages = [];
            $LanguageList = Language::whereIn('code', $row_languages)->first();
            foreach ($row_languages as $l) {
                $lang = $LanguageList->where('code', $l)->first();
                if ($lang) {
                    $languages[] = $lang->id;
                }
            }
            $query->whereIn('lang_id', $languages);
        }


        $level = $this->request->level;
        if (empty($level)) {
            $level = '';
        } else {
            $levels = explode(',', $level);
            $query->whereIn('level', $levels);
        }

        $mode = $this->request->mode;
        if (empty($mode)) {
            $mode = '';
        } else {
            $modes = explode(',', $mode);
            $query->whereIn('mode_of_delivery', $modes);
        }

        $category = $this->request->category;
        if (empty($category)) {
            $category = '';
        } else {
            $categories = explode(',', $category);

            $query->whereHas('quiz', function ($q) use ($categories) {
                $q->whereIn('category_id', $categories);
            });

        }
        $subCategory = $this->request->get('sub-category');
        if (!empty($subCategory)) {

            $query->whereHas('quiz', function ($q) use ($subCategory) {
                $q->where('sub_category_id', $subCategory);
            });

        }

        $query->where('type', 2)->where('status', 1);

        $order = $this->request->order;
        if (empty($order)) {
            $order = '';
        } else {
            if ($order == "price") {
                $query->orderBy('price', 'asc');
            } else {
                $query->latest();
            }
        }

        $courses = $query->paginate(itemsGridSize());
        $total = $courses->total();
        $levels = CourseLevel::select('id', 'title')->where('status', 1)->get();
        return view(theme('components.quiz-page-section'), compact('levels', 'order', 'category', 'level', 'order', 'language', 'type', 'total', 'courses','mode'));
    }
}
