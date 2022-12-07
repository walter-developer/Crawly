<?php

//Carrega o autoload do composer para uso da psr-4
$path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$autoload = $path . 'vendor' . DIRECTORY_SEPARATOR .  'autoload.php';
require_once $autoload;

//Carrega classe principal
use App\Crawly;

//Executa o crawly da aplicação
(new Crawly())->run();
