<?php

namespace Modules\FrontendManage\Http\Controllers;

use Exception;
use Throwable;
use App\AboutPage;
use App\Http\Controllers\Controller;
use App\Traits\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use Modules\FrontendManage\Entities\FrontPage;
use Modules\SystemSetting\Entities\SocialLink;
use Modules\FrontendManage\Entities\HomeSlider;
use Modules\SystemSetting\Entities\Testimonial;
use Modules\FrontendManage\Entities\HomeContent;
use Modules\FrontendManage\Entities\CourseSetting;
use Modules\FrontendManage\Entities\PrivacyPolicy;
use Modules\FrontendManage\Entities\TopbarSetting;
use Modules\SystemSetting\Entities\FrontendSetting;

class FrontendManageController extends Controller
{
    use ImageStore;

    public function index()
    {
        return 'Frontend Manage';
    }


    // HomeContent
    public function HomeContent()
    {
        try {
            $home_content = app('getHomeContent');
            $pages = FrontPage::where('status', 1)->get();
            $blocks = DB::table('homepage_block_positions')->orderBy('order', 'asc')->get();
            return view('frontendmanage::home_content', compact('home_content', 'pages', 'blocks'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function HomeContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {

            if ($request->instructor_banner != null) {
                UpdateHomeContent('instructor_banner', $this->saveImage($request->instructor_banner));
            }
            if ($request->best_category_banner != null) {
                UpdateHomeContent('best_category_banner', $this->saveImage($request->best_category_banner));
            }

            if ($request->how_to_buy_logo1 != null) {
                UpdateHomeContent('how_to_buy_logo1', $this->saveImage($request->how_to_buy_logo1));
            }

            if ($request->how_to_buy_logo2 != null) {
                UpdateHomeContent('how_to_buy_logo2', $this->saveImage($request->how_to_buy_logo2));
            }

            if ($request->how_to_buy_logo3 != null) {
                UpdateHomeContent('how_to_buy_logo3', $this->saveImage($request->how_to_buy_logo3));
            }

            if ($request->how_to_buy_logo4 != null) {
                UpdateHomeContent('how_to_buy_logo4', $this->saveImage($request->how_to_buy_logo4));
            }
            if ($request->subscribe_logo != null) {
                UpdateHomeContent('subscribe_logo', $this->saveImage($request->subscribe_logo));
            }

            if ($request->become_instructor_logo != null) {
                UpdateHomeContent('become_instructor_logo', $this->saveImage($request->become_instructor_logo));
            }

            if ($request->slider_banner != null) {
                UpdateHomeContent('slider_banner', $this->saveImage($request->slider_banner));
            }


            if ($request->key_feature_logo1 != null) {
                UpdateHomeContent('key_feature_logo1', $this->saveImage($request->key_feature_logo1));
            }

            if ($request->key_feature_logo2 != null) {
                UpdateHomeContent('key_feature_logo2', $this->saveImage($request->key_feature_logo2));
            }

            if ($request->key_feature_logo3 != null) {
                UpdateHomeContent('key_feature_logo3', $this->saveImage($request->key_feature_logo3));
            }

            if ($request->banner_logo != null) {
                UpdateHomeContent('banner_logo', $this->saveImage($request->banner_logo));
            }


            $items = $request->except([
                'instructor_banner', 'best_category_banner',
                'how_to_buy_logo1', 'how_to_buy_logo2',
                'how_to_buy_logo3', 'how_to_buy_logo4',
                'subscribe_logo', 'key_feature_logo1',
                'key_feature_logo2', 'key_feature_logo3',
                'banner_logo', '_token', 'url', 'id',
                'become_instructor_logo', 'slider_banner'
            ]);

            foreach ($items as $key => $item) {
                UpdateHomeContent($key, $item);
            }
            UpdateHomeContent('show_menu_search_box', $request->show_menu_search_box == 1 ? 1 : 0);
            UpdateHomeContent('show_subscription_plan', $request->show_subscription_plan == 1 ? 1 : 0);
            UpdateHomeContent('show_banner_search_box', $request->show_banner_search_box == 1 ? 1 : 0);
            UpdateHomeContent('show_key_feature', $request->show_key_feature == 1 ? 1 : 0);
            UpdateHomeContent('show_banner_section', $request->show_banner_section == 1 ? 1 : 0);
            UpdateHomeContent('show_category_section', $request->show_category_section == 1 ? 1 : 0);
            UpdateHomeContent('show_testimonial_section', $request->show_testimonial_section == 1 ? 1 : 0);
            UpdateHomeContent('show_live_class_section', $request->show_live_class_section == 1 ? 1 : 0);
            UpdateHomeContent('show_instructor_section', $request->show_instructor_section == 1 ? 1 : 0);
            UpdateHomeContent('show_course_section', $request->show_course_section == 1 ? 1 : 0);
            UpdateHomeContent('show_best_category_section', $request->show_best_category_section == 1 ? 1 : 0);
            UpdateHomeContent('show_quiz_section', $request->show_quiz_section == 1 ? 1 : 0);
            UpdateHomeContent('show_article_section', $request->show_article_section == 1 ? 1 : 0);
            UpdateHomeContent('show_subscribe_section', $request->show_subscribe_section == 1 ? 1 : 0);
            UpdateHomeContent('show_become_instructor_section', $request->show_become_instructor_section == 1 ? 1 : 0);
            UpdateHomeContent('show_sponsor_section', $request->show_sponsor_section == 1 ? 1 : 0);
            UpdateHomeContent('show_how_to_buy', $request->show_how_to_buy == 1 ? 1 : 0);
            UpdateHomeContent('show_home_page_faq', $request->show_home_page_faq == 1 ? 1 : 0);

            GenerateHomeContent(SaasDomain());

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.homeContent');


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function PageContent()
    {
        try {
            $page_content = app('getHomeContent');
            return view('frontendmanage::page_content', compact('page_content'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function showTopBarSettings()
    {
        try {
            $data = TopbarSetting::getData();
            return view('frontendmanage::topbarSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function showCourseSettings()
    {
        try {
            $data = CourseSetting::getData();
            return view('frontendmanage::courseSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function saveCourseSettings(Request $request)
    {
        // return $request;
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            if (isset($request->show_enrolled_or_level_section) && !isset($request->enrolled_or_level)) {
                Toastr::warning(trans('frontendmanage.Required Data Not Selected'), trans('common.Failed'));
                return redirect()->back();
            }
            $data = CourseSetting::getData();
            $data->show_rating = $request->show_rating;
            $data->show_cart = $request->show_cart;
            $data->show_enrolled_or_level_section = $request->show_enrolled_or_level_section;
            $data->enrolled_or_level = $request->enrolled_or_level;
            $data->show_cql_left_sidebar = $request->show_cql_left_sidebar;
            $data->size_of_grid = $request->size_of_grid;
            $data->show_mode_of_delivery = $request->show_mode_of_delivery;

            $data->show_review_option = $request->show_review_option;
            $data->show_rating_option = $request->show_rating_option;
            $data->show_search_in_category = $request->show_search_in_category;

            $data->show_instructor_rating = $request->show_instructor_rating;
            $data->show_instructor_review = $request->show_instructor_review;
            $data->show_instructor_enrolled = $request->show_instructor_enrolled;
            $data->show_instructor_courses = $request->show_instructor_courses;
            $data->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('frontendmanage::courseSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function saveTopBarSettings(Request $request)
    {
        // return $request;
        if (demoCheck()) {
            return redirect()->back();

        }
        try {
            $data = TopbarSetting::getData();

            $data->left_side_text_show = $request->left_side_text_show;
            $data->left_side_logo = $request->left_side_logo;
            $data->left_side_text = $request->left_side_text;
            $data->left_side_text_link = $request->left_side_text_link;

            $data->right_side_text_1_show = $request->right_side_text_1_show;
            $data->reight_side_logo_1 = $request->reight_side_logo_1;
            $data->right_side_text_1 = $request->right_side_text_1;
            $data->right_side_text_1_link = $request->right_side_text_1_link;

            $data->right_side_text_2_show = $request->right_side_text_2_show;
            $data->reight_side_logo_2 = $request->reight_side_logo_2;
            $data->right_side_text_2 = $request->right_side_text_2;
            $data->right_side_text_2_link = $request->right_side_text_2_link;

            $data->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('frontendmanage::topbarSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function ContactPageContent()
    {
        try {
            $page_content = app('getHomeContent');;
            return view('frontendmanage::contact_page_content', compact('page_content'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function PageContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {
            if ($request->blog_page_banner != null) {
                UpdateHomeContent('blog_page_banner', $this->saveImage($request->blog_page_banner));
            }
            if ($request->about_page_banner != null) {
                UpdateHomeContent('about_page_banner', $this->saveImage($request->about_page_banner));
            }
            if ($request->instructor_page_banner != null) {
                UpdateHomeContent('instructor_page_banner', $this->saveImage($request->instructor_page_banner));
            }
            if ($request->become_instructor_page_banner != null) {
                UpdateHomeContent('become_instructor_page_banner', $this->saveImage($request->become_instructor_page_banner));
            }
            if ($request->quiz_page_banner != null) {
                UpdateHomeContent('quiz_page_banner', $this->saveImage($request->quiz_page_banner));
            }
            if ($request->class_page_banner != null) {
                UpdateHomeContent('class_page_banner', $this->saveImage($request->class_page_banner));
            }
            if ($request->course_page_banner != null) {
                UpdateHomeContent('course_page_banner', $this->saveImage($request->course_page_banner));
            }
            if ($request->saas_banner != null) {
                UpdateHomeContent('saas_banner', $this->saveImage($request->saas_banner));
            }

            if (isModuleActive('Subscription')) {
                UpdateHomeContent('subscription_page_title', $request->subscription_page_title);
                UpdateHomeContent('subscription_page_sub_title', $request->subscription_page_sub_title);
                if ($request->subscription_page_banner != null) {
                    UpdateHomeContent('subscription_page_banner', $this->saveImage($request->subscription_page_banner));
                }
            }


            $items = $request->except([
                'blog_page_banner', 'about_page_banner',
                'instructor_page_banner', 'become_instructor_page_banner',
                'quiz_page_banner', 'class_page_banner',
                'course_page_banner', 'subscription_page_banner', 'saas_banner'
            ]);

            foreach ($items as $key => $item) {
                UpdateHomeContent($key, $item);
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.pageContent');


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function ContactPageContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {


            $items = $request->except([
                'show_map', 'contact_page_body_image',
                'contact_page_banner', 'contact_page_phone',
                'contact_page_email', 'contact_page_address'
            ]);

            foreach ($items as $key => $item) {
                UpdateHomeContent($key, $item);
            }

            UpdateHomeContent('show_map', $request->show_map == 1 ? 1 : 0);


            if ($request->contact_page_body_image != null) {
                UpdateHomeContent('contact_page_body_image', $this->saveImage($request->contact_page_body_image));
            }
            if ($request->contact_page_banner != null) {
                UpdateHomeContent('contact_page_banner', $this->saveImage($request->contact_page_banner));
            }
            if ($request->contact_page_phone != null) {
                UpdateHomeContent('contact_page_phone', $this->saveImage($request->contact_page_phone));
            }
            if ($request->contact_page_email != null) {
                UpdateHomeContent('contact_page_email', $this->saveImage($request->contact_page_email));
            }
            if ($request->contact_page_address != null) {
                UpdateHomeContent('contact_page_address', $this->saveImage($request->contact_page_address));
            }


            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function PrivacyPolicy()
    {
        try {
            $privacy_policy = PrivacyPolicy::first();
            return view('frontendmanage::privacy_policy', compact('privacy_policy'));
        } catch (Throwable $th) {
            $errorMessage = $th->getMessage();
            Log::error($errorMessage);
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function PrivacyPolicyUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;

        try {
            $privacy_policy = PrivacyPolicy::find($request->id);
            $privacy_policy->details = $request->details;
            $privacy_policy->page_banner_title = $request->page_banner_title;
            $privacy_policy->page_banner_sub_title = $request->page_banner_sub_title;
            $privacy_policy->page_banner_status = $request->page_banner_status;


            if ($request->page_banner != null) {

                if ($request->file('page_banner')->extension() == "svg") {
                    $file = $request->file('page_banner');
                    $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName;
                    $file->move(public_path('uploads/settings'), $fileName);
                } else {
                    $url1 = $this->saveImage($request->page_banner);
                }
                $privacy_policy->page_banner = $url1;
            }

            $privacy_policy->save();
            if ($privacy_policy) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('frontend.privacy_policy');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function testimonials()
    {
        try {
            $data['testimonials'] = Testimonial::latest()->get();
            return view('frontendmanage::testimonials', compact('data'));
        } catch (Throwable $th) {

            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function testimonials_store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'body' => 'required',
            'author' => 'required|max:255',
            'profession' => 'required|max:255',
            'image' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $testimonial = new Testimonial();
            $testimonial->body = $request->body;
            $testimonial->star = $request->star;
            $testimonial->author = $request->author;
            $testimonial->profession = $request->profession;

            $image = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $image = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/testimonial/', $image);
                $image = 'public/uploads/testimonial/' . $image;
                $testimonial->image = $image;
            }

            $testimonial->status = $request->status;
            $testimonial->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'body' => 'required',
            'author' => 'required|max:255',
            'profession' => 'required|max:255',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            $testimonial = Testimonial::find($request->id);
            $testimonial->body = $request->body;
            $testimonial->author = $request->author;
            $testimonial->profession = $request->profession;
            $testimonial->star = $request->star;

            $image = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $image = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/testimonial/', $image);
                $image = 'public/uploads/testimonial/' . $image;
                $testimonial->image = $image;
            }

            $testimonial->status = $request->status;
            $testimonial->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_edit($id)
    {
        try {
            $data['testimonials'] = Testimonial::all();
            $edit = Testimonial::find($id);
            return view('frontendmanage::testimonials', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_delete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $testimonial = Testimonial::find($id);
            $testimonial->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function sectionSetting()
    {
        try {
            $data['frontends'] = FrontendSetting::whereNotIn('id', [1, 2])->latest()->get();
            return view('frontendmanage::sectionSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function sectionSettingEdit($id)
    {
        try {
            $edit = FrontendSetting::find($id);
            $data['frontends'] = FrontendSetting::whereNotIn('id', [1, 2])->latest()->get();

            return view('frontendmanage::sectionSetting', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function sectionSetting_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $frontend = FrontendSetting::find($request->id);
            $frontend->title = $request->title;
            $frontend->description = $request->description;
            $frontend->btn_name = $request->btn_name;
            $frontend->btn_link = $request->btn_link;
            $frontend->url = $request->url;
            if ($request->icon) {
                $frontend->icon = $request->icon;
            }
            $frontend->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.sectionSetting');

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSetting()
    {
        try {
            $data['social_links'] = SocialLink::latest()->get();
            return view('frontendmanage::socialSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSettingEdit($id)
    {
        try {
            $data['social_links'] = SocialLink::latest()->get();
            $edit = SocialLink::find($id);
            return view('frontendmanage::socialSetting', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSettingDelete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {

            $delete = SocialLink::find($id)->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect('frontend/social-setting');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function AboutPage()
    {
        $about = AboutPage::getData();
        return view('frontendmanage::about', compact('about'));
    }

    public function saveAboutPage(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $about = AboutPage::first();
            $about->who_we_are = $request->who_we_are;
            $about->banner_title = $request->banner_title;
            $about->story_title = $request->story_title;
            $about->story_description = $request->story_description;
            $about->teacher_title = $request->teacher_title;
            $about->teacher_details = $request->teacher_details;
            $about->course_title = $request->course_title;
            $about->course_details = $request->course_details;
            $about->student_title = $request->student_title;
            $about->student_details = $request->student_details;

            $about->total_student = $request->total_student;
            $about->total_teacher = $request->total_teacher;
            $about->total_courses = $request->total_courses;

            $about->show_testimonial = $request->show_testimonial;
            $about->show_brand = $request->show_brand;
            $about->show_become_instructor = $request->show_become_instructor;


            $about->about_page_content_title = $request->about_page_content_title;
            $about->about_page_content_details = $request->about_page_content_details;
            $about->live_class_title = $request->live_class_title;
            $about->live_class_details = $request->live_class_details;


            $about->sponsor_title = $request->sponsor_title;
            $about->sponsor_sub_title = $request->sponsor_sub_title;

            if ($request->image1 != null) {

                if ($request->file('image1')->extension() == "svg") {
                    $file1 = $request->file('image1');
                    $fileName1 = md5(rand(0, 9999) . '_' . time()) . '.' . $file1->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName1;
                    $file1->move(public_path('uploads/settings'), $fileName1);

                } else {
                    $url1 = $this->saveImage($request->image1);
                }

                $about->image1 = $url1;
            }

            if ($request->image2 != null) {

                if ($request->file('image2')->extension() == "svg") {
                    $file2 = $request->file('image2');
                    $fileName2 = md5(rand(0, 9999) . '_' . time()) . '.' . $file2->clientExtension();
                    $url2 = 'public/uploads/settings/' . $fileName2;
                    $file2->move(public_path('uploads/settings'), $fileName2);

                } else {
                    $url2 = $this->saveImage($request->image2);
                }

                $about->image2 = $url2;
            }


            if ($request->image3 != null) {

                if ($request->file('image3')->extension() == "svg") {
                    $file3 = $request->file('image3');
                    $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                    $url3 = 'public/uploads/settings/' . $fileName3;
                    $file3->move(public_path('uploads/settings'), $fileName3);

                } else {
                    $url3 = $this->saveImage($request->image3);
                }

                $about->image3 = $url3;
            }

            if ($request->image4 != null) {

                if ($request->file('image4')->extension() == "svg") {
                    $file4 = $request->file('image4');
                    $fileName4 = md5(rand(0, 9999) . '_' . time()) . '.' . $file4->clientExtension();
                    $url4 = 'public/uploads/settings/' . $fileName4;
                    $file4->move(public_path('uploads/settings'), $fileName4);

                } else {
                    $url4 = $this->saveImage($request->image4);
                }

                $about->image4 = $url4;
            }

            if ($request->live_class_image != null) {

                $url5 = $this->saveImage($request->live_class_image);

                $about->live_class_image = $url5;
            }

            if ($request->counter_bg != null) {

                $url5 = $this->saveImage($request->counter_bg);

                $about->counter_bg = $url5;
            }

            $about->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function socialSettingSave(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;

        $rules = [
            'icon' => 'required',
            'name' => 'required',
            'btn_link' => 'required',
            'status' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));


        try {
            $social_link = new SocialLink();
            $social_link->icon = $request->icon;
            $social_link->name = $request->name;
            $social_link->link = $request->btn_link;
            $social_link->status = $request->status;
            $social_link->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function socialSettingUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = ['id' => 'required',
            'name' => 'required',
            'icon' => 'required',
            'btn_link' => 'required',
            'status' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            $social_link = SocialLink::find($request->id);
            $social_link->icon = $request->icon;
            $social_link->name = $request->name;
            $social_link->link = $request->btn_link;
            $social_link->status = $request->status;
            $social_link->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect('frontend/social-setting');

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function changeHomePageBlockOrder(Request $request)
    {
        $ids = $request->get('ids');

        foreach ($ids as $index => $id) {
            DB::table('homepage_block_positions')->where('id', $id)->limit(1)->update(['order' => $index]);
        }
        $homepage_block_positions = DB::table('homepage_block_positions')->orderBy('order', 'asc')->get();
        UpdateHomeContent('homepage_block_positions', json_encode($homepage_block_positions));
        return true;
    }
}
