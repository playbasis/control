# Security Policy

This is a legacy Playbasis repository. Please treat security reports carefully and avoid publishing sensitive details before maintainers have had time to respond.

## Reporting A Vulnerability

Report suspected vulnerabilities privately through GitHub security advisories when available, or by contacting the repository maintainers directly.

Do not open public issues or pull requests containing:

- Live credentials or API keys.
- Webhook secrets or signing keys.
- Database dumps or customer data.
- Production hostnames that should not be public.
- Exploit steps that would put a live deployment at risk.

## Scope

Useful security reports include:

- Authentication or authorization bypasses.
- Tenant isolation failures.
- Dashboard endpoint hardening issues.
- Insecure defaults or committed secrets.
- Callback, webhook, and integration validation problems.
- Unsafe file, content, notification, user, goods, reward, or reporting flows.

## Maintenance Context

The June 2026 reset removed unsafe defaults, moved more configuration behind environment variables, and hardened public surfaces. The codebase remains legacy infrastructure, so new security work should include clear verification notes and preserve existing behavior where possible.
