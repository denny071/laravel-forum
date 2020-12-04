<?php

namespace App\Models;

use App\Events\ThreadHasNewReply;
use App\Models\Traits\RecordsActivity;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory,RecordsActivity;

    protected $guarded = [];
    protected $with = ['creator','channel'];
    protected $appends = ['isSubscribedTo'];


    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id',auth()->id())
            ->exists();
    }

    /**
     * @return string 跳转路径
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    /**
     * 回复
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * channel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * reply
     *
     * @param $reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        $this->notifySubscribers($reply);
        // event(new ThreadHasNewReply($this, $reply));

        return $reply;
    }


    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where("user_id","!=",$reply->user_id)
            ->each
            ->notify($reply);

    }

    /**
     * boot
     */
    protected static function boot()
    {
        parent::boot();


        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

    }

    /**
     * 订阅主题
     *
     * @param int $userId
     * @return void
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }


    /**
     * 取消订阅
     *
     * @param  int $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
             ->where('user_id', $userId ?: auth()->id())
             ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }



    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

}
