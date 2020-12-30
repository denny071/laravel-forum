<?php

namespace Tests\Feature;

use Exception;
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

        $this->post($thread->path() . '/replies', $reply->toArray())
             ->assertStatus(422);

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
        // $this->get($thread->path())->assertSee($reply->body);
        $this->assertDatabaseHas('replies',['body' => $reply->body]);
        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    /**
     * @test
     */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->delete("/replies/{$reply->id}")->assertRedirect("login");
    }

    /**
     * @test
     */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Models\Reply',['user_id' => auth()->id()]);

        $this->delete("replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies',['id' => $reply->id]);

        $this->assertEquals(0,$reply->thread->fresh()->replies_count);

    }

    /**
     * @test
     */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->patch("/replies/{$reply->id}")->assertRedirect('login');

        $this->signIn()->patch("/replies/{$reply->id}")->assertStatus(403);

    }

    /**
     * @test
     */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create("App\Models\Reply",["user_id" => auth()->id()]);

        $updateReply = "You have been changed,foo.";

        $this->patch("/replies/{$reply->id}",["id" => $reply->id,"body" => $updateReply]);

        $this->assertDatabaseHas('replies',['id' => $reply->id, 'body' => $updateReply]);
    }



     /** @test */
     public function replies_contain_spam_may_not_be_created()
     {
         $this->signIn();

         $thread = create('App\Models\Thread');
         $reply = make('App\Models\Reply',[
            'body' =>   'something forbidden'
         ]);


         $this->post($thread->path() . '/replies',$reply->toArray())
         ->assertStatus(422);
     }

     /** @test */
     public function users_may_only_reply_a_maximum_of_once_per_minute()
     {
         $this->signIn();

         $thread = create("App\Models\Thread");
         $reply = make("App\Models\Reply",[
             'body' => "My simple reply."
         ]);

         $this->post($thread->path() . '/replies', $reply->toArray())->assertStatus(200);

         $this->post($thread->path() . '/replies', $reply->toArray())->assertStatus(422);



     }
}
