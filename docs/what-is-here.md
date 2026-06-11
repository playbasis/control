# What Is Here

This repository contains the legacy Playbasis admin control plane. It is a preservation-focused codebase with substantial historical dashboard and configuration logic, not a new feature launch or a rewrite.

The map below describes the repo by operator goal rather than by controller name.

## Manage Tenants, Apps, And Admin Users

Use Control to manage the organizational structure behind a Playbasis program:

- Accounts, clients, domains, sites, apps, and settings.
- Plans, packages, user groups, admin users, and permissions-oriented flows.
- Dashboard and setup views used by historical Playbasis operators.

Related surfaces include `account`, `client`, `domain`, `app`, `plan`, `package`, `user`, `user_group`, `dashboard`, and `setting`.

## Configure Game Mechanics

Control is the operator surface for the legacy engagement engine:

- Actions and rules.
- Campaigns and games.
- Points, custom points, badges, levels, rewards, and leaderboards.
- Workflow and metric configuration.

Related surfaces include `action`, `rule`, `campaign`, `game`, `custompoints`, `badge`, `level`, `reward_control`, `leaderboard`, `workflow`, and `metric`.

## Build Journeys, Content, And Widgets

Operators can configure structured engagement experiences:

- Quests, quizzes, and journey-like flows.
- Content, CMS-like pages, links, media, and file management.
- Widgets and custom styling.

Related surfaces include `quest`, `quiz`, `content`, `cms`, `link`, `mediamanager`, `filemanager`, `widget`, and `custom_style`.

## Manage Players And Imports

Control includes surfaces for inspecting and managing people and activity:

- Player lists and player-oriented management views.
- Imports and action data logs.
- Registration, referral, reward, quest, quiz, goods, and custom point reports.

Related surfaces include `player`, `import`, `action_data_log`, `report_*`, `statistic`, `statistics`, and `insights`.

## Operate Goods, Merchants, And Stores

The dashboard contains retail and redemption-oriented admin flows:

- Goods and reward goods.
- Merchants and store organizations.
- Locations and store-aware reporting.
- Goods, gift, goods-store, and redemption-adjacent reports.

Related surfaces include `goods`, `merchant`, `store_org`, `location`, `report_goods`, `report_goods_store`, and `report_gift`.

## Send Messages And Manage Integrations

Control includes admin areas for legacy messaging and integration setup:

- Email, SMS, push, and webhook management.
- Jive and Lithium integration surfaces.
- Node notification service files.
- Amazon SES and S3 config files.

Related surfaces include `email`, `sms`, `push`, `webhook`, `jive`, `lithium`, `notification/`, and config files under `application/config/`.

## What This Is Not

- It is not a clean-room rewrite.
- It is not a current SaaS launch.
- It is not a guarantee that every historical integration works without fresh credentials and environment-specific setup.
- It is not the only recommended shape for a modern Playbasis admin product.

Treat it as a maintained legacy reference: useful to run, inspect, harden, document, migrate from, and compare against future implementations.
