<?php

namespace App\Http\Controllers;

use App\Models\Reply;


/**
 * FavoritesController
 */
class FavoritesController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }
    /**
     * store 收藏
     *
     * @param  mixed $reply
     * @return void
     */
    public function store(Reply $reply)
    {
        $reply->favorite();

        return back();
    }
}
