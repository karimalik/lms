<?php

namespace Modules\CourseSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\LessonComplete;
use App\Traits\Filepond;
use App\Traits\Gdrive;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Image;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\Chapter;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseExercise;
use Modules\CourseSetting\Entities\CourseLevel;
use Modules\CourseSetting\Entities\Lesson;
use Modules\Localization\Entities\Language;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\SCORM\Http\Controllers\SCORMController;
use Modules\Setting\Model\GeneralSetting;
use Modules\VdoCipher\Http\Controllers\VdoCipherController;


class InstructorCourseSettingController extends Controller
{
    use Filepond, Gdrive;

    public function saveChapter(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;
        $this->validate($request, [
            'input_type' => 'required',
        ]);

        if ($request->input_type == 1) {
            $request->validate([
                'chapter_name' => 'required',
            ]);
        } else if ($request->input_type == 2) {
            $request->validate([
                'quiz' => 'required',
                'chapterId' => 'required',
                'lock' => 'required',

            ]);
        } else {
            $request->validate([
                'name' => 'required',
                'chapter_id' => 'required',
                'course_id' => 'required',
//                    'video_url'=>'required',
            ]);
            if (isModuleActive('Org')) {
                if ($request->fileType != 2) {
                    $this->validate($request, [
                        'file_type' => 'required',
                        'file_path' => 'required',
                    ]);
                } else {
                    if ($request->get('host') == "Vimeo") {
                        $request->validate([
                            'vimeo' => 'required',
                        ]);
                    } elseif ($request->get('host') == "VdoCipher") {
                        $request->validate([
                            'vdocipher' => 'required',
                        ]);
                    } elseif ($request->get('host') == "Iframe") {
                        $request->validate([
                            'iframe_url' => 'required',
                        ]);
                    } elseif ($request->get('host') == "Youtube" || $request->get('host') == "URL") {
                        $request->validate([
                            'video_url' => 'required',
                        ]);
                    } elseif ($request->get('host') == "ImagePreview") {
                        //
                    } else {
                        $request->validate([
                            'file' => 'required',
                        ]);
                    }
                }

            } else {
                if ($request->get('host') == "Vimeo") {
                    $request->validate([
                        'vimeo' => 'required',
                    ]);
                } elseif ($request->get('host') == "VdoCipher") {
                    $request->validate([
                        'vdocipher' => 'required',
                    ]);
                } elseif ($request->get('host') == "Iframe") {
                    $request->validate([
                        'iframe_url' => 'required',
                    ]);
                } elseif ($request->get('host') == "Youtube" || $request->get('host') == "URL") {
                    $request->validate([
                        'video_url' => 'required',
                    ]);
                } elseif ($request->get('host') == "ImagePreview") {
                    //
                } else {
                    $request->validate([
                        'file' => 'required',
                    ]);
                }
            }

        }

        if ($request->input_type == 1) {
            try {
                $loginUser = Auth::user();

                if ($loginUser->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }

                if (isset($course)) {

                    $chpter_no = Chapter::where('course_id', $course->course_id)->count();
                    $chapter = new Chapter();
                    $chapter->name = $request->chapter_name;
                    $chapter->course_id = $request->course_id;
                    $chapter->chapter_no = $chpter_no + 1;
                    $chapter->save();

                    if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                        foreach ($course->enrollUsers as $user) {
                            if (UserEmailNotificationSetup('Course_Chapter_Added', $user)) {
                                send_email($user, 'Course_Chapter_Added', [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                ]);
                            }
                            if (UserBrowserNotificationSetup('Course_Chapter_Added', $user)) {
                                send_browser_notification($user, $type = 'Course_Chapter_Added', $shortcodes = [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                ],
                                    '',//actionText
                                    ''//actionUrl
                                );
                            }
                        }
                    }

                    $courseUser = $course->user;
                    if (UserEmailNotificationSetup('Course_Chapter_Added', $courseUser)) {
                        send_email($courseUser, 'Course_Chapter_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Course_Chapter_Added', $courseUser)) {
                        send_browser_notification($courseUser, $type = 'Course_Chapter_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }


                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();
                } else {
                    Toastr::error('Invalid Access !', 'Failed');
                    return redirect()->back();
                }
            } catch (Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        } else if ($request->input_type == 2) {
            try {

                $loginUser = Auth::user();
                if ($loginUser->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }
                $chapter = Chapter::find($request->chapterId);

                if (isset($course) && isset($chapter)) {

                    $lesson = new Lesson();
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapterId;
                    $lesson->quiz_id = $request->quiz;
                    $lesson->is_quiz = $request->is_quiz;
                    $lesson->is_lock = $request->lock;
                    $lesson->save();
                    $quiz = OnlineQuiz::find($request->quiz);

                    if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                        foreach ($course->enrollUsers as $user) {
                            if (UserEmailNotificationSetup('Course_Quiz_Added', $user)) {
                                send_email($user, 'Course_Quiz_Added', [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ]);

                            }
                            if (UserBrowserNotificationSetup('Course_Quiz_Added', $user)) {

                                send_browser_notification($user, $type = 'Course_Quiz_Added', $shortcodes = [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ],
                                    '',//actionText
                                    ''//actionUrl
                                );
                            }
                        }
                    }

                    $courseUser = $course->user;
                    if (UserEmailNotificationSetup('Course_Quiz_Added', $courseUser)) {
                        send_email($courseUser, 'Course_Quiz_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Course_Quiz_Added', $courseUser)) {
                        send_browser_notification($courseUser, $type = 'Course_Quiz_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }

                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();
                }

                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->back();

            } catch (Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        } else {
            try {

                $user = Auth::user();

                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }

                $chapter = Chapter::find($request->chapter_id);

                if (isset($course) && isset($chapter)) {

                    $lesson = new Lesson();
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapter_id;
                    $lesson->name = $request->name;
                    $lesson->description = $request->description;

                    if (isModuleActive('Org') && $request->fileType != 2) {
                        $host = $request->file_type;
                        if ($host == "Video") {
                            $host = "Self";
                        }
                        $lesson->host = $host;
                        $lesson->video_url = $request->file_path;

                    } else {
                        if ($request->get('host') == "Vimeo") {
                            if (config('vimeo.connections.main.upload_type') == "Direct") {
                                $courseSettingController = new CourseSettingController();
                                $lesson->video_url = $courseSettingController->uploadFileIntoVimeo($request->name, $request->vimeo);
                            } else {
                                $lesson->video_url = $request->vimeo;
                            }
                        } elseif ($request->get('host') == "VdoCipher") {
                            $lesson->video_url = $request->vdocipher;
                        } elseif ($request->get('host') == "Youtube" || $request->get('host') == "URL") {
                            $lesson->video_url = $request->video_url;
                        } elseif ($request->get('host') == "Iframe") {
                            $lesson->video_url = $request->iframe_url;
                        } elseif ($request->get('host') == "Self") {
                            $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 'local');
                        } elseif ($request->get('host') == "AmazonS3") {
                            $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 's3');

                        } elseif ($request->get('host') == "VdoCipher") {
                            $vdoCipher = new VdoCipherController();
                            $lesson->video_url = $vdoCipher->uploadToVdoCipher($request->get('file'));

                        } elseif ($request->get('host') == "SCORM") {
                            $scorm = new SCORMController();
                            $path = $this->getPublicPathFromServerId($request->get('file'), 'local');
                            $url = $scorm->getScormUrl($path, $request->get('host'), $request->scorm_vendor_type);
                            $lesson->video_url = $url;
                        } elseif ($request->get('host') == "SCORM-AwsS3") {
                            $scorm = new SCORMController();
                            $path = $this->getPublicPathFromServerId($request->get('file'), 's3');
                            $url = $scorm->getScormUrl($path, $request->get('host'));
                            $lesson->video_url = $url;
                        } elseif (
                            $request->get('host') == "Zip"
                            || $request->get('host') == "PowerPoint"
                            || $request->get('host') == "Excel"
                            || $request->get('host') == "Text"
                            || $request->get('host') == "Word"
                            || $request->get('host') == "PDF"
                            || $request->get('host') == "Image"
                        ) {
                            $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 'local');

                        } elseif ($request->get('host') == "GoogleDrive") {
                            if (empty(\auth()->user()->googleToken)) {
                                Toastr::error(trans('setting.Google Drive login is required'), trans('common.Error'));
                                return redirect()->back();
                            }
                            $id = null;
                            $url = $this->getPublicPathFromServerId($request->get('file'), 'local');
                            if ($url) {
                                $file = $this->storeFileInGDrive(base_path($url), null);
                                if (isset($file->id)) {
                                    $id = $file->id;
                                }
                                if (File::exists(base_path($url))) {
                                    File::delete(base_path($url));
                                }
                            }

                            $lesson->video_url = $id;

                        } else {
                            $lesson->video_url = null;
                        }
                        $lesson->host = $request->host;

                    }
                    if ($lesson->video_url != null && saasPlanCheck('upload_limit', $lesson->video_url)) {
                        Toastr::error('You have reached upload limit', trans('common.Failed'));
                        return redirect()->back();
                    }

                    $lesson->duration = $request->duration;
                    $lesson->is_lock = $request->is_lock;
                    $lesson->save();

                    if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                        foreach ($course->enrollUsers as $user) {
                            if (UserEmailNotificationSetup('Course_Lesson_Added', $user)) {
                                send_email($user, 'Course_Lesson_Added', [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'lesson' => $lesson->name,
                                ]);

                            }
                            if (UserBrowserNotificationSetup('Course_Lesson_Added', $user)) {

                                send_browser_notification($user, $type = 'Course_Lesson_Added', $shortcodes = [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'lesson' => $lesson->name,
                                ],
                                    '',//actionText
                                    ''//actionUrl
                                );
                            }
                        }
                    }
                    $courseUser = $course->user;
                    if (UserEmailNotificationSetup('Course_Lesson_Added', $courseUser)) {
                        send_email($courseUser, 'Course_Lesson_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'lesson' => $lesson->name,
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Course_Lesson_Added', $courseUser)) {
                        send_browser_notification($courseUser, $type = 'Course_Lesson_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'lesson' => $lesson->name,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }


                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();
                }

                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->back();

            } catch (Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        }

    }

    public function deleteChapter($chapter, $course_id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $user = Auth::user();
            if ($user->role_id == 1) {
                $course = Course::where('id', $course_id)->first();
            } else {
                $course = Course::where('id', $course_id)->where('user_id', Auth::id())->first();
            }

            // return $course;
            if (isset($course)) {
                $lessons = Lesson::where('chapter_id', $chapter)->where('course_id', $course_id)->get();
                foreach ($lessons as $key => $lesson) {
                    $complete_lessons = LessonComplete::where('lesson_id', $lesson->id)->get();
                    foreach ($complete_lessons as $complete) {
                        $complete->delete();
                    }
                    $lessonController = new LessonController();
                    $lessonController->lessonFileDelete($lesson);
                    $lesson->delete();
                }

                $chapter = Chapter::find($chapter);
                $chapter->delete();

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('courseDetails', [$course_id]);
            } else {
                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->route('courseDetails', [$course_id]);
            }
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function updateChapter(Request $request)
    {
        $this->validate($request, [
            'input_type' => 'required',
        ]);

        if ($request->input_type == 1) {
            $request->validate([
                'chapter_name' => 'required',
            ]);
        } else if ($request->input_type == 2) {
            $request->validate([
                'quiz' => 'required',
                'chapterId' => 'required',
                'lock' => 'required',

            ]);
        } else {
            $request->validate([
                'name' => 'required',
                'chapter_id' => 'required',
                'course_id' => 'required',
            ]);

            if ($request->get('host') == "Vimeo") {
                $request->validate([
                    'vimeo' => 'required',
                ]);
            } elseif ($request->get('host') == "VdoCipher") {
                $request->validate([
                    'vdocipher' => 'required',
                ]);
            } elseif ($request->get('host') == "Youtube" || $request->get('host') == "URL") {
                $request->validate([
                    'video_url' => 'required',
                ]);
            } elseif ($request->get('host') == "Iframe") {
                $request->validate([
                    'iframe_url' => 'required',
                ]);
            } else {
//                $request->validate([
//                    'file' => 'required',
//                ]);
            }
        }

        if ($request->input_type == 1) {
            try {
                $user = Auth::user();
                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }

                // return $course;
                if (isset($course)) {
                    $chapter = Chapter::find($request->chapter);
                    $chapter->name = $request->chapter_name;
                    $chapter->save();

                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->route('courseDetails', [$request->course_id]);
                } else {
                    Toastr::error('Invalid Access !', 'Failed');
                    return redirect()->route('courseDetails', [$request->course_id]);
                }
            } catch (Exception $e) {

                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        } else if ($request->input_type == 2) {
            try {
                $user = Auth::user();

                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }

                $chapter = Chapter::find($request->chapterId);

                if (isset($course) && isset($chapter)) {

                    $lesson = Lesson::find($request->lesson_id);
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapterId;
                    $lesson->quiz_id = $request->quiz;
                    $lesson->is_quiz = $request->is_quiz;
                    $lesson->is_lock = $request->lock;
                    $lesson->save();
                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->route('courseDetails', [$request->course_id]);
                }

                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->route('courseDetails', [$request->course_id]);

            } catch (Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        } else {
            try {

                $user = Auth::user();
                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }
                $chapter = Chapter::find($request->chapter_id);

                if (isset($course) && isset($chapter)) {
                    // $success = trans('lang.Lesson').' '.trans('lang.Added').' '.trans('lang.Successfully');

                    $lesson = Lesson::find($request->lesson_id);
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapter_id;
                    $lesson->name = $request->name;
                    $lesson->description = $request->description;


                    if (isModuleActive('Org') && $request->fileType != 2) {
                        $host = $request->file_type;
                        if ($host == "Video") {
                            $host = "Self";
                        }
                        $lesson->host = $host;
                        $lesson->video_url = $request->file_path;

                    } else {
                        $lesson->host = $request->host;

                        if ($request->get('host') == "Vimeo") {
                            if (config('vimeo.connections.main.upload_type') == "Direct") {
                                $courseSettingController = new CourseSettingController();
                                $lesson->video_url = $courseSettingController->uploadFileIntoVimeo($request->name, $request->vimeo);
                            } else {
                                $lesson->video_url = $request->vimeo;
                            }
                        } elseif ($request->get('host') == "VdoCipher") {
                            $lesson->video_url = $request->vdocipher;
                        } elseif ($request->get('host') == "Youtube" || $request->get('host') == "URL") {
                            $lesson->video_url = $request->video_url;
                        } elseif ($request->get('host') == "Iframe") {
                            $lesson->video_url = $request->iframe_url;
                        } elseif ($request->get('host') == "Self") {
//
                            if (!empty($request->get('file'))) {
                                $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 'local');
                            }


                        } elseif ($request->get('host') == "AmazonS3") {
                            if (!empty($request->get('file'))) {
                                $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 's3');
                            }

                        } elseif ($request->get('host') == "SCORM") {
                            if (!empty($request->get('file'))) {
                                $path = $this->getPublicPathFromServerId($request->get('file'), 'local');
                                $scorm = new SCORMController();
                                $url = $scorm->getScormUrl($path, $request->get('host'), $request->scorm_vendor_type);
                                $lesson->video_url = $url;
                            }
                        } elseif ($request->get('host') == "SCORM-AwsS3") {
                            if (!empty($request->get('file'))) {
                                $scorm = new SCORMController();
                                $path = $this->getPublicPathFromServerId($request->get('file'), 's3');
                                $url = $scorm->getScormUrl($path, $request->get('host'));

                                $lesson->video_url = $url;
                            }
                        } elseif (
                            $request->get('host') == "Zip"
                            || $request->get('host') == "PowerPoint"
                            || $request->get('host') == "Excel"
                            || $request->get('host') == "Text"
                            || $request->get('host') == "Word"
                            || $request->get('host') == "PDF"
                            || $request->get('host') == "Image"
                        ) {
                            $lesson->video_url = $this->getPublicPathFromServerId($request->get('file'), 'local');

                        } elseif ($request->get('host') == "GoogleDrive") {
                            if (empty(\auth()->user()->googleToken)) {
                                Toastr::error(trans('setting.Google Drive login is required'), trans('common.Error'));
                                return redirect()->back();
                            }
                            $id = null;
                            $url = $this->getPublicPathFromServerId($request->get('file'), 'local');
                            if ($url) {
                                $file = $this->storeFileInGDrive(base_path($url));
                                if (isset($file->id)) {
                                    $id = $file->id;
                                }
                                if (File::exists(base_path($url))) {
                                    File::delete(base_path($url));
                                }
                            }

                            $lesson->video_url = $id;

                        } else {
                            $lesson->video_url = null;
                        }
                    }

                    $lesson->duration = $request->duration;
                    $lesson->is_lock = $request->is_lock;
                    $lesson->update();

                    $self_hosts = ['Self', 'Image', 'PDF', 'Word', 'Excel', 'Text', 'Zip', 'PowerPoint'];
                    if (in_array($lesson->host, $self_hosts)) {
                        $file = $lesson->video_url;
                        $filesize = filesize($file); // bytes
                        // $filesize = round($filesize / 1024 / 1024, 1); //MB
                        $filesize = round($filesize / 1024, 2); //KB
                        if (isModuleActive('LmsSaas')) {
                            if (in_array($lesson->host, $self_hosts)) {
                                saasPlanManagement('upload_limit', 'create', $filesize);
                            }
                            if (in_array($lesson->host, $self_hosts) && $lesson->old_file_size != null) {
                                saasPlanManagement('upload_limit', 'delete', $lesson->old_file_size);
                            }
                        }

                        $lesson->old_file_size = $filesize;
                        $lesson->file_size = $filesize;
                        $lesson->update();
                    }


                    send_email(Auth::user(), 'Course_Lesson_Added ', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'chapter' => $chapter->name,
                        'lesson' => $lesson->name,
                    ]);


                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->route('courseDetails', [$request->course_id]);
                }

                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->route('courseDetails', [$request->course_id]);

            } catch (Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
            }
        }

    }

