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

For PHP changes:

```bash
find . -path './vendor' -prune -o -path '*/node_modules/*' -prune -o -path './system' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

For first-party application work, it is acceptable to run a narrower lint pass that also excludes `application/libraries`:

```bash
find . -path './vendor' -prune -o -path '*/node_modules/*' -prune -o -path './system' -prune -o -path './application/libraries' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

This repository contains bundled legacy adapters such as Google, Sentry/Raven, Twilio, and other third-party libraries. Syntax errors are blockers. Deprecation warnings from untouched bundled adapters should be recorded as compatibility debt, but they should not block unrelated preservation, security, documentation, or setup fixes. If a PR changes or depends on a bundled adapter, lint that adapter directly and document the runtime/config assumptions.

For runtime work, prefer a fresh Docker start:

```bash
docker compose -f docker/docker-compose.yml up --build
```

## Security

Do not commit live credentials, private keys, customer data, webhook secrets, database dumps, or environment-specific production config.

Report vulnerabilities privately using the process in [SECURITY.md](SECURITY.md).
