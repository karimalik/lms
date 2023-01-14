<?php

namespace Modules\CourseSetting\Http\Controllers;

use App\LessonComplete;
use DB;
use App\User;
use Exception;
use App\Traits\Filepond;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\BundleSubscription\Entities\BundleCourse;
use Modules\BundleSubscription\Entities\BundleCoursePlan;

use Modules\Org\Entities\OrgBranch;
use Modules\Org\Entities\OrgMaterial;
use Modules\Quiz\Entities\OnlineExamQuestionAssign;
use Vimeo\Vimeo;
use Yajra\DataTables\DataTables;
use Modules\Payment\Entities\Cart;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Modules\Quiz\Entities\OnlineQuiz;
use Illuminate\Support\Facades\Session;
use Modules\Quiz\Entities\QuestionBank;
use Modules\Quiz\Entities\QuestionGroup;
use Modules\Quiz\Entities\QuestionLevel;
use Modules\Setting\Model\GeneralSetting;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\Lesson;
use Modules\CourseSetting\Entities\Chapter;
use Modules\Localization\Entities\Language;
use Modules\CourseSetting\Entities\Category;
use Modules\Certificate\Entities\Certificate;
use Modules\CourseSetting\Entities\CourseLevel;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\CourseExercise;
use Modules\AmazonS3\Http\Controllers\AmazonS3Controller;
use Modules\Newsletter\Http\Controllers\AcelleController;
use Modules\Newsletter\Http\Controllers\MailchimpController;
use Modules\Newsletter\Http\Controllers\GetResponseController;


class CourseSettingController extends Controller
{
    use Filepond;

    public function getSubscriptionList()
    {
        $list = [];

        try {
            $user = Auth::user();
            if ($user->subscription_method == "Mailchimp" && $user->subscription_api_status == 1) {
                $mailchimp = new MailchimpController();
                $mailchimp->mailchimp($user->subscription_api_key);
                $getlists = $mailchimp->mailchimpLists();
                foreach ($getlists as $key => $l) {
                    $list[$key]['name'] = $l['name'];
                    $list[$key]['id'] = $l['id'];
                }

            } elseif ($user->subscription_method == "GetResponse" && $user->subscription_api_status == 1) {
                $getResponse = new GetResponseController();
                $getResponse->getResponseApi($user->subscription_api_key);
                $getlists = $getResponse->getResponseLists();
                foreach ($getlists as $key => $l) {
                    $list[$key]['name'] = $l->name;
                    $list[$key]['id'] = $l->campaignId;
                }
            } elseif ($user->subscription_method == "Acelle" && $user->subscription_api_status == 1) {
                $acelleController = new AcelleController();

                $acelleController->getAcelleApiResponse();
                $getlists = $acelleController->getAcelleList();
                foreach ($getlists as $key => $l) {
                    $list[$key]['name'] = $l['name'];
                    $list[$key]['id'] = $l['uid'];


                }
            }
        } catch (\Exception $exception) {

        }
        return $list;

    }


    public function ajaxGetCourseSubCategory(Request $request)
    {
        try {
            $sub_categories = Category::where('parent_id', '=', $request->id)->get();
            return response()->json([$sub_categories]);
        } catch (Exception $e) {
            return response()->json("", 404);
        }
    }

