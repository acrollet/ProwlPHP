<?php

include('ProwlPHP.php');
$prowl = new Prowl('APIKEY');
$prowl->push(array(
                'application'=>'Application',
                'event'=>'Event',
                'description'=>"Description",
                'priority'=>0
            ));
?>