<?php
namespace App\Filters;

use App\Models\User;

class ThreadsFilters extends Filters
{
    protected $filters = ['by','popularity','unanswered'];

    /**
     * filter username
     *
     * @param $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * popularity
     *
     * @return void
     */
    public function popularity()
    {
        $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count','desc');

    }

    public function unanswered()
    {
        return $this->builder->where('replies_count',0);
    }
}
