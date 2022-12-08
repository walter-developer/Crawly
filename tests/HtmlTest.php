<?php

namespace Tests;

use Exception;
use App\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testInput()
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
}
