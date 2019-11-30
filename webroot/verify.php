<?php
define('GUEST', 23);
include_once('config.php');
include_once('helper.php');

sstart();

if (serror() || !svalid() || !snonce()) {
    http_response_code(500);
    return;
}

$dbr = db_init();
if (!$dbr) {
    http_response_code(500);
    return;
}

$code = $_GET['code'];

if (!verify($dbr, $code)) {
    http_response_code(500);
    return false;
}

return true;
