<?php
DEFINE('GUEST',23);

include("config.php");
include("helper.php");
sendSlack("@Test", $cfg_slack_admin);
?>