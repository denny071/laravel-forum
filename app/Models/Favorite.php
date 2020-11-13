<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory,RecordsActivity;

    protected $guarded = [];

    /**
     * replies
     *
     * @return void
     */
    public function replies()
    {
        return $this->hasMany(Reply::class)
            ->withCount('favorites')
            ->with('owner');
    }

    /**
     * favorited
     *
     * @return void
     */
    public function favorited()
    {
        return $this->morphTo();
    }



}
