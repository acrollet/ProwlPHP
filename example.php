<?php

include('ProwlPHP.php');

$prowl = new Prowl('APIKEY');
$prowl->push(array(
                'application'=>'Application',
                'event'=>'Event',
                'description'=>"Description",
                'priority'=>0
            ));

var_dump($prowl->getError());	// Optional
var_dump($prowl->getRemaining()); // Optional
var_dump(date('d m Y h:i:s', $prowl->getResetdate()));	// Optional

?>