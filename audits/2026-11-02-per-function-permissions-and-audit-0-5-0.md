# Combo archive permissions and audit update — 0.5.0

Installed and checked on 2 November 2026.

- Feature permissions now belong to each function. Checking proposals only counts; a separate preview and confirmation applies permissions.
- Imported legacy policy preserves the identical 1,020 active combination URLs. New/unconfigured functions do not inherit another function’s choices.
- Disabling a group keeps HTTP 200 routes with noindex/follow and removes affected combinations from the sitemap. No automatic redirect/404; native Rank Math archives remain separate.
- Audit entries expose exact changed settings, function, scope and affected URLs, with full CSV export. Historical diffs are reconstructed where snapshots exist.
- A preview-only FSE knowledge disable affected 11 URLs and left Field Service Manager unchanged; it was canceled. No feature was disabled during deployment checks.
- Targeted sitemap XML cache refresh removed older parts. Final public sitemap contains 1,020 unique function-anchored combinations, matching policy; canonical roots and pagination remain correct.

Snippet 193 includes per-function denial fallback while the manager is inactive. Snippet 148 invalidates the XML index and parts together on policy changes. Router/query snippet 186 was not changed.

[Plugin source, report and validation at deployment commit](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager/tree/2d38da62da3e5052661b3e5718b9978a4f311dca). Private backups, temporary maintenance routes and secrets are not versioned.
