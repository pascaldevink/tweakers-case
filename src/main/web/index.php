<?php

$startTime = microtime();

require_once realpath(dirname(__FILE__)) . '/../helper/Bootstrapper.php';

try {
    $bootstrapper = new Bootstrapper();
    $bootstrapper->init('ArticleController', 'ViewArticle');
} catch(Exception $e)
{
    var_dump($e);
}

$endTime = microtime();
$diffTime = $endTime - $startTime;

echo '<p class="load-time">Took '.$diffTime.' seconds to load</p>';