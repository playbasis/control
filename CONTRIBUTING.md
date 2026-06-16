# Contributing

This repository is a legacy Playbasis application. Contributions should prioritize preservation, compatibility, security, setup documentation, tests, and migration support over new feature expansion.

## Preferred Contributions

- Fixes that preserve existing behavior while improving compatibility.
- Security hardening for dashboard endpoints, callbacks, tenant checks, and secret handling.
- Documentation that explains real admin workflows, setup steps, and configuration.
- Tests or replay scripts for legacy behavior.
- Adapter, migration, or contract documentation that helps future modernization.
- Small, reviewable patches with clear verification output.

## Pull Request Expectations

Every pull request should include:

- A short behavior-preservation note.
- Commands run for verification, including `git diff --check` and PHP lint when PHP files change.
- Any config, environment variable, secret, migration, or deployment impact.
- Notes about any compatibility assumptions, especially PHP, MongoDB, CodeIgniter, Docker, and Node versions.

Avoid broad refactors unless they are required to make a focused fix safe.

## Local Verification

For docs-only changes:

```bash
git diff --check
```

For first-party PHP changes:

```bash
find . -path './vendor' -prune -o -path '*/node_modules/*' -prune -o -path './system' -prune -o -path './application/libraries' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

For adapter work, lint the adapter code that changed or is required by the behavior under review:

```bash
php -l application/libraries/path/to/adapter.php
```

This repository contains bundled legacy adapters such as Google, HTMLPurifier, Sentry/Raven, Twilio, and other third-party libraries. Those adapters are optional compatibility surfaces, not default blockers for unrelated preservation work. Do not require Twilio, Google, Sentry, HTMLPurifier, or similar bundled libraries for a PR unless the changed path uses them, autoloads them, or changes their integration behavior. Syntax or deprecation issues in untouched optional adapters should be recorded as compatibility debt; they should not block unrelated preservation, security, documentation, or setup fixes.

For runtime work, prefer a fresh Docker start:

```bash
docker compose -f docker/docker-compose.yml up --build
```

## Security

Do not commit live credentials, private keys, customer data, webhook secrets, database dumps, or environment-specific production config.

Report vulnerabilities privately using the process in [SECURITY.md](SECURITY.md).
