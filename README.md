# Playbasis Control

> This is a legacy Playbasis application stack. The June 2026 work focused on preserving the existing system, improving compatibility, removing unsafe defaults, hardening public surfaces, and making the repositories usable again. It was not a feature-building push or a rewrite.

Playbasis Control is the legacy admin dashboard for the Playbasis engagement stack. Operators use it to configure clients, sites, apps, rules, campaigns, rewards, goods, quests, quizzes, messages, widgets, users, reports, and integrations that are served by the companion `playbasis/api` runtime.

This repository is best understood as an admin control plane and reference implementation with substantial historical business logic. It is useful for preservation, audit, migration, adapter work, and understanding how Playbasis operators configured game mechanics.

## What This Repo Is

- A CodeIgniter-era PHP admin application backed by MongoDB configuration.
- An admin console for configuring game mechanics, player progress, campaigns, rewards, reports, and integrations.
- The legacy operator-facing companion to `playbasis/api`.
- A set of dashboard, reporting, and management views for the historical Playbasis estate.
- A modernization reference for rebuilding an admin surface around a future contract-first Playbasis Engine.

## What Admins Can Configure

The current codebase includes controllers, views, and language files for:

- Accounts, clients, sites, apps, domains, plans, packages, users, user groups, and settings.
- Actions, rules, campaigns, games, leaderboards, levels, points, custom points, badges, rewards, goods, and redemption-related workflows.
- Quests, quizzes, content, CMS-like flows, links, media, file management, widgets, and custom styles.
- Players, imports, activity/action logs, dashboards, statistics, insights, and reports.
- Merchants, store organizations, locations, goods-store reporting, and retail-style reward operations.
- Email, SMS, push, webhook, Jive, Lithium, and notification service areas.
- Docker runtime scaffolding and an older Node notification service under `notification/`.

See [docs/what-is-here.md](docs/what-is-here.md) for a goal-oriented map of the dashboard surface.

## Example Operator Workflows

The code supports admin workflows for:

- Defining customer loyalty programs with actions, points, badges, goods, and redemptions.
- Building learning or onboarding journeys with quests, quizzes, badges, and progress tracking.
- Configuring campaign and challenge systems with games, leaderboards, and rule-triggered rewards.
- Managing merchant, store, goods, and location data for retail engagement programs.
- Operating community or app engagement layers with players, messages, content, widgets, and reports.
- Reviewing healthcare, education, or operations demos that reuse engagement mechanics for structured journeys.
- Inspecting legacy configuration behavior before migrating to a newer Playbasis Engine.

See [docs/use-cases.md](docs/use-cases.md) for a fuller use-case map.

## How Control And API Fit Together

`playbasis/control` is the admin control plane. Operators configure the mechanics and content that define a Playbasis program.

`playbasis/api` is the runtime surface. Applications call the API to record player actions, apply rules, return progress, deliver rewards, process redemptions, and handle notifications or callbacks.

## Setup And Configuration

This is a legacy PHP application. Expect old framework conventions and older dependency assumptions.

The Docker entrypoint can generate local config files from sample config files and wire the app to MongoDB:

```bash
docker compose -f docker/docker-compose.yml up --build
```

The compose file starts:

- `server`, exposing ports `80` and `443`.
- `app`, mounted at `/var/www/control/`.
- `mongo`, using the `mongo:3.6` image.

Important Docker environment variables used while generating or running local config:

- `MONGO_HOSTBASE`
- `MONGO_USERNAME`
- `MONGO_PASSWORD`
- `BASE_URL`
- `DIR_IMAGE`
- `ENCRYPTION_KEY`
- `SESSION_ENCRYPT_COOKIE`
- `COOKIE_DOMAIN`
- `COOKIE_SECURE`
- `COOKIE_HTTPONLY`

Application and integration configuration is split across files under `application/config/`. Current env-backed values include:

- `CAPTCHA_PUBLIC_KEY`
- `CAPTCHA_PRIVATE_KEY`
- `DEFAULT_PASSWORD`
- `STRIPE_API_KEY`
- `STRIPE_PUBLISHABLE_KEY`
- `PAYPAL_MERCHANT_ID`
- `PAYPAL_ENV`
- `EMAIL_BCC_PLAYBASIS_EMAIL`
- `EMAIL_DEBUG_MODE`
- `FULLCONTACT_API_KEY`
- `FULLCONTACT_CALLBACK_URL`
- `GECKO_API_KEY`
- `AMAZON_SES_SECRET_KEY`
- `AMAZON_SES_ACCESS_KEY`
- `S3_KEY`
- `S3_SECRET`
- `S3_ENDPOINT`
- `S3_IMAGE`
- `TWILIO_MODE`
- `TWILIO_ACCOUNT_SID`
- `TWILIO_AUTH_TOKEN`
- `TWILIO_API_VERSION`
- `TWILIO_NUMBER`

The legacy Node notification service reads `PORT`. Other integrations may still use legacy config files or sample files and should be reviewed before deployment.

Do not commit live credentials. Use environment variables or deployment secrets for private keys and service tokens.
Set `ENCRYPTION_KEY` before using generated configs in any shared environment. `DEFAULT_PASSWORD` is intentionally blank by default; only set it if you intentionally enable the legacy sign-up flow that depends on it.

## Verification

For docs-only changes:

```bash
git diff --check
```

For PHP syntax checks:

```bash
find . -path './vendor' -prune -o -path '*/node_modules/*' -prune -o -path './system' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

For stack replay work, replay open branches onto a fresh `origin/master`, then run whitespace and PHP lint checks before merging.

## Known Limitations

- This is a legacy admin application, not a polished new SaaS product.
- The public docs are being rebuilt after a maintenance reset and may still lag some historical behavior.
- The framework, PHP assumptions, Node services, and MongoDB version reflect the age of the stack.
- Some integrations are historical and require explicit credentials or environment configuration before they are usable.
- New work should prioritize preservation, compatibility, security, documentation, tests, and migration support before feature expansion.

## Contributing And Security

See [CONTRIBUTING.md](CONTRIBUTING.md) for the preferred contribution model.

Report vulnerabilities privately. See [SECURITY.md](SECURITY.md). Do not open public issues containing live credentials, API keys, webhook secrets, database dumps, or private customer data.
