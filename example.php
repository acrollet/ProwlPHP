<?php

include('ProwlPHP.php');
$prowl = new Prowl('e9fae91305527666a8a73de3f2542a299e64d9b3');
$prowl->push(array(
                'application'=>'Application',
                'event'=>'Event',
                'description'=>"Description",
                'priority'=>0
            ));
var_dump($prowl->getError());
?>