<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Inspections\Spam;
use App\Models\Thread;


class RepliesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }



    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }



    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param integer $channelId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($channelId,Thread $thread, Spam $spam)
    {
        try {
            $this->validate(request(), ["body" => 'required|spamfree']);

            $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id(),
            ]);


        } catch (\Exception $e){
            return response(
                'Sorry,your reply could not be saved at this time.',422
            );
        }

        return $reply->load('owner');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $reply,Spam $spam)
    {
        $this->authorize('update',$reply);

        try{
            $this->validate(request(), ["body" => 'required|spamfree']);

            $reply->update(request(['body']));
        }catch (\Exception $e){
            return response(
                'Sorry,your reply could not be saved at this time.',422
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();


        if (request()->expectsJson()){
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }


}
