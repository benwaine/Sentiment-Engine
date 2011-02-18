<?php
$pdo = new PDO('sqlsrv:server=(local)\sqlexpress,3306;Database=sentiment_engine');
$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
