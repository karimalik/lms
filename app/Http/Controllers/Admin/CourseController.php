<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Subscribe;
use App\Traits\ImageStore;
use App\Traits\SendMail;
use App\Traits\SendSMS;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Coupons\Entities\Coupon;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\Course;
use Modules\SystemSetting\Entities\Blog;
use Modules\SystemSetting\Entities\Company;
use Modules\SystemSetting\Entities\FrontendSetting;
use Modules\Setting\Model\GeneralSetting;
use Modules\SystemSetting\Entities\Page;

class CourseController extends Controller
{
    use ImageStore, SendSMS, SendMail;


// START CATEGORY SECTIONS
    public function ajaxGetSubCategoryList(Request $request)
    {
        $subcategories = Category::where('parent_id', '=', $request->id)->get();
//        $subcategories = SubCategory::where('category_id', $request->id)->get();
        return response()->json([$subcategories]);
    }


    public function ajaxGetCourseList(Request $request)
    {
        $category_id = $request->category_id;
        $subcategory_id = $request->subcategory_id;
        if (Auth::user()->role_id == 1) {
            $subcategories = Course::select('id', 'title')->where('category_id', $category_id)->where('subcategory_id', $subcategory_id)->get();
        } else {
            $subcategories = Course::select('id', 'title')->where('category_id', $category_id)->where('subcategory_id', $subcategory_id)->where('user_id', Auth::user()->id)->get();
        }
        return response()->json([$subcategories]);
    }


    public function category(Request $request)
    {
        try {
            $categories = Category::orderBy('position_order', 'ASC')->with('parent')->get();
            $max_id = Category::max('position_order') + 1;

            return view('backend.categories.index', compact('categories', 'max_id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function category_delete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            Category::find($id)->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function category_edit($id)
    {
        try {
            $edit = Category::find($id);
            $categories = Category::all();
            $max_id = Category::max('position_order') + 1;
            return view('backend.categories.index', compact('categories', 'edit', 'max_id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function category_store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        // return $request;
        $rules = [
            'name' => 'required|max:255',
            'photo' => 'mimes:jpeg,jpg,png,gif,svg|max:10000',
            'thumbnail' => 'mimes:jpeg,jpg,png,gif,svg|max:10000'
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            if ($request->photo != "") {
                $url1 = $this->saveImage($request->photo);
            } else {
                $url1 = 'public/demo/category/image/1.png';
            }
            if ($request->thumbnail != "") {
                $url2 = $this->saveImage($request->thumbnail);
            } else {
                $url2 = 'public/demo/category/thumb/1.png';
            }
            DB::beginTransaction();

            $check_position = Category::where('position_order', $request->position_order)->first();

            if ($check_position != '') {
                $old_categories = Category::where('position_order', '>=', $request->position_order)->get();

                foreach ($old_categories as $old_category) {
                    $old_category->position_order = $old_category->position_order + 1;
                    $old_category->save();
                }
            }


            $store = new Category;
            $store->name = $request->name;
            $store->status = $request->status;
            $store->description = $request->description;
            if (!empty($request->parent)) {
                $store->parent_id = $request->parent;
            } else {
                $store->parent_id = null;
            }
            $store->position_order = $request->position_order;
            if (@$url1) {
                $store->image = $url1;
            }
            if (@$url2) {
                $store->thumbnail = $url2;
            }
            $store->save();
            DB::commit();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function category_status_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $store = Category::find($request->id);
            $store->status = $request->status;
            $store->save();
            return response()->json([
                'message' => 'success'
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function category_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'name' => 'required|max:255|unique:categories,name,' . $request->id,
        ];

        $this->validate($request, $rules, validationMessage($rules));


        $is_exist = Category::where('name', $request->name)->where('id', '!=', $request->id)->first();
        if ($is_exist) {
            Toastr::error('This name has been already taken', 'Failed');
            return redirect()->back();
        }


        try {
            if ($request->photo != "") {
                $url1 = $this->saveImage($request->photo);
            }
            if ($request->thumbnail != "") {
                $url2 = $this->saveImage($request->thumbnail);
            }


            $check_position = Category::where('position_order', $request->position_order)->first();

            if ($check_position != '') {
                $old_categories = Category::where('position_order', '>=', $request->position_order)->get();

                foreach ($old_categories as $old_category) {
                    $old_category->position_order = $old_category->position_order + 1;
                    $old_category->save();
                }
            }


            $store = Category::find($request->id);
            $store->name = $request->name;
            $store->status = $request->status;
            $store->url = $request->url;
            $store->title = $request->title;
            $store->description = $request->description;
            $store->show_home = $request->show_home;
            $store->position_order = $request->position_order;
            // $store->category_id = $request->category_id;
            if (@$url1) {
                $store->image = $url1;
            }
            if (@$url2) {
                $store->thumbnail = $url2;
            }

            if (!empty($request->parent)) {
                $store->parent_id = $request->parent;
            } else {
                $store->parent_id = null;
            }
            $results = $store->save();
            if ($results) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('course.category');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function coupon(Request $request)
    {
        try {
            $coupons = Coupon::all();
            return view('backend.courses.coupons', compact('coupons'));
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }











}

