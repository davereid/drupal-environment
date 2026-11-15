# Use Renovate for dependency updates

Date: 2026-11-15

_Drafted with the `adr-setup` AI skill._

## Status

Proposed

## Context

Lullabot's ADR [Use Renovate for third-party dependency updates](https://architecture.lullabot.com/adr/20251120-use-renovate/) gives a starting configuration for non-Drupal projects. That configuration is written for applications: its `"rangeStrategy": "bump"` raises the lower bound of every `composer.json` constraint on each update. This project is a Composer library, so its constraints are what its users must install: raising a lower bound forces every site using it to update that dependency too, for no benefit to the library.

This project also requires Conventional Commit pull request titles (release-please builds the changelog from them), code owner approval on every pull request, and maintains a `1.x` branch alongside `main`.

## Decision

We will use Renovate, starting from the ADR's non-Drupal configuration plus the settings from its Drupal configuration that aren't about Drupal (`group:symfony` and `composerIgnorePlatformReqs`), with these changes in `renovate.json`:

- `"rangeStrategy": "update-lockfile"`, with a Composer rule of `"widen"`, in place of `"bump"`: Composer ranges only gain new major versions (e.g. `^12` becomes `^12 || ^13`), and npm tooling only updates `package-lock.json`.
- `:semanticCommits`, so Renovate's pull request titles are Conventional Commits and pass the required title check.
- `baseBranchPatterns` covering `main` and `1.x`.
- `minimumReleaseAge` of 7 days, so a release published minutes earlier, possibly compromised or broken, isn't picked up.
- Auto-merge for minor, patch, pin, digest and lock file maintenance updates, without Renovate Approve: a bot's approval doesn't satisfy code owner review, so each update merges once the code owner approves it and the checks pass.

Dependabot security updates stay off, since Renovate opens security updates from Dependabot's alerts, which stay on.

## Consequences

The library's minimum supported versions only change when we decide to raise them, not with every dependency release. Each update still needs a code owner's approval, which is one click per pull request. Major updates, such as a new PHPUnit version, need manual review and sometimes code changes before they merge.
