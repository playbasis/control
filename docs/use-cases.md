# Use Cases

These use cases describe existing primitives in the legacy Playbasis stack. They are not promises of turnkey production deployments. Any production use should include environment-specific security review, integration testing, and deployment hardening.

## Loyalty And Rewards Programs

Configure customer actions, points, badges, goods, and redemption workflows.

Existing primitives:

- Action, rule, point, custom point, badge, reward, and level configuration.
- Goods, merchants, store organizations, and locations.
- Player views and reporting surfaces.
- Email, SMS, push, and webhook areas for follow-up workflows.

## Learning, Onboarding, And Training Journeys

Configure structured journeys where people complete tasks, quizzes, and milestones.

Existing primitives:

- Quest and quiz configuration.
- Actions for completion events.
- Badges, points, levels, rewards, and player progress.
- Content, links, widgets, and reports.

## Campaign And Challenge Systems

Operate time-bound or behavior-triggered engagement programs.

Existing primitives:

- Campaigns, games, rules, and leaderboards.
- Actions and workflow configuration.
- Rewards, badges, levels, and points.
- Dashboards, statistics, insights, and reports.

## Retail, Merchant, And Store Engagement

Manage location-aware rewards and store-oriented operations.

Existing primitives:

- Merchant and store organization management.
- Goods, goods-store reports, gifts, and reward configuration.
- Location setup and branch-style modeling.
- Player and activity reporting for operations teams.

## Community Or App Engagement Layers

Operate an engagement layer around an application or community.

Existing primitives:

- Player management.
- Actions, rules, points, badges, rewards, and leaderboards.
- Content, media, widgets, custom styles, and links.
- Email, SMS, push, webhook, Jive, and Lithium surfaces.
- Reports and insights for operators.

## Healthcare, Education, Or Operations Demos

Use the legacy mechanics as reusable engagement primitives for structured workflows.

Existing primitives:

- Quests and quizzes for checklists or learning paths.
- Actions for appointment, task, lesson, or workflow completion.
- Points, badges, rewards, and progress state.
- Reports for follow-through, participation, and outcomes.
- Admin configuration that can demonstrate regulated workflow patterns after security review.

## Migration And Modernization Reference

Use the admin codebase as a domain reference when rebuilding a modern Playbasis control plane.

Existing primitives:

- Historical admin workflows and language.
- Configuration patterns for rules, campaigns, rewards, goods, quests, quizzes, widgets, and reports.
- Legacy integration and notification assumptions.
- Docker/runtime repair work from the June 2026 maintenance reset.
- Regression and lint practices used to keep behavior stable during cleanup.
