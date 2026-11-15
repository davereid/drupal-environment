# ADR review: drupal-environment

> [!NOTE]
> Generated with the `adr-review` AI skill. A person should review it: verdicts may include false positives.

- Date: 2026-11-15
- Issue: #81
- Project commit: 304a15c, updated at 66f932a after #82 resolved both deviations and raised PHPStan to level 6
- Project ADRs: `docs/adrs` (3 accepted), adoption ADR: `docs/adrs/0002-follow-lullabot-adrs.md`
- Lullabot ADRs: architecture.lullabot.com, 2026-11-15 (66 accepted)
- Results: 11 follow, 0 partial, 0 deviate, 2 overridden, 52 not applicable, 1 unable to verify

## Summary

| ID | ADR | Verdict | Evidence | Effort |
| --- | --- | --- | --- | --- |
| lullabot:20211201-use-strict-types-in-php-code | [Use strict types in PHP code](https://architecture.lullabot.com/adr/20211201-use-strict-types-in-php-code/) | Follows | `phpcs.xml.dist:8` (fixed in #82) | - |
| lullabot:20220112-type-hints | [Always use typehints in new PHP code](https://architecture.lullabot.com/adr/20220112-type-hints/) | Follows | `src/Environment.php:90`, `tests/src/SetVariablesTestTrait.php:19` (fixed in #82) | - |
| lullabot:20260320-use-phpstan | [Use PHPStan for PHP code static analysis at level 6](https://architecture.lullabot.com/adr/20260320-use-phpstan/) | Follows | `phpstan.neon.dist:2`, `.github/workflows/ci.yml:47` (since #82) | - |
| lullabot:20220920-git-branch-naming | [Use [ticket-id]--[short-description] in feature branch names](https://architecture.lullabot.com/adr/20220920-git-branch-naming/) | Overridden | `docs/adrs/0002-follow-lullabot-adrs.md` | - |
| lullabot:20251120-use-renovate | [Use Renovate for third-party dependency updates](https://architecture.lullabot.com/adr/20251120-use-renovate/) | Overridden | `docs/adrs/0003-use-renovate.md`, `renovate.json` | - |
| lullabot:20220426-use-dedicated-accounts-service-integrations | [Use dedicated accounts for service integrations](https://architecture.lullabot.com/adr/20220426-use-dedicated-accounts-service-integrations/) | Unable to verify | Integrations are configured outside the repository | - |
| lullabot:20210608-record-architecture-decisions | [Record architecture decisions](https://architecture.lullabot.com/adr/20210608-record-architecture-decisions/) | Follows | `docs/adrs/` | - |
| lullabot:20211006-avoid-command-aliases | [Avoid command aliases and short options](https://architecture.lullabot.com/adr/20211006-avoid-command-aliases/) | Follows | `composer.json` and `package.json` scripts, `.github/workflows/` | - |
| lullabot:20220207-php-unit-tests | [Use PHPUnit for unit testing PHP code](https://architecture.lullabot.com/adr/20220207-php-unit-tests/) | Follows | `phpunit.xml.dist`, `tests/src/` | - |
| lullabot:20220511-use-inclusive-language | [Use inclusive language in code and technical writing](https://architecture.lullabot.com/adr/20220511-use-inclusive-language/) | Follows | `git grep -i -E 'master\|slave\|whitelist\|blacklist'`: no matches | - |
| lullabot:20240820-project-docs | [Document project setup in README.md](https://architecture.lullabot.com/adr/20240820-project-docs/) | Follows | `README.md:1-5` | - |
| lullabot:20260410-composer-audit | [Use composer audit in automated checks](https://architecture.lullabot.com/adr/20260410-composer-audit/) | Follows | `.github/workflows/ci.yml:46` | - |
| lullabot:20260505-pin-github-actions-to-hash | [Always pin GitHub Actions to a commit hash](https://architecture.lullabot.com/adr/20260505-pin-github-actions-to-hash/) | Follows | Every `uses:` in `.github/workflows/` is a full SHA | - |
| lullabot:20260522-use-git-feature-branch-workflow | [Use the feature branch git workflow](https://architecture.lullabot.com/adr/20260522-use-git-feature-branch-workflow/) | Follows | The `main` ruleset requires a pull request for every change | - |

## Resolved deviations

Both were Partial when this review ran, and #82 resolved them.

### lullabot:20211201-use-strict-types-in-php-code: Use strict types in PHP code

- **Verdict:** Partial, resolved in #82
- **Issue:** #79
- **The ADR requires:** `declare(strict_types=1);` in every PHP file, and a CI check that catches files without it.
- **The project does:** every tracked PHP file declares strict types, but `phpcs.xml.dist:6` only uses `PSR12`, which doesn't require it, so a new file without it would pass CI.
- **Impact:** low today; a contributor's new file could silently drop strict typing.
- **Effort:** S

To resolve:

- [x] Add `<rule ref="Generic.PHP.RequireStrictTypes"/>` to `phpcs.xml.dist`, and run `vendor/bin/phpcs`.

### lullabot:20220112-type-hints: Always use typehints in new PHP code

- **Verdict:** Partial, resolved in #82
- **Issue:** #80
- **The ADR requires:** type hints on class properties, parameters and return types in new PHP code.
- **The project does:** everything is typed except `Environment::__callStatic()`'s return type (`src/Environment.php:82`) and the test trait's `$originalVariables` property (`tests/src/SetVariablesTestTrait.php:19`). Both predate the ADR.
- **Impact:** low. Adding a native return type to `__callStatic()` would break subclasses that override it without one, so it belongs in a major release; the test property can change now.
- **Effort:** S

To resolve:

- [x] Type `$originalVariables` as `array` in `tests/src/SetVariablesTestTrait.php`.
- [x] Add `: mixed` to `Environment::__callStatic()` in the next major release, and note it in the release notes. Done before 2.0.0's release, with a `BREAKING CHANGE` note.

## Overridden

- lullabot:20220920-git-branch-naming: Use [ticket-id]--[short-description] in feature branch names, branches are named `<type>/<description>` to match Conventional Commit PR titles, see `docs/adrs/0002-follow-lullabot-adrs.md`.
- lullabot:20251120-use-renovate: Use Renovate for third-party dependency updates, the project uses Renovate with a library's range strategy instead of `bump`, see `docs/adrs/0003-use-renovate.md`.

## Follows

- lullabot:20210608-record-architecture-decisions: Record architecture decisions, `docs/adrs/` in Nygard's format.
- lullabot:20211006-avoid-command-aliases: Avoid command aliases and short options, no aliases or short options in `composer.json` or `package.json` scripts or in `.github/workflows/` (`read -r` has no long form).
- lullabot:20211201-use-strict-types-in-php-code: Use strict types in PHP code, every PHP file declares it and `phpcs.xml.dist:8` requires it (since #82).
- lullabot:20220112-type-hints: Always use typehints in new PHP code, including `src/Environment.php:90` and `tests/src/SetVariablesTestTrait.php:19` (since #82).
- lullabot:20220207-php-unit-tests: Use PHPUnit for unit testing PHP code, `phpunit.xml.dist`, `tests/src/`.
- lullabot:20220511-use-inclusive-language: Use inclusive language in code and technical writing, no matches for the ADR's examples in tracked files.
- lullabot:20240820-project-docs: Document project setup in README.md, `README.md` starts with the project name and an introduction.
- lullabot:20260410-composer-audit: Use composer audit in automated checks, `.github/workflows/ci.yml:46`.
- lullabot:20260320-use-phpstan: Use PHPStan for PHP code static analysis at level 6, `phpstan.neon.dist:2`, run in CI (`.github/workflows/ci.yml:47`, `composer run lint`) and by GrumPHP (since #82).
- lullabot:20260505-pin-github-actions-to-hash: Always pin GitHub Actions to a commit hash, every `uses:` in the tracked workflows is a 40-character SHA with a version comment.
- lullabot:20260522-use-git-feature-branch-workflow: Use the feature branch git workflow, the `main` ruleset requires pull requests; `1.x` is a maintenance branch, not a git-flow branch.

## Not applicable

- lullabot:20210609-environment-indicator: [Environment Indicator](https://architecture.lullabot.com/adr/20210609-environment-indicator/), Drupal sites only.
- lullabot:20210729-stage-file-proxy: [Stage File Proxy for static files](https://architecture.lullabot.com/adr/20210729-stage-file-proxy/), Drupal sites only.
- lullabot:20211026-one-time-login-first-drupal-user-account: [Block the Drupal user 1 account in production environments](https://architecture.lullabot.com/adr/20211026-one-time-login-first-drupal-user-account/), Drupal sites only.
- lullabot:20211026-use-settings-not-splits: [Use settings.php for environment overrides and local settings](https://architecture.lullabot.com/adr/20211026-use-settings-not-splits/), Drupal sites only.
- lullabot:20211207-ddev-locals: [Use DDEV for local environments](https://architecture.lullabot.com/adr/20211207-ddev-locals/), a library with no site to run locally.
- lullabot:20211212-config-status-check: [Confirm Drupal site configuration is always in a default state](https://architecture.lullabot.com/adr/20211212-config-status-check/), Drupal sites only.
- lullabot:20220208-naming-fields: [Consistently name entity fields](https://architecture.lullabot.com/adr/20220208-naming-fields/), Drupal sites only.
- lullabot:20220320-prevent-language-change: [Lock entity language on edit forms](https://architecture.lullabot.com/adr/20220320-prevent-language-change/), Drupal sites only.
- lullabot:20220425-hide-untranslatable-fields: [Always hide non-translatable fields on translation forms](https://architecture.lullabot.com/adr/20220425-hide-untranslatable-fields/), Drupal sites only.
- lullabot:20220429-composer-exit-failure: [Break composer install if patches don't apply](https://architecture.lullabot.com/adr/20220429-composer-exit-failure/), no Composer patches.
- lullabot:20220429-composer-patch-files: [Use local copies of patch files](https://architecture.lullabot.com/adr/20220429-composer-patch-files/), no Composer patches.
- lullabot:20220429-composer-patches-inline: [Store Composer Patches configuration in composer.json](https://architecture.lullabot.com/adr/20220429-composer-patches-inline/), no Composer patches.
- lullabot:20220429-composer-patchlevel: [Configure Composer Patches to Use `-p2` as `patchLevel` for Drupal core](https://architecture.lullabot.com/adr/20220429-composer-patchlevel/), no Composer patches.
- lullabot:20220503-use-type-tray: [Use Type Tray to improve editorial UX](https://architecture.lullabot.com/adr/20220503-use-type-tray/), Drupal sites only.
- lullabot:20220504-use-simple-add-more: [Use Simple Add More to improve editorial experience](https://architecture.lullabot.com/adr/20220504-use-simple-add-more/), Drupal sites only.
- lullabot:20220519-use-claro-as-admin-theme: [Use Claro for the Drupal administration theme](https://architecture.lullabot.com/adr/20220519-use-claro-as-admin-theme/), Drupal sites only.
- lullabot:20220622-1-do-not-add-metatags-to-node-form: [Do not add the Metatag field to the node form](https://architecture.lullabot.com/adr/20220622-1-do-not-add-metatags-to-node-form/), Drupal sites only.
- lullabot:20220622-use-css-logical-properties: [Use CSS Logical Properties](https://architecture.lullabot.com/adr/20220622-use-css-logical-properties/), no front-end code.
- lullabot:20220705-use-typescript: [Use TypeScript for pure JavaScript development](https://architecture.lullabot.com/adr/20220705-use-typescript/), no front-end code.
- lullabot:20220721-organize-stylesheets-using-smacss: [Use SMACSS to organize CSS within projects](https://architecture.lullabot.com/adr/20220721-organize-stylesheets-using-smacss/), no front-end code.
- lullabot:20220816-use-js-strict-mode: [Use JavaScript strict mode](https://architecture.lullabot.com/adr/20220816-use-js-strict-mode/), no front-end code.
- lullabot:20220906-identical-cache-backends: [Use identical cache backends on development environments](https://architecture.lullabot.com/adr/20220906-identical-cache-backends/), Drupal sites only.
- lullabot:20221205-render-entities-with-view-modes: [Leverage view modes when rendering entities](https://architecture.lullabot.com/adr/20221205-render-entities-with-view-modes/), Drupal sites only.
- lullabot:20230309-use-typed-entity: [Use Typed Entity to encapsulate business logic in dedicated classes](https://architecture.lullabot.com/adr/20230309-use-typed-entity/), Drupal sites only.
- lullabot:20231027-javascript-loading-defaults: [Load JavaScript as external resource with defer attribute](https://architecture.lullabot.com/adr/20231027-javascript-loading-defaults/), no front-end code.
- lullabot:20231110-field-order-as-in-page: [Order content type form fields based on content priority](https://architecture.lullabot.com/adr/20231110-field-order-as-in-page/), Drupal sites only.
- lullabot:20240212-do-not-use-placeholders-forms: [Do not use placeholder text in form fields](https://architecture.lullabot.com/adr/20240212-do-not-use-placeholders-forms/), Drupal sites only.
- lullabot:20240221-use-svg-icons: [Use SVGs for icons](https://architecture.lullabot.com/adr/20240221-use-svg-icons/), no front-end code.
- lullabot:20240924-11ty-adr: [Use 11ty to manage the ADRs](https://architecture.lullabot.com/adr/20240924-11ty-adr/), about Lullabot's ADR website.
- lullabot:20250109-use-issue-template-patches: [Use description template for upstream patches in composer](https://architecture.lullabot.com/adr/20250109-use-issue-template-patches/), no Composer patches.
- lullabot:20250124-use-one-h1-per-page: [Use only one h1 on each page](https://architecture.lullabot.com/adr/20250124-use-one-h1-per-page/), Drupal sites only.
- lullabot:20250320-use-relative-units-in-css: [Use relative units in CSS for resizable content](https://architecture.lullabot.com/adr/20250320-use-relative-units-in-css/), no front-end code.
- lullabot:20250409-use-imagemagick-with-drupal: [Use ImageMagick with Drupal](https://architecture.lullabot.com/adr/20250409-use-imagemagick-with-drupal/), Drupal sites only.
- lullabot:20250416-avoid-using-jquery: [Avoid using jQuery](https://architecture.lullabot.com/adr/20250416-avoid-using-jquery/), no front-end code.
- lullabot:20250922-use-twig-coding-standard: [Enforce Twig coding standards with automatic linting](https://architecture.lullabot.com/adr/20250922-use-twig-coding-standard/), Drupal sites only.
- lullabot:20250924-human-readable-views-filters: [Use human-readable names as exposed filter identifiers in Views](https://architecture.lullabot.com/adr/20250924-human-readable-views-filters/), Drupal sites only.
- lullabot:20251008-use-svg-viewbox: [Use SVG view box instead of width and height](https://architecture.lullabot.com/adr/20251008-use-svg-viewbox/), no front-end code.
- lullabot:20251125-main-deployable: [The main branch of a project should always be ready to deploy to production](https://architecture.lullabot.com/adr/20251125-main-deployable/), applies to launched sites; a library has no production deployment.
- lullabot:20251210-create-deliberate-stacking-contexts: [Layer elements deliberately with stacking contexts](https://architecture.lullabot.com/adr/20251210-create-deliberate-stacking-contexts/), no front-end code.
- lullabot:20251219-self-host-third-party-static-assets: [Self-host Third-Party Static Assets](https://architecture.lullabot.com/adr/20251219-self-host-third-party-static-assets/), no front-end code.
- lullabot:20260313-drupal-build-steps: [Use 'drush deploy' when building Drupal sites](https://architecture.lullabot.com/adr/20260313-drupal-build-steps/), Drupal sites only.
- lullabot:20260313-microcontent-terminology: [Use the term "microcontent" to describe embeddable content from the same website](https://architecture.lullabot.com/adr/20260313-microcontent-terminology/), Drupal sites only.
- lullabot:20260313-use-access-unpublished: [Use `access_unpublished` for password-protected pages](https://architecture.lullabot.com/adr/20260313-use-access-unpublished/), Drupal sites only.
- lullabot:20260320-files-listing-access: [Only allow developers and site administrators access to the default Drupal files listing](https://architecture.lullabot.com/adr/20260320-files-listing-access/), Drupal sites only.
- lullabot:20260414-use-tagify: [Use Tagify for entity autocomplete widgets in Drupal](https://architecture.lullabot.com/adr/20260414-use-tagify/), Drupal sites only.
- lullabot:20260421-use-stylelint: [Use Stylelint](https://architecture.lullabot.com/adr/20260421-use-stylelint/), no front-end code.
- lullabot:20260522-reduced-motion-handling: [Respect prefers-reduced-motion for all animations and transitions](https://architecture.lullabot.com/adr/20260522-reduced-motion-handling/), no front-end code.
- lullabot:20260610-use-data-drupal-selector-in-javascript: [Use data-drupal-selector to target form elements in JavaScript](https://architecture.lullabot.com/adr/20260610-use-data-drupal-selector-in-javascript/), Drupal sites only.
- lullabot:20260626-automate-checking-for-uncommitted-files: [Automate checking for uncommitted files](https://architecture.lullabot.com/adr/20260626-automate-checking-for-uncommitted-files/), no build step writes tracked files.
- lullabot:20260703-provide-editors-image-crop-control: [Always provide editors control over image style cropping](https://architecture.lullabot.com/adr/20260703-provide-editors-image-crop-control/), Drupal sites only.
- lullabot:20260806-conditional-library-loading: [Load JavaScript libraries conditionally](https://architecture.lullabot.com/adr/20260806-conditional-library-loading/), no front-end code.
- lullabot:20260807-sdc-enforce-schema-validation: [Enforce SDC Schema Validation](https://architecture.lullabot.com/adr/20260807-sdc-enforce-schema-validation/), no front-end code.

## Unable to verify

- lullabot:20220426-use-dedicated-accounts-service-integrations: Use dedicated accounts for service integrations, check in Packagist, Qlty and the Renovate app which account each integration runs as. A sole maintainer's personal library may reasonably use the maintainer's account; release-please moving to a GitHub App token (#60) would cover its credentials.
