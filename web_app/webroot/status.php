<?php
define('GUEST', 23);
include_once('config.php');
include_once('helper.php');

sstart();

$data = [];
header('Content-Type: application/json');

if (serror() || !svalid()) {
    echo json_encode($data);
    return;
}

$dbr = db_init();
if (!$dbr) {
    echo json_encode($data);
    return;
}

$data['verify_completed'] = isVerifyCompleted($dbr, $_SESSION['character_id']);
$data['invite_locked'] = isInviteLocked($dbr, $_SESSION['character_id']);
$data['linked_character'] = getLinkedCharacter($dbr, $_SESSION['character_id']);
echo json_encode($data);
