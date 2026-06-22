# Better Member UX for MemberPress

[![Plugin checks](https://github.com/ranaweb/better-member-ux-for-memberpress/actions/workflows/plugin-checks.yml/badge.svg)](https://github.com/ranaweb/better-member-ux-for-memberpress/actions/workflows/plugin-checks.yml)
[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL_v2_or_later-blue.svg)](LICENSE)

Better Member UX for MemberPress is a free, lightweight WordPress plugin that improves MemberPress account, login, and password reset pages without replacing templates or changing form submissions.

It adds cleaner account navigation, responsive membership tables, improved profile forms, accessible authentication cards, and an optional dashboard link. It works with Gutenberg, Elementor, Beaver Builder, Divi, the Classic Editor, and custom themes because it enhances existing MemberPress frontend markup.

## Features

- Two-column MemberPress account layout with a responsive sidebar
- Optional Dashboard link and configurable navigation labels
- Renames the MemberPress Home tab to Profile by default
- Profile Information and Address Details cards
- Safe handling of custom MemberPress profile fields
- Responsive Subscriptions and Payments tables
- Cleaner login and password reset pages
- Improved empty, error, and success states
- Configurable brand colors and helper text
- No template overrides, page builder dependency, tracking, CDN, or external assets

## Installation

1. Download the latest ZIP from [GitHub Releases](https://github.com/ranaweb/better-member-ux-for-memberpress/releases).
2. In WordPress, go to **Plugins > Add New > Upload Plugin**.
3. Upload the ZIP and activate **Better Member UX for MemberPress**.
4. Go to **Settings > Better Member UX** to adjust labels, colors, and the Dashboard link.

MemberPress must be active. The enhancement script exits without changing the page when supported MemberPress markup is not present.

## Supported pages

- MemberPress Account: profile, subscriptions, payments, and change password
- MemberPress Login
- MemberPress Forgot Password / Password Reset request

Checkout, registration, restriction rules, redirects, and custom dashboards are intentionally outside the version 1 scope.

## Safe by design

The plugin moves existing DOM nodes instead of cloning form fields. MemberPress form actions, hidden fields, nonces, validation, and submission handling remain intact. It does not modify MemberPress core files, store member data, call external APIs, or load remote assets.

## Troubleshooting and compatibility reports

If a WordPress, MemberPress, theme, or page-builder update changes frontend markup, open a [compatibility bug report](https://github.com/ranaweb/better-member-ux-for-memberpress/issues/new?template=bug_report.yml). Include:

- WordPress and PHP versions
- MemberPress version
- Theme and page builder
- Affected account or authentication view
- Browser console errors and screenshots when available
- Steps that reproduce the problem

Structured reports make markup changes much faster to diagnose.

## Development

The repository uses vanilla PHP, CSS, and JavaScript with no production build process.

Every push and pull request runs PHP syntax checks across supported PHP versions plus a JavaScript syntax check. Pushing a version tag such as `v1.0.0` builds an installable plugin ZIP and publishes a GitHub Release automatically.

See [CONTRIBUTING.md](CONTRIBUTING.md) for the maintenance workflow and [CHANGELOG.md](CHANGELOG.md) for release history.

## License and trademark

Licensed under the GPL v2 or later.

MemberPress is a trademark of Caseproof, LLC. This independent plugin is not affiliated with or endorsed by MemberPress or Caseproof.
