<?php
include __DIR__ . '/../vendor/autoload.php';

if (!defined('GUEST')) {
    die('go away');
}

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error_'.date('Ym').'.log');
date_default_timezone_set('UTC');

if (is_file(__DIR__ . '/../.env')) {
    $dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotEnv->load();
}

$cfg_url_base = getenv('SLACK_SIGNUP_URL_BASE');

$cfg_ccp_client_id = getenv('SLACK_SIGNUP_CCP_CLIENT_ID');
$cfg_ccp_client_secret = getenv('SLACK_SIGNUP_CCP_CLIENT_SECRET');

$cfg_user_agent = "BRAVE Slack";

$cfg_sql_url = getenv('SLACK_SIGNUP_SQL_URL');
$cfg_sql_user = getenv('SLACK_SIGNUP_SQL_USER');
$cfg_sql_pass = getenv('SLACK_SIGNUP_SQL_PASS');


$cfg_slack_admin = getenv('SLACK_SIGNUP_SLACK_ADMIN');
$cfg_slack_token = getenv('SLACK_SIGNUP_SLACK_TOKEN');
$cfg_slack_botname = getenv('SLACK_SIGNUP_SLACK_BOTNAME');


$cfg_core_api = getenv('SLACK_SIGNUP_CORE_API');
$cfg_core_app_id = getenv('SLACK_SIGNUP_CORE_APP_ID');
$cfg_core_app_secret = getenv('SLACK_SIGNUP_CORE_APP_SECRET');
