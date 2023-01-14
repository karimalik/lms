<?php

namespace Modules\Blog\Http\Controllers;


use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;


class BlogController extends Controller
{


    public function index(Request $request)
    {

        try {
            $user = Auth::user();
            $query = Blog::with('user');
            if ($user->role_id != 1) {
                $query->where('user_id', $user->id);
            }

            $blogs = $query->latest()->get();
            $query2 = BlogCategory::with('user');
            if ($user->role_id != 1) {
                $query->where('user_id', $user->id);
            }
            $categories = $query2->where('status', 1)->latest()->get();

            return view('blog::index', compact('blogs', 'categories'));

        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function store(Request $request)
    {
        if (saasPlanCheck('blog_post')) {
            Toastr::error('You have reached blog post limit', trans('common.Failed'));
            return redirect()->back();
        }
        if (demoCheck()) {
            return redirect()->back();
        }


        $rules = [
            'title' => 'required',
            'slug' => ['required', Rule::unique('blogs', 'slug')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
            'description' => 'required',
            'image' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = new Blog;
            $blog->title = $request->title;
            $blog->slug = $request->slug;
            $blog->description = $request->description;
            $blog->category_id = $request->category;
            $blog->tags = $request->tags;
            $blog->user_id = Auth::id();
            $blog->authored_date = !empty($request->publish_date) ? $request->publish_date : date('m/d/y');

            if ($request->image) {

                if (!File::isDirectory('public/uploads/blogs/')) {
                    File::makeDirectory('public/uploads/blogs/', 0777, true, true);
                }
                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->image = 'public/uploads/blogs/' . $name;

                $strpos = strpos($request->image, ';');
                $sub = substr($request->image, 0, $strpos);
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->thumbnail = 'public/uploads/blogs/' . $name;


            }
            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }


    public function show($id)
    {
        return view('blog::show');
    }


    public function edit($id)
    {
        return view('blog::edit');
    }

    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required',
            'id' => 'required',
            'slug' => ['required', Rule::unique('blogs', 'slug')->ignore($request->slug, 'slug')->when(isModuleActive('LmsSaas'), function ($q, $request) {
                return $q->where('lms_id', app('institute')->id)->where('id', '!=', $request->id);
            })],
            'description' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {


            $blog = Blog::find($request->id);
            $blog->title = $request->title;
            $blog->slug = $request->slug;
            $blog->description = $request->description;
            $blog->user_id = Auth::id();
            $blog->authored_date = !empty($request->publish_date) ? $request->publish_date : date('m/d/y');
            $blog->tags = $request->tags;
            $blog->category_id = $request->category;
            if ($request->image) {


                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->image = 'public/uploads/blogs/' . $name;


                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
                $upload_path = 'public/uploads/blogs/';
                $img->save($upload_path . $name);
                $blog->thumbnail = 'public/uploads/blogs/' . $name;


            }


            $blog->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }


    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'id' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $blog = Blog::findOrFail($request->id);
            $blog->delete();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }
}
