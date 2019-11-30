<?php
DEFINE('GUEST',23);

include("config.php");
include("helper.php");

// enable to test the Slack app
#sendSlack("@Test", $cfg_slack_admin);
