# Bing Site Scan: image ALT warning

Read-only production diagnosis, 30 October 2026.

## Confirmed source

WordPress REST `code-snippets/v1/snippets/9` reports snippet 9 active, global, version 2.0, modified 25 October 2026. The live code matches the existing v2 source from the featured-image work. GitHub still had the earlier WordPress-only implementation; commit 4b1b52c synchronizes it to the verified live version, including the Elementor Custom image-size hook. No live snippet was changed.

## HTML evidence

Four affected pages were fetched anonymously with the mandatory `tfsc_test=codex` marker:

- /beroepengids/artikel/wat-kan-ik-worden-met-een-opleiding-elektrotechniek/
- /vacature/field-service-engineer-high-tech/
- /kennisbank/wat-is-een-sorteermachine-en-hoe-werkt-deze/
- /vacature/commissioning-engineer-energy-ups-systems/

Each featured image has a nonempty ALT matching the post title. The other content images checked also have nonempty ALT text. No IMG tag on these four responses lacks the ALT attribute entirely.

The shared logo is the only image with an empty ALT on all four responses:

```html
<a href="https://thefieldservicecompany.nl">
  <img data-src="https://thefieldservicecompany.nl/wp-content/uploads/logo.svg"
       class="attachment-full size-full wp-image-11115" alt="" />
</a>
```

WordPress media REST confirms attachment 11115 has an empty `alt_text`. The parent homepage link has no text or aria-label. Bing's warning groups this empty ALT as missing alternative text.

## Resolution proposed, not applied

Set the logo's media ALT to `The Field Service Company – naar de homepage`; confirm the header renders it, invalidate affected page caches as required, and recheck HTML. If the header has a saved ALT override, correct that source too. A linked logo needs an accessible name describing its purpose. Reference: https://www.w3.org/WAI/tutorials/images/functional/.

Snippet 9 intentionally handles only the current singular post's featured image. It should not apply post titles to the global logo or every unrelated image.

This diagnosis proves the cause on the four checked pages, not on every URL in the full scan. No production content, media attributes, templates or caches were changed.

## Applied correction

On 30 October 2026, WordPress media attachment 11115 was updated through REST: `alt_text` is now `The Field Service Company – naar de homepage`.

All four pages were rechecked. Fresh HTML responses using `tfsc_test=codex&tfsc_alt_check=20261030-logo-v1` contain the corrected logo ALT and no empty or missing image ALTs. Previously cached responses using the original test URL still contain the old empty ALT. Therefore the metadata and fresh header rendering are fixed, but existing page-cache entries still require invalidation. The registered WordPress REST routes expose no LiteSpeed page-cache purge operation; no plugin code or cache configuration was changed to work around that limit.

Remaining verification: purge the site's generated HTML cache, then rerun the original URL checks. Existing Bing scan results will remain historical until rescanned.

## Homepage follow-up after user rescan

The user's homepage-only rescan still reported missing ALT. The supplied logged-in HTML includes administrative toolbar images with empty ALT; those are not served to anonymous crawlers. The public homepage separately had two other media sources with empty ALT: attachment 18679 (`service-monteur-08.webp`) and attachment 19559 (Google logo, repeated nine times in review cards). The earlier four-page verification did not cover the homepage.

Updated attachment 18679 through WordPress REST to `Service monteur werkt aan de elektrische aansluiting van een industriële motor`, after visually inspecting the image. Updated attachment 19559 to `Google`. No snippet code changed. A fresh anonymous homepage response with the test marker contains 13 IMG elements and no empty or missing ALT attributes, including all nine Google logos. Existing cached homepage responses can still require page-cache invalidation; Bing's previous scan remains historical until rescanned.

## Full priority-list verification with LiteSpeed NOCACHE

Fetched all 137 URLs from the original 30 October priority scan list, sequentially with a one-second pause between requests. Every request included `tfsc_test=codex&LSCWP_CTRL=NOCACHE`. All 137 returned HTTP 200 and `x-litespeed-cache-control: no-cache`. The homepage was separately tested with `LSCWP_CTRL=before_optm`, also returning no-cache and no empty ALT values. These checks do not purge the normal public cache or change Bing's historical results.

131 pages have no empty or missing image ALT values. Six pages still contain 13 empty ALT attributes (all attributes present, none absent):

| Page | Attachment IDs with empty ALT |
| --- | --- |
| /contact/ | 25907 |
| /over-ons/timeline/ | 27376, 11370, 11371, 11372, 27381, 27390, 27397, 27396 |
| /over-ons/wat-wij-doen/ | 25573 |
| /vacature/field-service-engineer-benelux/ | 22781 |
| /vacature/field-service-engineer-duikboten-internationaal-allround/ | 23253 |
| /vacature/internationaal-commissioning-engineer-renewable-energy/ | 18620 |

These are remaining findings, not applied fixes. Content-embedded IMG attributes may need editing in their stored content; updating media ALT alone does not necessarily update such saved HTML. Empty ALT can be intentional for decorative images, so inspect each image's purpose before changing it. No WordPress content or configuration changed during this audit.

LiteSpeed parameter documentation: https://docs.litespeedtech.com/lscache/lscwp/admin/#admin-ip-commands. NOCACHE requires Admin IP permission; acceptance here was checked using the actual response headers, not assumed from the query string alone.

## Remaining ALT repairs applied and verified

Updated 12 previously empty media ALT values after visual inspection (the existing nonempty ALT on attachment 22781 was preserved). The three vacancy IMG tags also had empty ALT saved directly in post content; these three attributes were replaced without changing titles or slugs. Elementor pages picked up the corrected media ALT values without changing their layouts or metadata.

All six affected URLs were fetched again using the test marker and LiteSpeed NOCACHE: zero empty or missing image ALTs remain in those responses. No global purge was performed. Normal cached responses and Bing's historical scan may still need refreshing.

The exact ALT mappings are descriptive of the viewed images; timeline images describe the tractor, dragrace, IVA group, industrial visit, internet café and office photos rather than using unrelated vacancy keywords.
