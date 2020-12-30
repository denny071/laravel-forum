<?php

namespace App\Http\Controllers;

use App\Filters\ThreadsFilters;
use App\Inspections\Spam;
use App\Models\Channel;
use App\Models\Thread;
use Illuminate\Http\Request;

/**
 * 话题
 *
 * ThreadsController
 */
class ThreadsController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }


    /**
     * index 首页
     *
     * @param  mixed $channel
     * @param  mixed $filters
     * @return void
     */
    public function index(Channel $channel, ThreadsFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        if(request()->wantsJson()) {
            return $threads;
        }
        return view('threads.index',compact('threads'));
    }


    /**
     * create 创建页面
     *
     * @return void
     */
    public function create()
    {
        return view('threads.create');
    }


    /**
     * store 保存
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request, Spam $spam)
    {
        $this->validate($request,[
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);



        $thread =Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);

        return redirect($thread->path())->with('flash','你发布成功!');;
    }


    /**
     * show 详情
     *
     * @param  mixed $channelId
     * @param  mixed $thread
     * @return void
     */
    public function show($channelId,Thread $thread)
    {
        if (auth()->check() ) {
            auth()->user()->read($thread);
        }
        return view('threads.show',compact('thread'));
    }

    /**
     * destroy 删除
     *
     * @param  mixed $channel
     * @param  mixed $thread
     * @return void
     */
    public function destroy($channel,Thread $thread)
    {

        $this->authorize('update',$thread);

        $thread->delete();

        if (\request()->wantsJson()) {
            return response([],204);
        }
        return redirect('/threads');
    }

    /**
     * getThreads 获得话题
     *
     * @param  mixed $channel
     * @param  mixed $filters
     * @return void
     */
    protected function getThreads(Channel $channel, ThreadsFilters $filters)
    {

        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        $threads = $threads->get();
        return $threads;
    }
}
