<?php

namespace Modules\StudentSetting\Http\Controllers;

use App\Exports\CountryList;
use App\Exports\RegularStudentExport;
use App\Http\Controllers\Controller;
use App\Imports\ImportRegularStudent;
use App\User;
use App\LessonComplete;
use App\StudentCustomField;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OfflineStudentExport;
use Illuminate\Support\Facades\Validator;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\Lesson;
use Illuminate\Contracts\Support\Renderable;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Certificate\Entities\CertificateRecord;
use App\Http\Controllers\Frontend\WebsiteController;
use Modules\StudentSetting\Entities\StudentImportTemporary;

class StudentImportController extends Controller
{

    public function index()
    {
        $courses = Course::where('type', 1)->get();
        $custom_field = StudentCustomField::getData();
        return view('studentsetting::student_import', compact('courses', 'custom_field'));
    }


    public function regular()
    {
        return view('studentsetting::regular_student_import');
    }

    public function create()
    {
        return view('studentsetting::create');
    }

    public function export()
    {
        return Excel::download(new OfflineStudentExport, 'student_import.xlsx');
    }

    public function regularStudentexport()
    {
        return Excel::download(new RegularStudentExport, 'sample-student.xlsx');
    }

    public function country_list_export()
    {
        return Excel::download(new CountryLIst, 'country_list.xlsx');
    }

    public function store(Request $request)
    {
        if (saasPlanCheck('student')) {
            Toastr::error('You have reached student limit', trans('common.Failed'));
            return redirect()->back();
        }
        if (demoCheck()) {
            return redirect()->back();
        }

        $validate_rules = [
            'course' => 'required',
            'file' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        $file_type = strtolower($request->file->getClientOriginalExtension());
        if ($file_type <> 'csv' && $file_type <> 'xlsx' && $file_type <> 'xls') {
            Toastr::warning('The file must be a file of type: xlsx, csv or xls', 'Warning');
            return redirect()->back();
        } else {
            try {

                DB::beginTransaction();
                $path = $request->file('file');
                $custom_field = StudentCustomField::getData();
                Excel::import(new StudentImport($custom_field), $request->file('file'), 'local', \Maatwebsite\Excel\Excel::XLSX);


                $data = StudentImportTemporary::where('created_by', Auth::user()->id)->get();
                foreach ($data as $key => $student) {
                    $check_user = User::where('email', $student->email)->first();
                    if ($check_user == null) {
                        $new_student = new User();
                        $new_student->role_id = 3;
                        $new_student->name = $student->name;
                        $new_student->email = $student->email;
                        $new_student->username = $student->email;
                        $new_student->phone = $student->phone;
                        $new_student->dob = @$student->dob;
                        $new_student->gender = @$student->gender;
                        $new_student->country = isset($student->country) ? $student->country : Settings('country_id');
                        $new_student->job_title = @$student->job_title;
                        $new_student->company_id = @$student->company;
                        $new_student->identification_number = @$student->identification_number;
                        $new_student->password = Hash::make('12345678');
                        $new_student->created_at = date('Y-m-d h:i:s');
                        $new_student->referral = Str::random(10);
                        $new_student->email_verified_at = now();
                        $new_student->teach_via = 2;
                        $new_student->lms_id = SaasInstitute()->id;


                        $new_student->language_id = Settings('language_id');
                        $new_student->language_code = Settings('language_code');
                        $new_student->language_name = Settings('language_name');
                        $new_student->language_rtl = Settings('language_rtl');


                        $new_student->save();

                        send_email($new_student, 'Offline_Enrolled', ['email' => $new_student->email,]);

                        send_email($new_student, 'New_Student_Reg', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'name' => $new_student->name
                        ]);
                    } else {
                        $new_student = $check_user;
                    }


                    $check_enrolled = CourseEnrolled::where('user_id', $new_student->id)->where('course_id', $request->course)->first();

                    if ($check_enrolled == null) {
                        $enroll = new CourseEnrolled();
                        $enroll->user_id = $new_student->id;
                        $enroll->course_id = $request->course;
                        $enroll->purchase_price = 0.00;
                        $enroll->coupon = null;
                        $enroll->discount_amount = 0.00;
                        $enroll->save();
                    }


                }


                StudentImportTemporary::where('created_by', Auth::user()->id)->delete();

                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);

                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
    }


    public function regularStore(Request $request)
    {
        if (saasPlanCheck('student')) {
            Toastr::error('You have reached student limit', trans('common.Failed'));
            return redirect()->back();
        }
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'file' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));


        $extensions = ["xls", "xlsx"];
        $result = strtolower($request->file->getClientOriginalExtension());
        if (!in_array($result, $extensions)) {
            Toastr::error('Invalid File Extension. Allow xls,xlsx format', 'Failed');
            return redirect()->back();
        }

        $path = $request->file('file');
        Excel::import(new ImportRegularStudent(), $path, 'local', \Maatwebsite\Excel\Excel::XLSX);
        Toastr::success('Operation successful', 'Success');
        return redirect()->back();

    }


}
