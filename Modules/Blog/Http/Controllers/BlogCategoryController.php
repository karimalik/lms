<?php

namespace Modules\Blog\Http\Controllers;


use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Modules\Blog\Entities\BlogCategory;
use Modules\CourseSetting\Entities\Category;


class BlogCategoryController extends Controller
{


    public function index()
    {

        try {
            $user = Auth::user();
            $query = BlogCategory::with('user');
            if ($user->role_id != 1) {
                $query->where('user_id', $user->id);
            }

            $categories = $query->latest()->get();
            $max_id = Category::max('position_order') + 1;
            return view('blog::category', compact('categories', 'max_id'));

        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'title' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = new BlogCategory;
            $blog->title = $request->title;
            $blog->parent_id = $request->parent;
            $blog->position_order = $request->position_order;
            $blog->user_id = Auth::id();
            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $edit = BlogCategory::find($id);
        $query = BlogCategory::with('user');
        if ($user->role_id != 1) {
            $query->where('user_id', $user->id);
        }

        $categories = $query->latest()->get();
        $max_id = BlogCategory::max('position_order') + 1;
        return view('blog::category', compact('categories', 'max_id', 'edit'));

    }


    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'title' => 'required',
            'id' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {


            $blog = BlogCategory::find($request->id);
            $blog->title = $request->title;
            $blog->parent_id = $request->parent;
            $blog->position_order = $request->position_order;


            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }


    public function destroy($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $blog = BlogCategory::findOrFail($id);

        try {
            $blog->delete();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }
}
