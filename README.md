# Slack SignUp and Checker

## Slack Checker

See [slack_checks/README.md](slack_checks/README.md).

## Slack SignUp Web-App
 
### Requirements

PHP, MySQL and a web server.

#### EVE App

- Create a new app at https://developers.eveonline.com
- Connection Type: Authentication Only
- Callback: `https://slack-signup.domain/sso.php`
- Set `$cfg_ccp_client_id` and `$cfg_ccp_client_secret` in webroot/config.php.

#### Neucore App

- Create a new app
- Add groups: member, family
- Add roles: app-groups
- Set `$cfg_core_api`, `$cfg_core_app_id` and `$cfg_core_app_secret` in webroot/config.php.

#### Slack App

- Create a Slack app at https://api.slack.com/apps
- Add permission: `chat:write:bot`
- Install app to workspace
- Set `$cfg_slack_admin`, `$cfg_slack_token` and `$cfg_slack_botname` in webroot/config.php.

### Install

- Set `$cfg_url_base` in webroot/config.php.
- Set `$cfg_sql_url`, `$cfg_sql_user` and `$cfg_sql_pass` in webroot/config.php.
- Create the database schema from `slack_signup.sql`, the `account` table is no longer needed.

#### Cron

cron.php is not used anymore, it's replaced by [slack_checks](slack_checks).
