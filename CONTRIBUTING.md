# Contributing

Thanks for helping keep Better Member UX for MemberPress compatible with WordPress, MemberPress, themes, and page builders.

## Reporting a compatibility problem

Use the structured bug-report form. Include exact version numbers, the affected page, reproducible steps, and screenshots or browser-console errors when available. Remove personal member information before attaching screenshots or logs.

## Proposing a change

1. Open an issue describing the problem or feature.
2. Fork the repository and create a focused branch.
3. Preserve MemberPress form actions, nonces, hidden inputs, validation, and submission behavior.
4. Keep selectors scoped with the `bmux-` prefix.
5. Do not add template overrides, external assets, tracking, or a production build dependency.
6. Update the changelog and tests when behavior changes.
7. Open a pull request using the repository template.

## Local checks

Run the JavaScript syntax check:

```sh
node --check assets/js/frontend.js
```

Run PHP syntax checks with PHP 7.4 or newer:

```sh
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

Then test the affected MemberPress view on desktop and mobile. The `qa/smoke-fixture.html` file is useful for quick visual checks, but it does not replace testing on a real WordPress site.
