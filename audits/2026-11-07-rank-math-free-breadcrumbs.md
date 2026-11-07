# Rank Math Free en breadcrumbs — tussenstand 7 november 2026

Bij het uitschakelen van Rank Math Pro verdwenen de zichtbare breadcrumbs. De live controle daarna liet zien dat Pro weer actief was en de breadcrumbs weer zichtbaar waren. De Elementor-templates gebruiken het Rank Math Breadcrumbs-widget. Dat widget vereist Rank Math Pro. Rank Math Free kan de kruimels genereren via `[rank_math_breadcrumb]`. [Documentatie van Rank Math](https://rankmath.com/kb/breadcrumbs/)

## Wat de site nu doet

- **Snippet 113** maakt de virtuele tussenarchieven van de Beroepengids en registreert aparte Elementor-templatevoorwaarden voor de tussenarchieven en detailarchieven. **Snippet 199 v3.5** bepaalt de aangepaste kruimels. De actieve versie komt overeen met de [GitHub-export](../wordpress/exports/code-snippets/2026-10-17/all/rewrite-breadcrumbs-met-taxonomy-slug-v3-5.code-snippets.php).
- **Snippet 99** bouwt het kruimelpad voor de vacature-comboarchieven met de routegegevens van de actieve router **406**. Snippet **193** stemt daarnaast de breadcrumbverwijzingen in de gestructureerde gegevens af op de combo-URL. [Snippet 99](../wordpress/snippets/php/99-rank-math-combo-archive-breadcrumbs.php) · [Snippet 193](../wordpress/snippets/php/193-combo-archive-seo.php)
- De shortcode van Rank Math Free 1.0.281 roept dezelfde `rank_math/frontend/breadcrumb/items`-filter aan waarop 99 en 199 inhaken. Dit is in de broncode gecontroleerd. De vergelijking op de site met alleen Free staat nog open.

| Live voorbeeld | Zichtbaar pad op 7 november |
| --- | --- |
| [Tussenarchief Functienamen](https://thefieldservicecompany.nl/beroepengids/functienamen/) | Home › Beroepengids › Functienamen |
| [Detailarchief Field Service Engineer](https://thefieldservicecompany.nl/beroepengids/functienamen/field-service-engineer/) | Home › Functienamen › Field Service Engineer. Functienamen linkt terug naar het tussenarchief. |
| Beroepengids-artikel | Home › Beroepengids › Artikelen › artikeltitel. Artikelen linkt naar `/beroepengids/artikelen/`. |
| [Vacature-comboarchief](https://thefieldservicecompany.nl/vacatures/functienaam/commissioning-engineer/bedrijfssector/energie/) | Home › Vacatures › Commissioning Engineer › Energie. Commissioning Engineer linkt naar het bovenliggende vacaturearchief. |

Het detailarchief heeft in de huidige weergave geen afzonderlijke kruimel ‘Beroepengids’. Gebruik het bestaande pad als referentie voor de overstap. Een extra kruimel zou een aparte inhoudelijke wijziging zijn.

## Omvang en opmaak

- Op openbare pagina’s bevestigd: **10 gedeelde Elementor-templates en 2 losse pagina’s** (Beroepengids en Voor Werkgevers), samen **13 widgets**. Template **25495** bedient het vacatureoverzicht, de gewone vacaturefilters en de comboarchieven en bevat twee breadcrumb-widgets. De Beroepengids gebruikt **24205** voor de tussenarchieven en **24908** voor de detailarchieven.
- Templates **28197** en **27450** bevatten ook een breadcrumb-widget. Live gebruik daarvan is nog niet bevestigd.
- De algemene CSS op `.rank-math-breadcrumb` kan blijven werken met dezelfde uitvoer van Free. Widgetgebonden opmaak moet mee naar de vervanger. Op het blog zijn linkkleur, onderrand en marge aan het huidige widget gekoppeld. Behoud bestaande klassen zoals `tfsc-breadcrumb-scroll` en `broodkruimel` waar ze worden gebruikt en vergelijk desktop en mobiel.

## Aanvulling na hervatting op 7 november

De eigenaar heeft gevraagd om een eigen uitbreiding waarmee de bestaande widgets kunnen blijven werken. Daarvoor is [TFSC Rank Math Breadcrumbs 0.1.0](../wordpress/plugins/tfsc-rank-math-breadcrumbs/README.md) ontwikkeld. Die registreert hetzelfde Elementor-widgettype `breadcrumbs` zodra Rank Math Pro uitstaat en gebruikt de bestaande Free-renderer. De shortcodevervanging hieronder is daarmee een terugvaloptie.

De uitbreiding is inmiddels als versie 0.3.1 geïnstalleerd en actief. Naast het widget bewaart die de bestaande About/Mentions-markeringen, queryparameterredirect en lokale sitemap. De automatische redirectopruiming gebruikt nu ondertekende serverbewijzen; zie de [beveiligingsreview](2026-11-07-rank-math-security-review.md). De [eerdere live proef met Pro uit](2026-11-07-rank-math-free-compatibility-test.md) is geslaagd. Het [functionele vervolgverslag](2026-11-07-rank-math-redirect-cleanup-and-job-locations.md) beschrijft de opruiming en de bevestigde onafhankelijke vacaturelocaties. Pro blijft uit. Zoekintentieanalyse is uitgezet; SEO Analyzer blijft op verzoek aan.

## Eerder voorgestelde shortcodevervanging

1. Leg de huidige breadcrumbs vast en maak een herstelkopie van de templates. Begin met **25495** en **24908**. Vervang daar bij voorkeur op een testomgeving het widget op dezelfde plek door een Elementor Shortcode-widget met `[rank_math_breadcrumb]`.
2. Test met Rank Math Pro uit en Free aan. Vergelijk labels, volgorde en linkbestemmingen. Klik vanaf het Beroepengids-detailarchief terug naar het tussenarchief en vanaf het comboarchief terug naar het bovenliggende vacaturearchief. Vergelijk ook `BreadcrumbList`, mobiele scroll en opmaak.
3. Werk de overige bevestigde templates en losse pagina’s pas bij als de proef overeenkomt. Controleer eerst of 28197 en 27450 ergens live worden gebruikt.

De templates en bestaande functionele TFSC-snippets zijn niet gewijzigd. De gevraagde uitbreiding is geïnstalleerd en zoekintentieanalyse is afzonderlijk uitgezet. De tijdelijke beheersnippet staat weer uit.
