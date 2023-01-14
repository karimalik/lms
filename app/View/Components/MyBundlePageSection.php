<?php

namespace App\View\Components;


use Illuminate\View\Component;
use Modules\BundleSubscription\Entities\BundleCoursePlan;
use Modules\CourseSetting\Entities\CourseEnrolled;

class MyBundlePageSection extends Component
{

    public function render()
    {
        $bundle_ids = [];
        $enrolls = CourseEnrolled::select('bundle_course_id')->distinct()->get();
        foreach ($enrolls as $bundle) {
            if ($bundle->bundle_course_id != 0) {
                $bundle_ids[] = $bundle->bundle_course_id;
            }
        }
        $BundleCourse = BundleCoursePlan::where('status', 1)->whereIn('id', $bundle_ids)->orderBy('order', 'asc')->with('reviews', 'course')->get();

        return view(theme('components.my-bundle-page-section'), compact('BundleCourse'));
    }
}
