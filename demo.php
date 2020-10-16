<?php
require_once './src/phpico.php';
use boxplay\Phpico\Toico;
$ico = (new Toico('./timg.jpeg'))->createIco();
