<?php

namespace Tests;

use Exception;
use App\Crawly;
use PHPUnit\Framework\TestCase;

class CrawlyTest extends TestCase
{
    public function testRun()
    {
        $crawly = new Crawly();
        try {
            $this->assertTrue($crawly->run());
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function testFindTokenAnswer()
    {
        $crawly = new Crawly();
        try {
            $token = '53uu808x4u492u8xzuv2w7w444667u21';
            $this->assertTrue($crawly->findTokenAnswer($token));
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}
