<?php


namespace App\Models\Traits;

use App\Models\Favorite;

trait Favoritable
{

    /**
     * favorites
     *
     * @return object
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class,'favorited');
    }


    /**
     * favorite
     *
     * @return object
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if( ! $this->favorites()->where($attributes)->exists()){
            return $this->favorites()->create($attributes);
        }
    }


    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }


    /**
     * isFavorited
     *
     * @return boolean
     */
    public function isFavorited()
    {
        return !!$this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }


    /**
     * getFavoritesCountAttribute
     *
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    protected static function bootFavoritable()
{
    static::deleting(function ($model) {
        $model->favorites->each->delete();
    });
}
}
