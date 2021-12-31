<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Comment;

class CommentController extends Controller
{
    
    /**
     * Get Comments
     *
     * @return Comments
     */
    public function index()
    {
        //
        $comments = Comment::where('reply_id', 0)->get();

        $commentsData = [];

        foreach ($comments as $key) {
            $replies = $this->replies($key->id);

            array_push($commentsData,[
                "name" => $key->username,
                "commentid" => $key->id,
                "comment" => $key->comment,
                "replies" => $replies,
                "date" => $key->created_at->toDateTimeString()
            ]);
        }
        $collection = collect($commentsData);
        return $collection->sortBy('date');
    }

    protected function replies($commentId)
    {
        $comments = Comment::where('reply_id', $commentId)->get();
        $replies = [];

        foreach ($comments as $key) {
            array_push($replies,[
                "name" => $key->username,
                "commentid" => $key->id,
                "comment" => $key->comment,
                "date" => $key->created_at->toDateTimeString()
            ]);
        }
        
        $collection = collect($replies);
        return $collection->sortBy('date');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        'comment' => 'required',
        'reply_id' => 'filled',
        'username' => 'required',
        ]);

        $comment = Comment::create($request->all());

        if($comment)
            return [ "status" => "true","commentId" => $comment->id ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $commentId
     * @param  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}