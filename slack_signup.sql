CREATE DATABASE `slack_signup` /*!40100 DEFAULT CHARACTER SET utf8 */;

CREATE TABLE `account`
(
    `slack_id`                  varchar(9) NOT NULL,
    `slack_username`            text                DEFAULT NULL,
    `slack_realname`            text                DEFAULT NULL,
    `character_id`              int(11)             DEFAULT NULL,
    `character_name`            text                DEFAULT NULL,
    `corporation_id`            int(11)             DEFAULT NULL,
    `corporation_name`          text                DEFAULT NULL,
    `alliance_id`               int(11)             DEFAULT NULL,
    `alliance_name`             text                DEFAULT NULL,
    `faction_id`                int(11)             DEFAULT NULL,
    `faction_name`              text                DEFAULT NULL,
    `core_tags`                 text                DEFAULT NULL,
    `core_groups`               text                DEFAULT NULL,
    `core_perms`                text                DEFAULT NULL,
    `auth_code`                 text                DEFAULT NULL,
    `created_at`                int(11)    NOT NULL DEFAULT 0,
    `updated_at`                int(11)    NOT NULL DEFAULT 0,
    `completed_at`              int(11)    NOT NULL DEFAULT 0,
    `verify_reminder_at`        int(11)    NOT NULL DEFAULT 0,
    `verify_delete_reminder_at` int(11)    NOT NULL DEFAULT 0,
    `name_started_at`           int(11)    NOT NULL DEFAULT 0,
    `name_reminder_at`          int(11)    NOT NULL DEFAULT 0,
    `name_delete_reminder_at`   int(11)    NOT NULL DEFAULT 0,
    `left_started_at`           int(11)    NOT NULL DEFAULT 0,
    `left_reminder_at`          int(11)    NOT NULL DEFAULT 0,
    `left_delete_reminder_at`   int(11)    NOT NULL DEFAULT 0,
    PRIMARY KEY (`slack_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `invite`
(
    `character_id`   int(11) NOT NULL,
    `character_name` text    NOT NULL,
    `email`          text    NOT NULL,
    `email_history`  text DEFAULT NULL,
    `invited_at`     int(11) NOT NULL,
    `slack_id`       text DEFAULT NULL,
    `account_status` text DEFAULT NULL,
    PRIMARY KEY (`character_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
