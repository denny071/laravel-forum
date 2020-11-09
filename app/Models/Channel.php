<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
        /**
     * guarded
     *
     * @var array
     */
    protected $guarded = [];

     /**
     * getRouteKeyName
     *
     * @return void
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * threads
     *
     * @return void
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
