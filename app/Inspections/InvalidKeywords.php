<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords
{
    protected $keywords = [
        'something forbidden'
    ];

    public function detect($body)
    {
        foreach($this->keywords as $invalidkeyword) {
            if (stripos($body, $invalidkeyword) !== false) {
                throw new Exception("Your reply contains spam.");
            }
        }
    }
}
