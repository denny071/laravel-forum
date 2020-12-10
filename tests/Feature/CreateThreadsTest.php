<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(["title" => null])
            ->assertSessionHasErrors('title');
    }

    /**
     * @test
     */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(["body" => null])
            ->assertSessionHasErrors('body');
    }

    /**
     * @test
     */
    public function a_thread_requires_a_valid_channel()
    {
        // create two channel that id is 1 and 2
       Channel::factory(2)->create();
        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');
        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }



    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = make("App\Models\Thread",$overrides);
        return $this->post("/threads",$thread->toArray());
    }

    /**
     * @test
     */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        // Given we have a signed in user
        $this->signIn();
        // When we hit the endPoint to create a new thread
        $thread = make('App\Models\Thread');
        $response = $this->post('/threads',$thread->toArray());

        // Then when we visit the thread
        // we should see the new thread
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /**
     * @test
     */
    public function guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')->assertRedirect('/login');

        $this->post('/threads')->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function guests_may_not_see_the_create_thread_page()
    {
        $this->withExceptionHandling()->get("/threads/create")->assertRedirect("/login");
    }

    /**
     * @test
     */
    public function a_thread_can_be_deleted()
    {
        $this->signIn();

        $thread = create('App\Models\Thread',['user_id' =>  auth()->id()]);

        $reply = create('App\Models\Reply',['thread_id' => $thread->id]);

        $response = $this->json('DELETE',$thread->path());
        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads',['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /**
     * @test
     */
    public function guests_cannot_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Models\Thread');

        $reponse = $this->delete($thread->path());

        $reponse->assertRedirect('/login');

    }

    /**
     * @test
     */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Models\Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /**
     * @test相似度百分之多少算侵权
     */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Models\Thread',['user_id' => auth()->id()]);
        $reply = create('App\Models\Reply', ['thread_id' => $thread->id]);


        $response =  $this->json("DELETE",$thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads',['id' => $thread->id]);
        $this->assertDatabaseMissing('replies',['id' => $reply->id]);

        // $this->assertDatabaseMissing('activities',[
        //     "subject_id" => $thread->id,
        //     "subject_type" => get_class($thread)
        // ]);

        // $this->assertDatabaseMissing('activities',[
        //     'subject_id' => $reply->id,
        //     'subject_type' => get_class($reply)
        // ]);

        $this->assertEquals(0,Activity::count());
    }
}
