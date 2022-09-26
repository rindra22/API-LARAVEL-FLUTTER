<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at','desc')->with('user:id,name,image')->withCount('comments','likes')->get()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'required|string'
        ]);

        $post = Post::create([
            'body' => $validated['body'],
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Post created',
            'post' => $post
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return response([
            'post' => Post::where('id',$id)->withCount('comments','likes')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ]);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied'
            ],403);
        }

        $validated = $request->validate([
            'body' => 'required|string'
        ]);


        $post->update([
            'body' => $validated['body'],
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Post updated',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ]);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied'
            ],403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted'
        ]);
    }
}
