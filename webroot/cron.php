<?php

if (php_sapi_name() !== 'cli') { die("go away"); }

define('GUEST', 23);
include_once('config.php');
include_once('helper.php');

# -----------------------------------------------------------------------------------------------------------------------

$sleep = 60 * 60 * 1;

while(1) {

    try {
	print("---- Cycle START\n");
	refresher();
	print("---- Cycle STOP\n");
    } catch (Exception $e) {
	print_r($e);
	var_dump($e->getMessage());
    }
    sleep($sleep);

}

?>
