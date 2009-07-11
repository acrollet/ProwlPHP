<?php

include('ProwlPHP.php');

$prowl = new Prowl('APIKEY');
$prowl->push(array(
                'application'=>'Application',
                'event'=>'Event',
                'description'=>'Test message! \n Sent at ' . date('H:i:s'),
                'priority'=>0,
                //'apikey'=>'APIKEY'	// Not required if already set during object construction.
                //'providerkey'=>"PROVIDERKEY'
            ),true);

var_dump($prowl->getError());	// Optional
var_dump($prowl->getRemaining()); // Optional
var_dump(date('d m Y h:i:s', $prowl->getResetdate()));	// Optional

?>