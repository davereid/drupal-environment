# Follow Lullabot's architecture decisions

Date: 2026-11-15

_Drafted with the `adr-setup` AI skill._

## Status

Accepted

## Context

Lullabot records decisions that apply across its projects as Architecture Decision Records, published at https://architecture.lullabot.com. Following them keeps this project consistent with Lullabot's other projects, so people moving between them know what to expect, and saves us from making the same decisions again. Some of them don't fit this project.

## Decision

We will follow Lullabot's accepted ADRs, except those listed below. Lullabot ADRs accepted after this one apply too, unless we add them to this list.

When one of this project's own ADRs covers the same thing as a Lullabot ADR, ours takes precedence.

### Exceptions

- [Use [ticket-id]--[short-description] in feature branch names](https://architecture.lullabot.com/adr/20220920-git-branch-naming/): branches are named `<type>/<description>`, matching the Conventional Commit PR titles that release-please reads, and pull requests link their issues instead.

## Consequences

New Lullabot ADRs apply to this project as they're published. The `adr-review` skill reports any we deviate from, so we can follow them or add them to the exceptions with a reason.
