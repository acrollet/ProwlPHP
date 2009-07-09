<?php

include('ProwlPHP.php');
$prowl = new Prowl('APIKEY');
$prowl->post('Application', 'Event', 'Description');

?>