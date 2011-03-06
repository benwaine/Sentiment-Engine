<?php

ini_set('html_errors', 0);
require_once 'bootstrap.php';

$bootstrap = $application->getBootstrap();

$sampler = new SE\Tweet\Utility\Sampler(new \Zend_Http_Client(), 'ipad', 100);

$sample = $sampler->gatherSample();

var_dump($sample);

?>
