<?php

require_once realpath(dirname(__FILE__)) . '/../helper/Bootstrapper.php';

try {
    $bootstrapper = new Bootstrapper();
    $bootstrapper->init('ArticleController', 'AddCommentSave');
} catch(Exception $e)
{
    var_dump($e);
}
