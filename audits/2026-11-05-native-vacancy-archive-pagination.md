# Native vacancy archive pagination — 5 November 2026

## Cause

WordPress Reading settings use 50 posts per page. Search & Filter query 1 uses 10 and targets the vacancy post-type archive. Shared Elementor archive 25495, Loop Grid 44137129, uses Current Query. Snippet 186 synchronized the main query page size only on virtual combo routes. Native taxonomy archives therefore used the global size: Leiden showed all 40 eligible vacancies on page 1 and zero on /page/3/, with HTTP 200.

## Live change and rollback

The original snippet 186 was saved before editing and remains inactive and unchanged. Its active replacement is **snippet 406**, `Vac Combo Archives | Router & Query [v4.4] native pagination sync`. The maintained source remains `wordpress/snippets/php/186-combo-archive-base.php` for continuity.

The native vacancy taxonomy branch reads the existing Search & Filter postsPerPage setting, sets posts_per_page and posts_per_archive_page, enables row counts and preserves WordPress's parsed page number. No hardcoded page size or offset was added.

The existing shared-grid link normalizer now also handles native vacancy archives. Pretty permalinks use /page/N/; query-string taxonomy routes retain taxonomy and term arguments and use paged=N. Capture-phase navigation preserves the archive route when paging. Existing combo reset handling remains scoped to combos.

No changes to robots, canonicals, sitemaps, Rank Math, vacancy data, Elementor templates or global WordPress page size. Scope follows the existing vacancy taxonomy allowlist.

Rollback: deactivate 406 before reactivating 186, then refresh affected archive page cache. Never activate both simultaneously because the replacement contains the same PHP function names.

## Validation

- Leiden pages 1–4 return HTTP 200, ten vacancy cards each.
- All 40 vacancy IDs match the original unpaginated page, in the original order, with no duplicates or missing IDs.
- Previously empty native /page/3/ now has ten results and retains the canonical to combo /page/3/.
- Browser clicks from native page 1 to 2 to 3 preserve the native archive route and correct current page.
- Clean public Leiden root and page 3 serve the repaired results and /page/N/ links after cache refresh.
- Existing combo root and page 3 and general /vacatures/ retain ten results and pagination.
- Representative native archives checked for ten taxonomy types: education level, knowledge, work region, job activities, function, contract type, location, sector, international days and vacancy study direction. Where a second page exists, its link returns 1–10 results. A sector archive with eight results correctly has no pagination.
- Query-string native taxonomy routes tested for contract type and vacancy study direction.
- Temporary authenticated maintenance snippets 407 and 408 are inactive; their routes return 404. No forms submitted.

## Cache

Initially refreshed the vacancy post-type cache and explicit Leiden/FSE URL caches. Subsequently purged the term-archive tags for all 234 terms in the 13 allowed vacancy taxonomies, covering cached pagination as well. This used LiteSpeed's term tag constant and litespeed_purge action, in batches of 80; no global site or object-cache purge by the agent. The owner then independently purged all vacancy pages and archives; no further purge was performed afterward.

## References

- https://developer.wordpress.org/reference/classes/wp_query/#pagination-parameters
- https://docs.litespeedtech.com/lscache/lscwp/api/#litespeed_purge
- https://github.com/litespeedtech/lscache_wp/blob/master/src/tag.cls.php
