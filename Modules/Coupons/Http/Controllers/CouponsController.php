<?php

namespace Modules\Coupons\Http\Controllers;

use App\User;
use Illuminate\Validation\Rule;
use Modules\CourseSetting\Entities\Course;
use Validator;
use App\InviteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Coupons\Entities\Coupon;
use Modules\RolePermission\Entities\Role;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\CourseSetting\Entities\Category;
use Modules\Coupons\Entities\UserWiseCouponSetting;

class CouponsController extends Controller
{

    public function invitebyCode()
    {
        $user_wise_coupons = UserWiseCoupon::all();
        $categories = Category::all();
        if (Auth::user()->role_id == 1) {
            $roles = Role::all();
        } elseif (Auth::user()->role_id == 2) {
            $roles = Role::where('id', '!=', 1)->get();
        } else {
            $roles = Role::where('id', 3)->get();
        }

        $inviteSettings = UserWiseCouponSetting::all();
        return view('coupons::invitebyCode', compact('inviteSettings', 'roles', 'user_wise_coupons', 'categories'));
    }

    public function inviteSettings()
    {

        if (Auth::user()->role_id == 1) {
            $roles = Role::all();
        } elseif (Auth::user()->role_id == 2) {
            $roles = Role::where('id', '!=', 1)->get();
        } else {
            $roles = Role::where('id', 3)->get();
        }

        $inviteSettings = UserWiseCouponSetting::get();
        return view('coupons::inviteSettings', compact('inviteSettings', 'roles'));
    }

    public function inviteSettingEdit($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        if (Auth::user()->role_id == 1) {
            $roles = Role::all();
        } elseif (Auth::user()->role_id == 2) {
            $roles = Role::where('id', '!=', 1)->get();
        } else {
            $roles = Role::where('id', 3)->get();
        }

        $edit = UserWiseCouponSetting::find($id);
        $inviteSettings = UserWiseCouponSetting::all();
        return view('coupons::inviteSettings', compact('inviteSettings', 'roles', 'edit'));
    }

    public function inviteSettingDelete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        try {
            $delete = UserWiseCouponSetting::find($id)->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function inviteSettingStore(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'max_limit' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'status' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));
        try {
            $invite_setting = UserWiseCouponSetting::where('role_id', 3)->first();
            if ($invite_setting == null) {
                $invite_setting = new UserWiseCouponSetting();
            }
            $invite_setting->role_id = 3;
            $invite_setting->type = $request->type;
            $invite_setting->status = $request->status;
            $invite_setting->amount = $request->amount;
            $invite_setting->max_limit = $request->max_limit;
            $invite_setting->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function coupon_delete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $deleted = Coupon::find($id)->delete();
            if ($deleted) {
                $coupons = Coupon::latest()->get();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);

        }
    }


    public function coupon_single(Request $request)
    {
        try {
            $categories = Category::all();
            $coupons = Coupon::with('totalUsed')->where('category', 2)->latest()->get();
            $edit = Coupon::find($request->id);
            if (!empty($edit)) {
                $subcategories = Category::where('parent_id', $edit->category_id)->get();
                $edit->subcategories = $subcategories;
                $courses = Course::where('category_id', $edit->category_id)->where('subcategory_id', $edit->subcategory_id)->get();
                $edit->courses = $courses;

            }
            return view('coupons::single_coupons', compact('edit', 'coupons', 'categories'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);
        }
    }


    public function coupon_personalized(Request $request)
    {
        try {
            $users = User::where('role_id', 3)->get();
            $coupons = Coupon::with('totalUsed')->where('category', 3)->latest()->get();
            $edit = Coupon::find($request->id);
            return view('coupons::personalized_coupons', compact('edit', 'coupons', 'users'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);
        }
    }


    public function index()
    {
        try {
            $coupons = Coupon::with('totalUsed')->latest()->get();
            return view('coupons::coupons', compact('coupons',));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);

        }
    }

    public function coupon_common()
    {
        try {
            $coupons = Coupon::with('totalUsed')->where('category', 1)->latest()->get();
            return view('coupons::common_coupons', compact('coupons'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);

        }
    }

    public function saveCoupon(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required|max:255',
            'code' => ['required', Rule::unique('coupons', 'code')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
            'type' => 'required',
            'category' => 'required',
            'value' => 'required|numeric|min:0',
            'limit' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];


        $this->validate($request, $rules, validationMessage($rules));


        try {
            $coupon = new Coupon();
            $coupon->user_id = Auth::id();
            if ($request->category) {
                $coupon->category = $request->category;
            }
            if ($request->category_id) {
                $coupon->category_id = $request->category_id;
            }
            if ($request->subcategory_id) {
                $coupon->subcategory_id = $request->subcategory_id;
            }
            if ($request->course_id) {
                $coupon->course_id = $request->course_id;
            }
            if ($request->coupon_user_id) {
                $coupon->coupon_user_id = $request->coupon_user_id;
            }
            $coupon->title = $request->title;
            $coupon->code = $request->code;
            $coupon->type = $request->type;
            $coupon->value = $request->value;
            $coupon->limit = $request->limit;

            $coupon->min_purchase = $request->min_purchase;
            $coupon->max_discount = $request->max_discount;
            $coupon->start_date = date('Y-m-d', strtotime($request->start_date));
            $coupon->end_date = date('Y-m-d', strtotime($request->end_date));
            $coupon->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {

            return response()->json(['error' => trans("lang.Operation Failed")]);

        }
    }


    public function editCoupon($id)
    {
        try {
            $edit = Coupon::find($id);
            $coupons = Coupon::with('totalUsed')->latest()->get();
            return view('coupons::coupons', compact('coupons', 'edit'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);

        }
    }


    public function updateCoupon(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required',
            'code' => ['required', Rule::unique('coupons', 'code')->ignore($request->code, 'code')->where('id', $request->id)->where('id')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
            'type' => 'required',
            'value' => 'required',
            'min_purchase' => 'required|numeric|min:0',
            'max_discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {


            $coupon = Coupon::find($request->id);
            $coupon->user_id = Auth::id();
            $coupon->title = $request->title;
            $coupon->limit = $request->limit;

            if ($request->category) {
                $coupon->category = $request->category;
            }
            if ($request->category_id) {
                $coupon->category_id = $request->category_id;
            }
            if ($request->subcategory_id) {
                $coupon->subcategory_id = $request->subcategory_id;
            }
            if ($request->course_id) {
                $coupon->course_id = $request->course_id;
            }
            if ($request->coupon_user_id) {
                $coupon->coupon_user_id = $request->coupon_user_id;
            }

            $coupon->code = $request->code;
            $coupon->type = $request->type;
            $coupon->value = $request->value;
            $coupon->min_purchase = $request->min_purchase;
            $coupon->max_discount = $request->max_discount;
            $coupon->start_date = date('Y-m-d', strtotime($request->start_date));
            $coupon->end_date = date('Y-m-d', strtotime($request->end_date));
            $coupon->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));

            if ($coupon->category == 3) {
                return redirect()->route('coupons.personalized');
            }
            if ($coupon->category == 2) {
                return redirect()->route('coupons.single');
            }
            return redirect()->route('coupons.manage');


        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed']);

        }
    }
}
