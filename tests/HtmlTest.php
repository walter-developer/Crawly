<?php

namespace Tests;

use Exception;
use App\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testRun()
    {
        $htmlText = '<form action="/" method="post" id="form">
                    <input type="hidden" name="token" id="token" value="913z5908z57246876143v3931057v18y">
                    <input type="button" value="Descobrir resposta" onclick="findAnswer()">
                 </form>';
        $html = new Html($htmlText);
        try {
            $this->assertTrue(count($html->input('text', true) ?: []));
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function testFindTokenAnswer()
    {
        $crawly = new Crawly();
        try {
            $token = '53uu808x4u492u8xzuv2w7w444667u21';
            $this->assertTrue($crawly->text('span'));
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}
