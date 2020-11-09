<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;


/**
 * ProfilesController 个人中心
 */
class ProfilesController extends Controller
{

    /**
     * show
     *
     * @param  mixed $user
     * @return void
     */
    public function show(User $user)
    {
        return view('profiles.show',[
            'profileUser' => $user,
            'activities' => Activity::feed($user)
        ]);
    }


}
