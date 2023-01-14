<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\CourseEnrolled;

class MyCoursesPageSection extends Component
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }


    public function render()
    {
        if (routeIs('myClasses')) {
            $type = 3;
        } elseif (routeIs('myQuizzes')) {
            $type = 2;
        } elseif (routeIs('myCourses')) {
            $type = 1;
        } else {
            $type = 4;
        }

        $with = ['course', 'course.activeReviews', 'course.courseLevel', 'course.BookmarkUsers', 'course.user', 'course.reviews', 'course.enrollUsers'];

        if ($type == 1) {
            $with[] = 'course.completeLessons';
            $with[] = 'course.lessons';
        } elseif ($type == 2) {
            $with[] = 'course.quiz';
            $with[] = 'course.quiz.assign';
        } elseif ($type == 3) {
            $with[] = 'course.class';
            $with[] = 'course.class.zoomMeetings';
            if (isModuleActive('BBB')) {
                $with[] = 'course.class.bbbMeetings';
            }
            if (isModuleActive('Jisti')) {
                $with[] = 'course.class.jitsiMeetings';
            }
        }
        $per_page=15;
        if ($this->request->category) {
            $category_id = $this->request->category;
            $courses = CourseEnrolled::where('user_id', Auth::user()->id)
                ->whereHas('course', function ($query) use ($category_id, $type) {
                    $query->where('type', '=', $type);
                    $query->where('category_id', '=', $category_id);
                })
                ->with($with)->paginate($per_page);
        } else {
            $category_id = '';
            $courses = CourseEnrolled::where('user_id', Auth::user()->id)
                ->whereHas('course', function ($query) use ($type) {
                    $query->where('type', '=', $type);
                })
                ->with($with)->paginate($per_page);
        }

        if ($this->request->search) {
            $search = $this->request->search;
            $courses = CourseEnrolled::where('user_id', Auth::user()->id)
                ->whereHas('course', function ($query) use ($search, $type) {
                    $query->where('type', '=', $type);
                    $query->where('title', 'LIKE', '%' . $search . '%');
                })->latest()
                ->with($with)->paginate($per_page);


        } else {
            $search = '';
        }

        $categories = Category::where('status', 1)->with('activeSubcategories')->get();
        return view(theme('components.my-courses-page-section'), compact('category_id', 'search', 'courses', 'categories'));
    }
}
