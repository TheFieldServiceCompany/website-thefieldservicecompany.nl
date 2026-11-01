# header_main — vaste ankerafstand

LIVE: document 17275, container 5b8468be. Sinds 1 november 2026 staat
Elementors native `sticky_anchor_link_offset` op **90 px**. Dit houdt de
inhoudsopgavekoppen onder de bestaande sticky header. Kennisbank, Beroepengids
en Blog gebruiken dezelfde header; de artikeltemplates zijn niet gewijzigd.

Snippet 359 blijft inactief. De header verdwijnt niet meer bij scrollen.
Er is geen nieuwe scroll-JavaScript toegevoegd.

Herstel: verwijder uitsluitend sticky_anchor_link_offset of zet deze op 0
in container 5b8468be, na controle op latere wijzigingen. Ververs daarna
Elementor- en LiteSpeed-cache. De exacte eerdere meta staat in
wordpress/exports/elementor/2026-11-01-before-anchor-buffer/17275.json.

Zie audits/2026-11-01-native-anchor-buffer.md voor controles.
