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

if (!isset($_POST['mail']) || !filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(500);
    return;
}
$email = $_POST['mail'];

if (!invite($dbr, $email)) {
    http_response_code(500);
    return;
}
