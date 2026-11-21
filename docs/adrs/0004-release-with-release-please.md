# Release with release-please from Conventional Commit pull request titles

Date: 2026-11-15

_Drafted with the `adr-suggestions` AI skill._

## Status

Proposed

## Context

Releases were tagged by hand. Introduced in `0d3cbec` (#45, 2026-11-12) to automate releases, per #45: release-please opens a release pull request with the next version and `CHANGELOG.md` on pushes to `main` and `*.x` branches, and merging it tags the release.

_The reasons and alternatives above were suggested by the `adr-suggestions` AI skill from the sources cited, not written by a person. Someone who was part of the decision should check them, then remove this line._

<!-- TODO: Alternatives considered? -->

## Decision

We will release with release-please (`release-please-config.json`), which picks the next version and writes the changelog from Conventional Commit messages. Pull requests are squash-merged with their title as the commit message, and a required check (`.github/workflows/pr-title.yml`) makes every title a Conventional Commit.

## Consequences

Every contributor's pull request title decides whether and where the change appears in the release notes. release-please's PHP strategy hides `docs`, `ci`, `build`, `refactor`, `test` and `style` changes from them. Changes from before release-please are listed through an empty commit after `bootstrap-sha` (`70b523e`).

<!-- TODO: Other consequences? -->

## References

- https://github.com/googleapis/release-please-action
- https://github.com/amannn/action-semantic-pull-request
- https://www.conventionalcommits.org/
