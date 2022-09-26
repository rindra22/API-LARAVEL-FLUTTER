<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        return response([
            'post' => $post->comments()->with('user:id,name,image')->get()
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
    public function store(Request $request,$id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        $validated = $request->validate([
            'comment' => 'required|string'
        ]);
        Comment::create([
            'comment' => $validated['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'messsage' => 'Comment created'
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied'
            ]);
        }

        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $validated['comment']
        ]);

        return response([
            'messsage' => 'Comment updated'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied'
            ]);
        }

        $comment->delete();

        return response([
            'messsage' => 'Comment deleted'
        ],200);
    }
}
