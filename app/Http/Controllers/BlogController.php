<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blog_query = Blog::withCount('likes')
            ->with('user:id,name')
            ->search(request('search'))
            ->filterBy(request('filter'));
        $blogs = $blog_query->paginate(10);
        return $blogs;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:250',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->messages()
            ], 422);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/images/post'), $imageName);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->image = $imageName;
        $blog->user_id = $request->user()->id;
        $blog->save();

        return response()->json([
            'type' => 'success',
            'msg' => 'Blog Created successfully',
            'data' => $blog
        ], 200);
    }

    public function toggle_like($id, Request $request)
    {
        $blog = Blog::where('id', $id)->count();
        if ($blog) {
            $user = $request->user();
            $blogLike = BlogLike::where(['blog_id' => $id, 'user_id' => $user->id])->first();
            if ($blogLike) {
                $blogLike->delete();
                return response()->json([
                    'type' => 'success',
                    'message' => 'Like Successfully Removed!'
                ], 200);
            } else {
                $blogLike = new BlogLike();
                $blogLike->blog_id = $id;
                $blogLike->user_id = $user->id;
                $blogLike->save();
                return response()->json([
                    'type' => 'success',
                    'message' => 'Blog Successfully Liked!'
                ], 200);
            }
            return $user;
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'No Blog Found'
            ], 400);
        }
    }
}
