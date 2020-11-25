<?php

namespace Tests\Unit;

use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;


    public function setUp():void
    {
        parent::setUp();

        $this->thread = create("App\Models\Thread");
    }

    /**
     * @test
     */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$this->thread->replies);
    }

    /**
     * @test
     */
    public function a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\Models\User',$this->thread->creator);
    }

    /**
     * @test
     */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Models\Thread');

        $this->assertInstanceOf('App\Models\Channel', $thread->channel);
    }

    /**
     * @test
     */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Models\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}",$thread->path());
    }

    /**
     * @test
     */
    public function a_thread_has_creator()
    {
        $this->assertInstanceOf('App\Models\User',$this->thread->creator);
    }

    /**
     * @test
     */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);
        $this->assertCount(1,$this->thread->replies);
    }


    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        // Given we have a thread
        $thread =  create("App\Models\Thread");
        // And an authenticated user
        $this->signIn();
        // When thre user subscribes to the thread
        $thread->subscribe();
        // Then we should be able to fetch all threads that the use has subscribed to.
        $this->assertEquals(
            1,
            $thread->subcriptions()->where('user_id',auth()->id())->count()
        );
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        // Given we have a thread
        $thread = create('App\Models\Thread');

        // Add a user who is subscribed to the thread
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertEquals(
            0,
            $thread->subcriptions()->where('user_id',auth()->id())->count()
        );
    }
}
