<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Models\Thread');
        $reply = make('App\Models\Reply', ['body' => null]);

        $this->post($thread->path()."/replies",$reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthenticated_user_may_no_add_replies()
    {
        $thread = create('App\Models\Thread');
        $reply = create('App\Models\Reply');
        $this->withExceptionHandling()
            ->post($thread->path().'/replies',$reply->toArray())
            ->assertRedirect("login");
    }

    /**
     * @test
     */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {

        // Given we have a authenticated user  already login user
        $this->signIn();
        // no login user
//        $user = factory('App\Models\User')->create();
        // And an existing thread
        $thread = create('App\Models\Thread');

        // When the user adds a reply to the thread
        $reply = make('App\Models\Reply');

//        dd($thread->path().'/replies');

        $this->post($thread->path() . '/replies', $reply->toArray());

        // Then their reply should be visible on the page
        $this->get($thread->path())->assertSee($reply->body);
    }
}
