<?php

namespace App\Models;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * thread 主题
     *
     * @return void
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * notify 通知
     *
     * @param  mixed $reply
     * @return void
     */
    public function notify($reply)
    {
        return $this->user->notify(new ThreadWasUpdated($this->thread, $reply));
    }
}
