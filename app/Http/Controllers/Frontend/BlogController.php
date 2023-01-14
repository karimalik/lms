<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogComment;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function allBlog()
    {
        try {
            return view(theme('pages.blogs'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function blogDetails(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)->with('user', 'comments')->firstOrFail();

        try {

            if ($blog->status == 0) {
                if ($request->preview != 1) {
                    Toastr::error('Blog status is not active', 'Failed');
                    return Redirect::to('/');
                }
            }

            if (empty($request->preview)) {
                $blog->viewed = $blog->viewed + 1;
                $blog->save();
            }
            return view(theme('pages.blogDetails'), compact('blog'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function loadMoreData(Request $request)
    {
        $data = null;
        if ($request->id > 0) {
            $data = Blog::where('status', 1)->with('user')
                ->where('id', '<', $request->id)
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get();
        }

        $output = '';
        $last_id = '';

        if ($data) {
            foreach ($data as $blog) {
                $output .= view(theme('components.single-blog-post'), compact('blog'));
                $last_id = $blog->id;
            }
        }
        $result['last_id'] = $last_id;
        $result['view'] = $output;
        return $result;
    }

    public function blogCommentSubmit(Request $request)
    {
        if (!Auth::check()) {
            $validate_rules = [
                'name' => 'required',
                'email' => 'required|email',
                'comment' => 'required',
                'blog_id' => 'required',
                'type' => 'required',

            ];
        } else {
            $validate_rules = [
                'comment' => 'required',
                'blog_id' => 'required',
                'type' => 'required',
            ];
        }

        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $comment = new BlogComment();
            if (\auth()->check()) {
                $comment->user_id = \auth()->id();
            } else {
                $comment->name = $request->name;
                $comment->email = $request->email;
            }

            $comment->comment = $request->comment;
            if ($request->type != 1) {
                $comment->comment_id = $request->comment_id;
            }
            $comment->blog_id = $request->blog_id;
            $comment->type = $request->type;
            $comment->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $exception) {
            GettingError($exception->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function deleteBlogComment($id)
    {
        $comment = BlogComment::findOrFail($id);

        try {
            if ($comment->type == 1) {
                $replies = $comment->replies;
                foreach ($replies as $reply) {
                    $reply->delete();
                }
            }
            $comment->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $exception) {
            GettingError($exception->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
