<?php
$h = fopen('commonwords.txt', 'r');
$cWordAr = array();
while(($word = fgets($h)) !== FALSE)
{
   $cWordAr[] = trim($word);
}

fclose($h);

var_dump($cWordAr);
?>
