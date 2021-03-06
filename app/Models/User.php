<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory,RecordsActivity,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



     /**
     * getRouteKeyName
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }


    /**
     * threads
     *
     * @return object
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    /**
     * lastReply
     *
     * @return void
     */
    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }


    /**
     * activity
     *
     * @return object
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }


    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            \Carbon\Carbon::now()
        );
    }

    /**
     * visitedThreadCacheKey 阅读话题缓存key
     *
     * @param  mixed $thread
     * @return void
     */
    public function visitedThreadCacheKey($thread)
    {
        return $key = sprintf("users.%s.visits.%s", $this->id,$thread->id);
    }
}
