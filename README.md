# Slack SignUp and Checker

## Slack SignUp Web-App
 
### Requirements

PHP >=7.2, MySQL and a web server.

#### EVE App

- Create a new app at https://developers.eveonline.com
- Connection Type: Authentication Only
- Callback: `https://slack-signup.domain/sso.php`

#### Neucore App

- Create a new [Neucore](https://github.com/bravecollective/brvneucore) app
- Add groups: member, family
- Add roles: app-groups, app-chars

#### Slack App

- Create a Slack app at https://api.slack.com/apps
- Add permission: `chat:write:bot`
- Install app to workspace

### Install

- Create the database schema from `slack_signup.sql`.
- Copy `web_app/.env.dist` to `web_app/.env` and adjust values or create corresponding environment variables.
- In `web_app/` execute `composer install`

## Slack Checker

See [slack_checks/README.md](slack_checks/README.md).
