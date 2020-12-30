<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Exception;
use PHPUnit\Framework\TestCase;

class SpamTest extends TestCase
{

    /** @test */
    public function it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here.'));
    }

    /** @test */
    public function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect("Innocent reply here."));

        $this->expectException(Exception::class);

        $spam->detect('something forbidden');
    }

    /** @test */
    public function it_checks_for_any_being_held_dow()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect("Innocent reply here."));

        $this->expectException(Exception::class);

        $spam->detect('Hello word aaaaaaaaaa');
    }
}
