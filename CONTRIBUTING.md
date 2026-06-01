# Contributing

Thanks for taking the time to contribute — the wholesaler that ships
parcels every day with this library appreciates it too.

## Quick start

```sh
git clone git@github.com:crakter/bringapi.git
cd bringapi
composer install
composer qa            # cs + phpstan + psalm + phpunit
```

If `composer qa` is green, your patch is ready to push.

## Working on v4 vs v3

- **New code** goes under `src/v4/` (autoloaded as `Bring\Api\*`).
- **Bug fixes that also need to land for v3 users** should be applied in
  `src/Clients`, `src/Entity`, `src/DefaultData` etc. as well — those
  namespaces are marked `@deprecated` but still supported until 5.0.
- Don't introduce new features in the v3 namespace. v3 is in maintenance.

## Coding standards

- PHP **8.2** minimum, target PHP 8.5. We use `declare(strict_types=1);`
  in every file.
- PSR-12 + a few project tweaks via php-cs-fixer (see
  `.php-cs-fixer.dist.php`).
- PHPStan level **8** clean on `src/v4`.
- Psalm errorLevel **4** clean on `src/v4`.
- No magic. Prefer typed properties, readonly classes, enums.

## Tests

- Every new endpoint or HTTP behaviour change needs a test in
  `tests/v4/`. Use the `RecordingClient` helper in `tests/v4/Support/`.
- Tests must run against a mock PSR-18 client. Do **not** hit Bring's
  real API in unit tests; if you need to verify behaviour against the
  live server, add it under a `@group integration` annotation gated on
  `BRING_UID` / `BRING_API_KEY` env vars.
- Credentials never appear in fixtures. If you need a fake API key in a
  test, use a placeholder like `'k'` or `'sk-test'`.

## Commit messages

Prose, not subject-line-only. Briefly answer:
1. What problem is this fixing or what feature is it adding?
2. Why this approach, what alternatives were rejected?
3. What downstream effects should reviewers look out for?

A one-line subject is fine for trivial fixes; anything that touches the
public API should explain itself.

## Pull requests

- Branch from `main` (or `master` on the upstream repo).
- Keep PRs focused. A behaviour change + a refactor + a doc rewrite is
  three separate PRs.
- Update `CHANGELOG.md` under the `## [Unreleased]` heading.
- Update `UPGRADE-4.0.md` if the change affects the v3→v4 migration path.

## Reporting bugs

Open an issue with:
- The minimum PHP snippet that reproduces it.
- The HTTP request you expected the library to send and the one it
  actually sent (use the `RedactingLogger` — please do not paste raw
  Mybring credentials into the issue tracker).
- The PHP, library, and Guzzle versions (`composer info`).

For security issues, follow `SECURITY.md` instead — do not open a public
issue.

## Releasing

1. Update `CHANGELOG.md`: move items from `[Unreleased]` to a new
   version section.
2. Bump the version in `composer.json` if you maintain it there
   (Packagist primarily uses git tags, so the tag is what matters).
3. Tag: `git tag -a v4.0.0-rc1 -m "v4.0.0-rc1"` and push the tag.
4. GitHub release notes mirror the CHANGELOG entry.
