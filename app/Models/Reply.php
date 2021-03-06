<?php

namespace App\Models;

use App\Models\Traits\Favoritable;
use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;
    use Favoritable,RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner','favorites'];
    protected $appends = ['favoritesCount','isFavorited'];


    public function owner()
    {
        return $this->belongsTo(User::class,"user_id");
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    protected static function boot()
    {
        parent::boot(); //

        static::created(function ($reply){
           $reply->thread->increment('replies_count');
        });

        static::deleted(function ($reply){
            $reply->thread->decrement('replies_count');
        });
    }

}

