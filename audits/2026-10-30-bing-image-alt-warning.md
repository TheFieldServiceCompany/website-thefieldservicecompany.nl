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