    public function courseSortByCat($id)
    {
        try {
            if (!empty($id))
                $courses = Course::whereHas('enrolls')
                    ->where('category_id', $id)->with('user', 'category', 'subCategory', 'enrolls', 'comments', 'reviews', 'lessons')->paginate(15);
            else
                $courses = Course::whereHas('enrolls')->with('user', 'category', 'subCategory', 'enrolls', 'comments', 'reviews', 'lessons')->paginate(15);

            return response()->json([
                'courses' => $courses
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);
        }
    }


    public function getAllCourse()
    {
        try {
            $user = Auth::user();

            $video_list = $this->getVimeoList();
            $vdocipher_list = $this->getVdoCipherList();

            $courses = [];
            $categories = Category::get();
            if ($user->role_id == 1) {
                $quizzes = OnlineQuiz::latest()->get();
            } else {
                $quizzes = OnlineQuiz::where('created_by', $user->id)->latest()->get();
            }

            $instructors = User::whereIn('role_id', [1, 2])->get();
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();
            $levels = CourseLevel::where('status', 1)->get();
            $title = trans('courses.All');

            $sub_lists = $this->getSubscriptionList();

            return view('coursesetting::courses', compact('sub_lists', 'levels', 'video_list', 'vdocipher_list', 'title', 'quizzes', 'courses', 'categories', 'languages', 'instructors'));
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function courseSortBy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $user = Auth::user();

            $video_list = $this->getVimeoList();
            $vdocipher_list = $this->getVdoCipherList();

            $categories = Category::get();
            $instructors = User::whereIn('role_id', [1, 2])->get();
            if ($user->role_id == 1) {
                $quizzes = OnlineQuiz::latest()->get();
            } else {
                $quizzes = OnlineQuiz::where('created_by', $user->id)->latest()->get();
            }
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();


            $courses = Course::query();
            // $courses->where('active_status', 1);
            if ($request->category != "") {
                $courses->where('category_id', $request->category);
            }
            if ($request->type != "") {
                $courses->where('type', $request->type);
            } else {
                $courses->whereIn('type', [1, 2]);
            }
            if ($request->instructor != "") {
                $courses->where('user_id', $request->instructor);
            }
            if ($request->status != "") {
                $courses->where('status', $request->status);
            }
            if (Route::current()->getName() == 'getActiveCourse') {
                $courses->where('status', 1);
            }
            if (Route::current()->getName() == 'getPendingCourse') {
                $courses->where('status', 0);
            }

            if ($request->category) {
                $category_search = $request->category;
            } else {
                $category_search = '';

            }

            if ($request->type) {
                $category_type = $request->type;
            } else {
                $category_type = '';

            }

            if ($request->instructor) {
                $category_instructor = $request->instructor;
            } else {
                $category_instructor = '';

            }

            if ($request->search_status) {
                $category_status = $request->search_status;
            } else {
                $category_status = '';

            }


            $courses = $courses->with('user', 'category', 'subCategory', 'enrolls', 'lessons')->orderBy('id', 'desc')->get();

            $levels = CourseLevel::where('status', 1)->get();
            $sub_lists = $this->getSubscriptionList();
            return view('coursesetting::courses', compact('sub_lists', 'levels', 'category_search', 'vdocipher_list', 'category_instructor', 'category_type', 'category_status', 'video_list', 'quizzes', 'courses', 'categories', 'languages', 'instructors'));

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function saveCourse(Request $request)
    {
        Session::flash('type', 'store');

        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'type' => 'required',
            'language' => 'required',
            'title' => 'required',
            'duration' => 'required',
            'image' => 'required|mimes:jpeg,bmp,png,jpg|max:1024',

        ];

        $this->validate($request, $rules, validationMessage($rules));


        if ($request->type == 1) {
            $rules = [
                'level' => 'required',
                // 'host' => 'required',
            ];
            $this->validate($request, $rules, validationMessage($rules));

            if (isset($request->show_overview_media)) {

                $rules = [
                    'host' => 'required',
                ];
                $this->validate($request, $rules, validationMessage($rules));

                if ($request->get('host') == "Vimeo") {
                    $rules = [
                        'vimeo' => 'required',
                    ];
                    $this->validate($request, $rules, validationMessage($rules));

                } elseif ($request->get('host') == "VdoCipher") {
                    $rules = [
                        'vdocipher' => 'required',
                    ];
                    $this->validate($request, $rules, validationMessage($rules));
                } elseif ($request->get('host') == "Youtube") {
                    $rules = [
                        'trailer_link' => 'required'
                    ];
                    $this->validate($request, $rules, validationMessage($rules));

                } else {


                }


            }

        }


        try {
            if (!empty($request->image)) {
                $course = new Course();
                $fileName = "";
                if ($request->hasFile('image')) {

                    $strpos = strpos($request->image, ';');
                    $sub = substr($request->image, 0, $strpos);
                    $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                    $img = Image::make($request->image);
//                    $img->resize(800, 500);
                    $upload_path = 'public/uploads/courses/';
                    $img->save($upload_path . $name);
                    $course->image = 'public/uploads/courses/' . $name;

                    $strpos = strpos($request->image, ';');
                    $sub = substr($request->image, 0, $strpos);
                    $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                    $img = Image::make($request->image);
//                    $img->resize(270, 181);
                    $img->resize(270, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $upload_path = 'public/uploads/courses/';
                    $img->save($upload_path . $name);
                    $course->thumbnail = 'public/uploads/courses/' . $name;
                }

                $course->user_id = Auth::id();
                if ($request->type == 1) {
                    $course->quiz_id = null;
                    $course->category_id = $request->category;
                    $course->subcategory_id = $request->sub_category;
                } elseif ($request->type == 2) {
                    $course->quiz_id = $request->quiz;
                    $course->category_id = null;
                    $course->subcategory_id = null;
                }


                $course->lang_id = $request->language;
                $course->scope = $request->scope;
                $course->title = $request->title;
                $course->slug = null;
                $course->duration = $request->duration;


                if ($request->is_discount == 1) {
                    $course->discount_price = $request->discount_price;
                } else {
                    $course->discount_price = null;
                }
                if ($request->is_free == 1) {
                    $course->price = 0;
                    $course->discount_price = null;
                } else {
                    $course->price = $request->price;
                }


                $course->publish = 1;
                $course->status = 0;
                $course->level = $request->level;

                $course->mode_of_delivery = $request->mode_of_delivery;

                $course->show_overview_media = $request->show_overview_media ? 1 : 0;
                $course->host = $request->host;
                $course->subscription_list = $request->subscription_list;

                if (!empty($request->assign_instructor)) {
                    $course->user_id = $request->assign_instructor;
                }

                if (!empty($request->assistant_instructors)) {
                    $assistants = $request->assistant_instructors;
                    if (($key = array_search($course->user_id, $assistants)) !== false) {
                        unset($assistants[$key]);
                    }
                    if (!empty($assistants)) {
                        $course->assistant_instructors = json_encode(array_values($assistants));
                    }
                }
                if ($request->get('host') == "Vimeo") {
                    if (config('vimeo.connections.main.upload_type') == "Direct") {
                        $course->trailer_link = $this->uploadFileIntoVimeo($request->title, $request->vimeo);
                    } else {
                        $course->trailer_link = $request->vimeo;
                    }

                } elseif ($request->get('host') == "VdoCipher") {
                    $course->trailer_link = $request->vdocipher;
                } elseif ($request->get('host') == "Youtube") {
                    $course->trailer_link = $request->trailer_link;
                } elseif ($request->get('host') == "Self") {
                    $course->trailer_link = $this->getPublicPathFromServerId($request->get('file'), 'local');


                } elseif ($request->get('host') == "AmazonS3") {

                    $course->trailer_link = $this->getPublicPathFromServerId($request->get('file'), 's3');

                } else {
                    $course->trailer_link = null;
                }


                if (!empty($request->assign_instructor)) {
                    $course->user_id = $request->assign_instructor;
                }


                if (!empty($request->assistant_instructors)) {
                    $assistants = $request->assistant_instructors;
                    if (($key = array_search($course->user_id, $assistants)) !== false) {
                        unset($assistants[$key]);
                    }
                    if (!empty($assistants)) {
                        $course->assistant_instructors = json_encode(array_values($assistants));
                    }
                }

                $course->meta_keywords = $request->meta_keywords;
                $course->meta_description = $request->meta_description;
                $course->about = $request->about;
                $course->requirements = $request->requirements;
                $course->outcomes = $request->outcomes;
                $course->type = $request->type;
                $course->drip = $request->drip;
                $course->complete_order = $request->complete_order;
                if (Settings('frontend_active_theme') == "edume") {
                    $course->what_learn1 = $request->what_learn1;
                    $course->what_learn2 = $request->what_learn2;
                }
                $course->save();
            }


            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->to(route('getAllCourse'));

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function uploadFileIntoVimeo($course_title, $file)
    {
        try {
            $response = $this->configVimeo()->upload($file, [
                'name' => $course_title,
                'privacy' => [
                    'view' => 'disable',
                    'embed' => 'whitelist'
                ],
                'embed' => [
                    'title' => [
                        'name' => 'hide',
                        'owner' => 'hide',
                    ]
                ]
            ]);
            $this->configVimeo()->request($response . '/privacy/domains/' . request()->getHttpHost(), [], 'PUT');
            return $response;
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), trans('common.Failed'));
            return null;
        }
    }

    public function AdminUpdateCourse(Request $request)
    {

        Session::flash('type', 'update');
        Session::flash('id', $request->id);

        if (demoCheck()) {
            return redirect()->back();
        }
        Session::flash('type', 'courseDetails');


        $rules = [
            'type' => 'required',
            'language' => 'required',
            'title' => 'required',
            'image' => 'nullable|mimes:jpeg,bmp,png,jpg|max:1024',

        ];
        $this->validate($request, $rules, validationMessage($rules));


        if ($request->type == 1) {
            $rules = [
                'duration' => 'required',
                'level' => 'required',
                // 'host' => 'required',
            ];
            $this->validate($request, $rules, validationMessage($rules));

            if (isset($request->show_overview_media)) {

                if ($request->get('host') == "Vimeo") {
                    $rules = [
                        'vimeo' => 'required',
                    ];
                    $this->validate($request, $rules, validationMessage($rules));

                } elseif ($request->get('host') == "VdoCipher") {
                    $rules = [
                        'vdocipher' => 'required',
                    ];
                    $this->validate($request, $rules, validationMessage($rules));

                } elseif ($request->get('host') == "Youtube") {
                    $rules = [
                        'trailer_link' => 'required'
                    ];
                    $this->validate($request, $rules, validationMessage($rules));

                } else {


                }
            }

        }


        try {

            $course = Course::find($request->id);
            $course->scope = $request->scope;
            if ($request->file('image') != "") {
                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
//                $img->resize(800, 500);
                $upload_path = 'public/uploads/courses/';
                $img->save($upload_path . $name);
                $course->image = 'public/uploads/courses/' . $name;

                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
//                $img->resize(270, 181);
                $img->resize(270, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $upload_path = 'public/uploads/courses/';
                $img->save($upload_path . $name);
                $course->thumbnail = 'public/uploads/courses/' . $name;
            }


            $course->user_id = Auth::id();

            if (!empty($request->assign_instructor)) {
                $course->user_id = $request->assign_instructor;
            }
            $course->drip = $request->drip;
            $course->complete_order = $request->complete_order;
            $course->lang_id = $request->language;
            $course->title = $request->title;
            $course->duration = $request->duration;
            $course->subscription_list = $request->subscription_list;


            if ($request->is_discount == 1) {
                $course->discount_price = $request->discount_price;
            } else {
                $course->discount_price = null;
            }
            if ($request->is_free == 1) {
                $course->price = 0;
                $course->discount_price = null;
            } else {
                $course->price = $request->price;
            }


            $course->level = $request->level;
            $course->mode_of_delivery = $request->mode_of_delivery;

            $course->show_overview_media = $request->show_overview_media ? 1 : 0;
            if ($request->get('host') == "Vimeo") {
                if (config('vimeo.connections.main.upload_type') == "Direct") {
                    $course->trailer_link = $this->uploadFileIntoVimeo($request->title, $request->vimeo);
                } else {
                    $course->trailer_link = $request->vimeo;
                }
            } elseif ($request->get('host') == "VdoCipher") {
                $course->trailer_link = $request->vdocipher;
            } elseif ($request->get('host') == "Youtube") {
                $course->trailer_link = $request->trailer_link;
            } elseif ($request->get('host') == "Self") {
                if ($request->get('file')) {
                    $course->trailer_link = $this->getPublicPathFromServerId($request->get('file'), 'local');

                }

            } elseif ($request->get('host') == "AmazonS3") {
                if ($request->get('file')) {

                    $course->trailer_link = $this->getPublicPathFromServerId($request->get('file'), 's3');
                }


            } else {
                $course->trailer_link = null;
            }
            $course->host = $request->host;
            $course->meta_keywords = $request->meta_keywords;
            $course->meta_description = $request->meta_description;
            $course->about = $request->about;
            $course->type = $request->type;
            $course->requirements = $request->requirements;
            $course->outcomes = $request->outcomes;
            if ($request->type == 1) {
                $course->quiz_id = null;
                $course->category_id = $request->category;
                $course->subcategory_id = $request->sub_category;
            } elseif ($request->type == 2) {
                $course->quiz_id = $request->quiz;
                $course->category_id = null;
                $course->subcategory_id = null;
            }

            if (Settings('frontend_active_theme') == "edume") {
                $course->what_learn1 = $request->what_learn1;
                $course->what_learn2 = $request->what_learn2;
            }

            $course->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function AdminUpdateCourseCertificate(Request $request)
    {

        Session::flash('type', 'update');
        Session::flash('id', $request->course_id);

        if (demoCheck()) {
            return redirect()->back();
        }
        Session::flash('type', 'courseDetails');


        $rules = [
            'certificate' => 'required',

        ];
        $this->validate($request, $rules, validationMessage($rules));


        try {

            $course = Course::find($request->course_id);
            $course->certificate_id = $request->certificate;
            $course->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function CourseQuetionShow($question_id, $id, $chapter_id, $lesson_id)
    {
        try {
            $levels = QuestionLevel::get();
            $groups = QuestionGroup::get();
            $banks = [];
            $bank = QuestionBank::with('category', 'subCategory', 'questionGroup')->find($question_id);
            $categories = Category::get();
            $data = [];
            $data['lesson_id'] = $lesson_id;
            $data['chapter_id'] = $chapter_id;
            $data['edit_chapter_id'] = $chapter_id;

            $user = Auth::user();
            $course = Course::findOrFail($id);
            if ($course->type == 1) {

                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->latest()->get();
                } else {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->where('created_by', $user->id)->latest()->get();
                }

            } else {
                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('active_status', 1)->get();

                } else {
                    $quizzes = OnlineQuiz::where('created_by', $user->id)->where('active_status', 1)->get();

                }
            }

            $chapters = Chapter::where('course_id', $id)->orderBy('position', 'asc')->with('lessons')->get();


            $categories = Category::get();
            $instructors = User::where('role_id', 2)->get();
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();
            $course_exercises = CourseExercise::where('course_id', $id)->get();

            $video_list = $this->getVimeoList();
            $vdocipher_list = $this->getVdoCipherList();
            $levels = CourseLevel::where('status', 1)->get();
            if (Auth::user()->role_id == 1) {
                $certificates = Certificate::latest()->get();
            } else {
                $certificates = Certificate::where('created_by', Auth::user()->id)->latest()->get();
            }


            // return $quizzes;
            return view('coursesetting::course_details', compact('data', 'bank', 'vdocipher_list', 'levels', 'video_list', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'quizzes', 'certificates'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function CourseLessonShow($id, $chapter_id, $lesson_id)
    {
        try {
            $data = [];
            $data['edit_lesson_id'] = $lesson_id;
            $data['chapter_id'] = $chapter_id;

            $user = Auth::user();
            $course = Course::findOrFail($id);
            if ($course->type == 1) {
                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->latest()->get();
                } else {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->where('created_by', $user->id)->latest()->get();
                }
            } else {
                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('active_status', 1)->get();
                } else {
                    $quizzes = OnlineQuiz::where('created_by', $user->id)->where('active_status', 1)->get();
                }
            }

            $chapters = Chapter::where('course_id', $id)->orderBy('position', 'asc')->with('lessons')->get();

            $categories = Category::get();
            $instructors = User::where('role_id', 2)->get();
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();
            $course_exercises = CourseExercise::where('course_id', $id)->get();

            $video_list = $this->getVimeoList();
            $vdocipher_list = $this->getVdoCipherList();

            $levels = CourseLevel::where('status', 1)->get();
            if (Auth::user()->role_id == 1) {
                $certificates = Certificate::latest()->get();
            } else {
                $certificates = Certificate::where('created_by', Auth::user()->id)->latest()->get();
            }
            // $editChapter = Chapter::where('id', $chapter_id)->first();
            $editLesson = Lesson::where('id', $lesson_id)->first();


            $data['isDefault'] = false;
            if (isModuleActive('Org')) {
                $material = OrgMaterial::where('link', $editLesson->video_url)->first();
                if ($material) {
                    $data['isDefault'] = false;
                } else {
                    $data['isDefault'] = true;
                }
            }

            return view('coursesetting::course_details', $data, compact('data', 'editLesson', 'levels', 'video_list', 'vdocipher_list', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'quizzes', 'certificates'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function CourseChapterShow($id, $chapter_id)
    {
        try {
            $data = [];
            $data['chapter_id'] = $chapter_id;

            $user = Auth::user();
            $course = Course::findOrFail($id);
            if ($course->type == 1) {

                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->latest()->get();
                } else {
                    $quizzes = OnlineQuiz::where('category_id', $course->category_id)->where('created_by', $user->id)->latest()->get();
                }

            } else {
                if ($user->role_id == 1) {
                    $quizzes = OnlineQuiz::where('active_status', 1)->get();

                } else {
                    $quizzes = OnlineQuiz::where('created_by', $user->id)->where('active_status', 1)->get();

                }
            }

            $chapters = Chapter::where('course_id', $id)->orderBy('position', 'asc')->with('lessons')->get();

            $categories = Category::get();
            $instructors = User::where('role_id', 2)->get();
            $languages = Language::select('id', 'native', 'code')
                ->where('status', '=', 1)
                ->get();
            $course_exercises = CourseExercise::where('course_id', $id)->get();

            $video_list = $this->getVimeoList();
            $vdocipher_list = $this->getVdoCipherList();

            $levels = CourseLevel::where('status', 1)->get();
            if (Auth::user()->role_id == 1) {
                $certificates = Certificate::latest()->get();
            } else {
                $certificates = Certificate::where('created_by', Auth::user()->id)->latest()->get();
            }
            $editChapter = Chapter::where('id', $chapter_id)->first();

            return view('coursesetting::course_details', compact('data', 'editChapter', 'levels', 'video_list', 'vdocipher_list', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'quizzes', 'certificates'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function courseDetails($id, $data = null)
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);
        if ($course->type == 1) {

            if ($user->role_id == 1) {
                $quizzes = OnlineQuiz::where('status', 1)->where('category_id', $course->category_id)->latest()->get();
            } else {
                $quizzes = OnlineQuiz::where('status', 1)->where('category_id', $course->category_id)->where('created_by', $user->id)->latest()->get();
            }

        } else {
            if ($user->role_id == 1) {
                $quizzes = OnlineQuiz::where('status', 1)->get();

            } else {
                $quizzes = OnlineQuiz::where('status', 1)->where('created_by', $user->id)->get();

            }
        }

        $chapters = Chapter::where('course_id', $id)->orderBy('position', 'asc')->with('lessons')->get();

        $categories = Category::get();
        $instructors = User::whereIn('role_id', [1, 2])->get();
        $languages = Language::select('id', 'native', 'code')
            ->where('status', '=', 1)
            ->get();
        $course_exercises = CourseExercise::where('course_id', $id)->get();

        $video_list = $this->getVimeoList();
        $vdocipher_list = $this->getVdoCipherList();


        $levels = CourseLevel::where('status', 1)->get();
        if (Auth::user()->role_id == 1) {
            $certificates = Certificate::latest()->get();
        } else {
            $certificates = Certificate::where('created_by', Auth::user()->id)->latest()->get();
        }

        return view('coursesetting::course_details', compact('data', 'vdocipher_list', 'levels', 'video_list', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'quizzes', 'certificates'));

    }

    public function setCourseDripContent(Request $request)
    {

        Session::flash('type', 'drip');
        $course_id = $request->get('course_id');


        $lesson_id = $request->get('lesson_id');
        $lesson_date = $request->get('lesson_date');
        $lesson_day = $request->get('lesson_day');
        $drip_type = $request->get('drip_type');


        if (!empty($lesson_id) && is_array($lesson_id)) {
            foreach ($lesson_id as $l_key => $l_id) {
                $lesson = Lesson::find($l_id);

                if ($lesson) {

                    $checkType = $drip_type[$l_key];

                    if ($checkType == 1) {
                        $lesson->unlock_days = null;

                        if (!empty($lesson_date[$l_key])) {
                            $lesson->unlock_date = date('Y-m-d', strtotime($lesson_date[$l_key]));
                        } else {
                            $lesson->unlock_date = null;
                        }
                    } else {
                        $lesson->unlock_date = null;
                        if (!empty($lesson_day[$l_key])) {
                            $lesson->unlock_days = $lesson_day[$l_key];
                        } else {
                            $lesson->unlock_days = null;
                        }
                    }


                    $lesson->save();
                }
            }

        }
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }


    public function changeChapterPosition(Request $request)
    {
        $ids = $request->get('ids');
        if (count($ids) != 0) {
            foreach ($ids as $key => $id) {

                $chapter = Chapter::find($id);
                if ($chapter) {
                    $chapter->position = $key + 1;
                    $chapter->save();
                }
            }
        }
        return true;
    }

    public function changeLessonPosition(Request $request)
    {
        $ids = $request->get('ids');
        // return $ids;
        if (count($ids) != 0) {
            foreach ($ids as $key => $id) {
                $lesson = Lesson::find($id);
                if ($lesson) {
                    $lesson->position = $key + 1;
                    $lesson->save();
                }
            }
        }
        return true;
    }


    public function courseDelete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $hasCourse = CourseEnrolled::where('course_id', $id)->count();
        if ($hasCourse != 0) {
            Toastr::error('Course Already Enrolled By ' . $hasCourse . ' Student', trans('common.Failed'));
            return redirect()->back();
        }

        $carts = Cart::where('course_id', $id)->get();
        foreach ($carts as $cart) {
            $cart->delete();
        }

        $course = Course::findOrFail($id);
        if ($course->host == "Self") {
            if (file_exists($course->trailer_link)) {
                unlink($course->trailer_link);
            }
        }
        if (file_exists($course->image)) {
            unlink($course->image);
        }
        if (file_exists($course->thumbnail)) {
            unlink($course->thumbnail);
        }

        $chapters = Chapter::where('course_id', $course->id)->get();
        foreach ($chapters as $chapter) {
            $lessons = Lesson::where('chapter_id', $chapter->id)->where('course_id', $course->id)->get();
            foreach ($lessons as $key => $lesson) {
                $complete_lessons = LessonComplete::where('lesson_id', $lesson->id)->get();
                foreach ($complete_lessons as $complete) {
                    $complete->delete();
                }
                $lessonController = new LessonController();
                $lessonController->lessonFileDelete($lesson);
                $lesson->delete();
            }

            $chapter->delete();
        }

        if (isModuleActive('BundleSubscription')) {
            $bundle = BundleCourse::where('course_id', $course->id)->get();
            foreach ($bundle as $b) {
                $b->delete();
            }
        }

        $course->delete();


        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }


    public function getAllCourseData(Request $request)
    {

        $query = Course::whereIn('type', [1, 2])->with('category', 'quiz', 'user');
        if ($request->course_status != "") {
            if ($request->course_status == 1) {
                $query->where('courses.status', 1);
            } elseif ($request->course_status == 0) {
                $query->where('courses.status', 0);
            } else {

            }
        }
        if ($request->category != "") {
            $query->where('category_id', $request->category);
        }
        if ($request->type != "") {
            $query->where('type', $request->type);
        }
        if ($request->instructor != "") {
            $query->where('user_id', $request->instructor);
        }
        if ($request->search_status != "") {
            $query->where('courses.status', $request->search_status);
        }


        if (isInstructor()) {
            $query->where('user_id', '=', Auth::id());
            $query->orWhereJsonContains('assistant_instructors',[(string)Auth::user()->id]);
        }

        $query->select('courses.*');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('type', function ($query) {
                return $query->type == 1 ? 'Course' : 'Quiz';

            })->addColumn('status', function ($query) {
                $approve = false;
                if (Auth::user()->role_id == 1) {
                    $approve = true;
                } else {
                    $courseApproval = Settings('course_approval');
                    if ($courseApproval == 0) {
                        $approve = true;
                    }
                }
                if ($approve) {
                    if (permissionCheck('course.status_update')) {
                        $status_enable_eisable = "status_enable_disable";
                    } else {
                        $status_enable_eisable = "";
                    }
                    $checked = $query->status == 1 ? "checked" : "";
                    $view = '<label class="switch_toggle" for="active_checkbox' . $query->id . '">
                                                    <input type="checkbox" class="' . $status_enable_eisable . '"
                                                           id="active_checkbox' . $query->id . '" value="' . $query->id . '"
                                                             ' . $checked . '><i class="slider round"></i></label>';
                } else {
                    $view = $query->status == 1 ? "Approved" : 'Pending';
                }


                return $view;
            })->addColumn('lessons', function ($query) {
                return $query->lessons->count();
            })
            ->editColumn('category', function ($query) {
                if ($query->category) {
                    return $query->category->name;
                } else {
                    return '';
                }

            })
            ->editColumn('quiz', function ($query) {
                if ($query->quiz) {
                    return $query->quiz->title;
                } else {
                    return '';
                }

            })->editColumn('user', function ($query) {
                if ($query->user) {
                    return $query->user->name;
                } else {
                    return '';
                }

            })->addColumn('enrolled_users', function ($query) {
                return $query->enrollUsers->where('teach_via', 1)->count() . "/" . $query->enrollUsers->where('teach_via', 2)->count();
            })
            ->editColumn('scope', function ($query) {
                if ($query->scope == 1) {
                    $scope = trans('courses.Public');
                } else {
                    $scope = trans('courses.Private');
                }
                return $scope;

            })->addColumn('price', function ($query) {
                $priceView = '';
                if ($query->discount_price != null) {
                    $priceView = '<span>' . getPriceFormat($query->discount_price) . '</span>';
                } else {
                    $priceView = '<span>' . getPriceFormat($query->price) . '</span>';

                }
                return $priceView;


            })->addColumn('action', function ($query) {
                if (permissionCheck('course.details')) {
                    if ($query->type == 1) {
                        $course_detalis = '<a href="' . route('courseDetails', [$query->id]) . '" class="dropdown-item" >' . __('courses.Add Lesson') . '</a>';
                    } else {
                        $course_detalis = "";
                    }
                } else {
                    $course_detalis = "";
                }

                if (permissionCheck('course.edit')) {


                    $title = 'data-title ="' . escapHtmlChar($query->title) . '"';

                    $course_edit = '<a href="' . route('courseDetails', [$query->id]) . '?type=courseDetails" class="dropdown-item" >' . __('common.Edit') . '</a>';
                } else {
                    $course_edit = "";
                }

                if (permissionCheck('course.view')) {
                    $course_view = '<a href="' . route('courseDetails', [$query->id]) . '" class="dropdown-item" >' . trans('common.View') . '</a>';
                } else {
                    $course_view = "";
                }

                if (permissionCheck('course.delete')) {
                    $deleteUrl = route('course.delete', $query->id);
                    $course_delete = '<a onclick="confirm_modal(\'' . $deleteUrl . '\')"
                                                               class="dropdown-item edit_brand">' . trans('common.Delete') . '</a>';
                } else {
                    $course_delete = "";
                }
                if (permissionCheck('course.enrolled_students') && $query->type == 1) {
                    $enrolled_students = '<a href="' . route('course.enrolled_students', $query->id) . '" class="dropdown-item edit_brand">' . trans('student.Students') . '</a>';
                } else {
                    $enrolled_students = "";
                }
                if (isModuleActive('CourseInvitation') && permissionCheck('course.courseInvitation') && $query->type == 1) {
                    $course_invitation = '<a href="' . route('course.courseInvitation', $query->id) . '" class="dropdown-item edit_brand">' . trans('common.Send Invitation') . '</a>';
                } else {
                    $course_invitation = "";
                }
                if (Settings('frontend_active_theme') == "edume") {
                    if ($query->feature == 0) {
                        $markAsFeature = '<a href="' . route('courseMakeAsFeature', [$query->id, 'make']) . '" class="dropdown-item" >' . trans('courses.Mark As Feature') . '</a>';

                    } else {
                        $markAsFeature = '<a href="' . route('courseMakeAsFeature', [$query->id, 'remove']) . '" class="dropdown-item" >' . trans('courses.Remove Feature') . '</a>';
                    }
                } else {
                    $markAsFeature = '';
                }

                $actioinView = ' <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        ' . trans('common.Action') . '
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenu2">
                                                        <a target="_blank"
                                                           href="' . courseDetailsUrl($query->id, $query->type, $query->slug) . '"
                                                           class="dropdown-item"
                                                        > ' . trans('courses.Frontend View') . '</a>
                                                        ' . $course_detalis . '
                                                        ' . $markAsFeature . '
                                                        ' . $course_edit . '
                                                        ' . $course_view . '
                                                        ' . $course_delete . '
                                                        ' . $enrolled_students . '
                                                        ' . $course_invitation . '




                                                    </div>
                                                </div>';

                return $actioinView;


            })->rawColumns(['status', 'price', 'action', 'enrolled_users'])
            ->make(true);
    }

    public function configVimeo()
    {
        try {

            if (config('vimeo.connections.main.common_use')) {
                $vimeo_client = saasEnv('VIMEO_CLIENT');
                $vimeo_secret = saasEnv('VIMEO_SECRET');
                $vimeo_access = saasEnv('VIMEO_ACCESS');
            } else {
                $vimeos = Cache::rememberForever('vimeoSetting_' . SaasDomain(), function () {
                    return \Modules\VimeoSetting\Entities\Vimeo::all();
                });
                $vimeo = $vimeos->where('created_by', Auth::user()->id)->first();
                if ($vimeo) {
                    $vimeo_client = $vimeo->vimeo_client;
                    $vimeo_secret = $vimeo->vimeo_secret;
                    $vimeo_access = $vimeo->vimeo_access;
                }

            }
            if (empty($vimeo_secret) || empty($vimeo_client)) {
                return null;
            }
            $lib = new  Vimeo($vimeo_client, $vimeo_secret);
            $lib->setToken($vimeo_access);
            return $lib;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getVideoFromVimeoApi($page = 1)
    {
        try {
            if (config('vimeo.connections.main.upload_type') == "Direct") {
                return [];
            }
            if ($this->configVimeo()) {
                return $this->configVimeo()->request('/me/videos', [
                    'per_page' => 100,
                    'page' => $page,
                ], 'GET');
            } else {
                return [];
            }

        } catch (\Exception $e) {
            return [];
        }
    }

    public function getVimeoList()
    {
        try {
            $video_list = [];
            $page = 1;
            $vimeo_video_list = $this->getVideoFromVimeoApi($page);

            if (isset($vimeo_video_list['body']['error'])) {
//                Toastr::error($vimeo_video_list['body']['error'], trans('common.Failed'));
            }
            if (isset($vimeo_video_list['body']['total'])) {
                $total_videos = $vimeo_video_list['body']['total'];

                if (isset($vimeo_video_list['body']['data'])) {
                    if (count($vimeo_video_list['body']['data']) != 0) {
                        foreach ($vimeo_video_list['body']['data'] as $data) {
                            $video_list[] = $data;
                        }
                    }
                    $totalPage = round($total_videos / 3);

                    for ($page = 2; $page <= $totalPage; $page++) {
                        $list = $this->getVideoFromVimeoApi($page);
                        if (isset($list['body']['data'])) {
                            if (count($list['body']['data']) != 0) {
                                foreach ($list['body']['data'] as $data) {
                                    $video_list[] = $data;
                                }
                            }
                        }
                        $page++;
                    }


                }
            }


        } catch (\Exception $e) {
            $video_list = [];
        }


        return $video_list;
    }

    public function addNewCourse()
    {
        if (saasPlanCheck('course')) {
            Toastr::error('You have reached valid course limit', trans('common.Failed'));
            return redirect()->back();
        }
        $user = Auth::user();

        $video_list = $this->getVimeoList();
        $vdocipher_list = $this->getVdoCipherList();


        $categories = Category::get();
        if ($user->role_id == 1) {
            $quizzes = OnlineQuiz::where('status', 1)->latest()->get();
        } else {
            $quizzes = OnlineQuiz::where('status', 1)->where('created_by', $user->id)->latest()->get();
        }

        $instructors = User::whereIn('role_id', [1, 2])->select('name', 'id')->get();
        $languages = Language::select('id', 'native', 'code')
            ->where('status', '=', 1)
            ->get();
        $levels = CourseLevel::where('status', 1)->get();
        $title = trans('courses.All');

        $sub_lists = $this->getSubscriptionList();

        return view('coursesetting::add_course', compact('sub_lists', 'levels', 'video_list', 'vdocipher_list', 'title', 'quizzes', 'categories', 'languages', 'instructors', 'vdocipher_list'));


    }

    public function changeLessonChapter(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $lesson_id = $request->lesson_id;

        $lesson = Lesson::findOrFail($lesson_id);
        $lesson->chapter_id = $chapter_id;
        $lesson->save();
        return true;
    }

    public function getVdoCipherList()
    {
        try {
            $curl = curl_init();

            $header = array(
                "Accept: application/json",
                "Authorization:Apisecret " . env('VDOCIPHER_API_SECRET'),
                "Content-Type: application/json"
            );

//            &q=array
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://dev.vdocipher.com/api/videos?page=1&limit=20",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $header,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                return [];
            } else {
                return json_decode($response)->rows;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function courseMakeAsFeature($id, $type)
    {
        try {
            if ($type == "make") {
                $items = Course::all();
                foreach ($items as $item) {
                    if ($id == $item->id) {
                        $featureStatus = 1;
                    } else {
                        $featureStatus = 0;
                    }
                    $item->feature = $featureStatus;
                    $item->save();
                }
            } else {
                $course = Course::find($id);
                $course->feature = 0;
                $course->save();
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->to(route('getAllCourse'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function CourseQuestionDelete($quiz_id, $question_id)
    {
        $assign = OnlineExamQuestionAssign::where('online_exam_id', $quiz_id)->where('question_bank_id', $question_id)->first();
        if ($assign) {
            $assign->delete();
        }

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();

    }

    public function getAllVdocipherData(Request $request)
    {
        try {
            $curl = curl_init();

            $header = array(
                "Accept: application/json",
                "Authorization:Apisecret " . env('VDOCIPHER_API_SECRET'),
                "Content-Type: application/json"
            );
            if ($request->page) {
                $page = $request->page;
            } else {
                $page = 1;
            }

            if ($request->search) {
                $search = $request->search;
            } else {
                $search = '';
            }

//            &q=array
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://dev.vdocipher.com/api/videos?page=" . $page . "&limit=20&q=" . $search,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $header,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                return [];
            } else {
                $items = json_decode($response)->rows;
                $response = [];
                foreach ($items as $item) {
                    $response[] = [
                        'id' => $item->id,
                        'text' => $item->title
                    ];
                }
                $data['results'] = $response;
                $data['pagination'] = ["more" => count($response) != 0 ? true : false];
                return response()->json($data);
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getSingleVdocipherData($id)
    {
        try {
            $curl = curl_init();

            $header = array(
                "Accept: application/json",
                "Authorization:Apisecret " . env('VDOCIPHER_API_SECRET'),
                "Content-Type: application/json"
            );

//            &q=array
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://dev.vdocipher.com/api/videos/" . $id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $header,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                return null;
            } else {
                $item = json_decode($response);

                return response()->json($item);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