    public function editChapter($id, $course_id)
    {

        try {
            $courseSetting = new CourseSettingController();
            $video_list = $courseSetting->getVimeoList();
            $vdocipher_list = $courseSetting->getVdoCipherList();
            $editChapter = Chapter::where('id', $id)->first();
            $course = Course::find($course_id);
            $chapters = Chapter::where('course_id', $course_id)->with('lessons')->get();
            $categories = Category::get();
            $instructors = User::where('role_id', 2)->get();
            $languages = Language::get();
            $quizzes = OnlineQuiz::where('category_id', $course->category_id)->get();
            $course_exercises = CourseExercise::where('course_id', $course_id)->get();
            $levels = CourseLevel::where('status', 1)->get();
            // return $course;
            return view('coursesetting::course_details', compact('vdocipher_list', 'levels', 'course', 'chapters', 'categories', 'instructors', 'languages', 'course_exercises', 'editChapter', 'quizzes', 'video_list'));

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function saveFile(Request $request)
    {

        Session::flash('type', 'files');
        $request->validate([
            'status' => 'required',
            'file' => 'required'
        ]);

        try {

            $course_file = new CourseExercise();
            $course_file->course_id = $request->id;
            $course_file->file = $this->getPublicPathFromServerId($request->get('file'), 'local');

            if (saasPlanCheck('upload_limit', $course_file->file)) {
                Toastr::error('You have reached upload limit', trans('common.Failed'));
                return redirect()->back();
            }

            $course_file->lock = $request->lock;
            $course_file->fileName = $request->fileName;
            $course_file->status = $request->status;
            $course_file->save();

            $course = Course::find($request->id);
            if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                foreach ($course->enrollUsers as $user) {
                    if (UserEmailNotificationSetup('Course_ExerciseFile_Added', $user)) {
                        send_email($user, 'Course_ExerciseFile_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => Course::find($request->id)->first(['title'])->title,
                            'filename' => $course_file->fileName,
                        ]);

                    }
                    if (UserBrowserNotificationSetup('Course_ExerciseFile_Added', $user)) {

                        send_browser_notification($user, $type = 'Course_ExerciseFile_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => Course::find($request->id)->first(['title'])->title,
                            'filename' => $course_file->fileName,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                }
            }
            $courseUser = $course->user;
            if (UserEmailNotificationSetup('Course_ExerciseFile_Added', $courseUser)) {
                send_email($courseUser, 'Course_ExerciseFile_Added', [
                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                    'course' => $course->title,
                    'filename' => $course_file->fileName,
                ]);
            }
            if (UserBrowserNotificationSetup('Course_ExerciseFile_Added', $courseUser)) {
                send_browser_notification($courseUser, $type = 'Course_ExerciseFile_Added', $shortcodes = [
                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                    'course' => $course->title,
                    'filename' => $course_file->fileName,
                ],
                    '',//actionText
                    ''//actionUrl
                );
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function updateFile(Request $request)
    {
        Session::flash('type', 'files');
        $request->validate([
            'status' => 'required',
            // 'exercise_file'=>'required'
        ]);

        try {

            $course_file = CourseExercise::find($request->id);


            if ($request->get('file') != "") {

                $course_file->file = $this->getPublicPathFromServerId($request->get('file'), 'local');


                $filesize = filesize($course_file->file); // bytes
                // $filesize = round($filesize / 1024 / 1024, 1); //MB
                $filesize = round($filesize / 1024, 2); //KB
                if (saasPlanCheck('upload_limit', $filesize)) {
                    Toastr::error('You have reached upload limit', trans('common.Failed'));
                    return redirect()->back();
                }
                if (isModuleActive('LmsSaas')) {
                    saasPlanManagement('upload_limit', 'create', $filesize);
                    if ($course_file->old_file_size != null) {
                        saasPlanManagement('upload_limit', 'delete', $course_file->old_file_size);
                    }
                }
            }


            $course_file->old_file_size = $filesize;
            $course_file->file_size = $filesize;

            $course_file->lock = $request->lock;
            $course_file->fileName = $request->fileName;
            $course_file->status = $request->status;
            $course_file->save();
            $course = Course::find($course_file->course_id);
            if ($course) {
                if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                    foreach ($course->enrollUsers as $user) {
                        if (UserEmailNotificationSetup('Course_ExerciseFile_Added', $user)) {
                            send_email($user, 'Course_ExerciseFile_Added', [
                                'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'filename' => $course_file->fileName,
                            ]);

                        }
                        if (UserBrowserNotificationSetup('Course_ExerciseFile_Added', $user)) {

                            send_browser_notification($user, $type = 'Course_ExerciseFile_Added', $shortcodes = [
                                'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'filename' => $course_file->fileName,
                            ],
                                '',//actionText
                                ''//actionUrl
                            );
                        }
                    }
                }
            }


            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function deleteFile(Request $request)
    {
        Session::flash('type', 'files');
        try {
            $course_file = CourseExercise::find($request->id);
            if (file_exists($course_file->file)) {
                unlink($course_file->file);
            }
            $course_file->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function download_course_file($id)
    {
        try {
            $course_file = CourseExercise::find($id);
            // return base_path();
            $file_path = base_path('/' . $course_file->file);
            return response()->download($file_path);
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }


    }
}
