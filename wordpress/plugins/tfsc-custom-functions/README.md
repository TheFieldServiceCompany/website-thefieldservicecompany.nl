# TFSC Custom Functions 0.6.0

**Versie 0.6.0 is actief op de live website sinds 2 november 2026.** Alle 63
individuele vacaturepagina's en het vacaturearchief zijn na de uitrol
gecontroleerd. Zie `audits/2026-11-02-vacancy-context-all-forms.md` voor de
livecontrole, Supabase-fallbackproef en het herstelpad.

Versie 0.6.0 breidt dezelfde gevalideerde POST-route uit naar advies en vragen
vanaf een afzonderlijke vacaturepagina. Solliciteren, advies en vragen openen
daardoor alle drie een schone formulier-URL en geven uitsluitend het opgeslagen
`vacaturenummer` door. Algemene adviesknoppen buiten vacaturepagina's blijven
contextvrij en behouden hun bestaande bestemming.

Het oude globale Elementor Custom Code-blok `21893` is niet meer nodig. Het
scrapete een verouderd zichtbaar label, voegde een queryparameter toe en kon op
algemene pagina's een registratieknop kapen. Het blok wordt bij de livegang van
0.6.0 verwijderd. De databasefallback op de exacte bron-URL blijft actief voor
historisch herstel en als onafhankelijke reserveroute.

## Eerdere livebasis

**Versie 0.5.0 is actief op de live website sinds 2 november 2026.** De
pluginversie is in WordPress teruggelezen en alle 63 openbare vacaturepagina's
zijn na de uitrol gecontroleerd. Zie
`audits/2026-11-02-vacancy-application-context.md` voor oorzaak, uitvoering,
testresultaten en herstelpad.

## Vacaturecontext voor solliciteren

Versie 0.5.0 vervangt de oude Elementor-widget die zichtbare tekst als
`Vacaturenummer:` uitleest en titel, bedrijf, contractvorm en pagina-URL door
middel van losse queryparameters verstuurt.

Op een afzonderlijke vacaturepagina leest de plugin uitsluitend het opgeslagen
Pods-/postveld `vacaturenummer`. Alle knoppen met
`data-tfsc-cta-action="apply"` of de bestaande class `button-solliciteren`
gebruiken één verborgen POST-formulier naar `/formulier-solliciteren/`. De
formulier-URL blijft daardoor schoon. De bestaande Elementor Request Parameter
blijft compatibel doordat alleen de gevalideerde POST-waarde server-side aan de
bestaande formulierrendering wordt aangeboden.

De oude Elementor HTML-widget `62cd538` wordt op vacaturepagina's niet meer
gerenderd. De centrale handler houdt de bestaande `data-tfsc-*` attributen op
de zichtbare knoppen intact, zodat GTM de kliklocatie kan blijven meten. De
bestaande vacature-vraagknoppen krijgen alleen `vacaturenummer` mee; titel,
bedrijf en contractvorm worden niet meer uit de browser-HTML afgeleid.

Een ontbrekend of niet-numeriek vacaturenummer stopt de route met een duidelijke
melding. De plugin kiest nooit een vacature op basis van titel, label of URL.

De controles in `audit/verify-php.mjs` bewijzen onder meer opslagvelduitlezing,
validatie, uitschakeling van de oude widget, POST-overdracht en afwezigheid van
de overbodige browservelden.

**Versie 0.4.1 is actief op de live website sinds 1 november 2026.** PHP en CSS zijn geplaatst en teruggelezen; de Elementor-kitinstellingen uit 0.4.0 zijn ongewijzigd.

0.4.1 trekt de mobiele grens gelijk met Elementor: maximaal 767 px. De tabletgrens blijft 1024 px. `.hide-mobile` en popup-/hoogteregels zijn ongewijzigd. Zie [de mediaquery-audit](audit/mediaqueries-0.4.1.md).

## Waar beheer je wat?

| Instelling | Beheerplek | Werking |
| --- | --- | --- |
| Huisstijlkleuren | Elementor → Site Settings → Global Colors | CSS verwijst naar `--e-global-color-*`; hexwaarden in `var(..., #...)` zijn terugvalwaarden. |
| Basistekst en H1–H6 | Elementor → Site Settings → Typography | Bestaande effectieve fonts, groottes, gewichten en mobiele maten zijn hier vastgelegd. Dubbele algemene kopgroottes zijn uit de CSS gehaald. |
| Global Fonts | Elementor → Site Settings → Global Fonts | Oude fontaliassen zijn gecorrigeerd; de CSS gebruikt de fontfamilievariabelen als basis/fallback. |
| Lokale fontbestanden | `@font-face` in de CSS | Alleen bestandsdefinities; geen tweede vaste preloadconfiguratie in PHP. |
| Knoppen, formulieren, lijsten, tabellen en focus | Plugin-CSS | Componentgedrag en bewuste uitzonderingen, met kleuren uit Elementor. |
| H2/H3 in artikelinhoud | `.elementor-widget-theme-post-content h2/h3` in CSS | Bewuste uitzondering: 26/22 px; algemene H2/H3 blijven 24/20 px. |

Verander algemene kopmaten in Site Settings en alleen de afwijkende artikelmaten bij de genoemde componentregels. Voeg geen tweede algemene H1–H6-regel toe in de CSS. Individuele widgets kunnen nog bewuste lokale afwijkingen hebben; deze migratie herschrijft niet alle widgets.

## PHP-correcties

- CSS-versie via `filemtime()`; bestaande handle `theme-override` en laadprioriteit behouden.
- Unieke functienamen en afvang voor directe toegang buiten WordPress.
- Zoektekst geëncodeerd; accentkleur via een CSS-klasse in plaats van inline PHP-opmaak.
- Leesduur ondersteunt ontbrekende berichten, Unicode-woorden en correct enkelvoud.
- Bestaande shortcode-namen behouden.
- Oude recente-reacties-filter, Eicons-voorbeeldcode, vaste fontpreloads en globale Font Awesome-deregistratie verwijderd.
- Globale uitschakeling van WordPress-blokstijlen verwijderd; de browser bevestigt dat `wp-block-library-css` weer laadt.
- Het niet-geladen admin-CSS-bestand is als herstelbestand op de server gelaten, zonder actief te worden.

## Validatie

18 PHP-controles en 13 controles met echte WordPress-hulpfuncties zijn geslaagd. Vooraf zijn zes paginatypen op desktop en mobiel vergeleken (24 ladingen). Vervolgens is de echte WordPress 7.1.4-/Elementor-site gecontroleerd: zes pagina’s geven HTTP 200, hashes en pluginstatus kloppen, checkboxfocus werkt, mobiel menu opent/sluit, tabellen blijven binnen het scherm en Meer toont extra loop-items (52 → 54). Geen formulier is verstuurd.

Zie [beheer en livegang 0.4.0](audit/beheer-en-livegang-0.4.0.md). De eerdere audits van kandidaat 0.3.0 zijn historisch; uitspraken daarin over nog niet live geplaatst gelden niet meer voor 0.4.0.

## Versies en herstel

- [Originele back-up 0.2.0](../../exports/plugins/2026-11-01-before-custom-functions-cleanup/).
- [Verse back-up inclusief Elementor-instellingen vóór 0.4.0](../../exports/plugins/2026-11-01-before-custom-functions-0.4.0/).
- [Kitpatch en live bewijs](audit/0.4.0/).
- [Back-up van 0.4.0 vóór de mediaquerycorrectie](../../exports/plugins/2026-11-01-before-custom-functions-0.4.1/) en [controlebewijs 0.4.1](audit/0.4.1/).

0.4.0 hoort bij de gesynchroniseerde Elementor-kit 9367. De kitpatch in het installatiepakket wordt niet automatisch uitgevoerd bij een plugin-upload. Voor exact herstel moeten zowel pluginbestanden als de bijbehorende kitinstellingen worden teruggezet, rekening houdend met latere wijzigingen. Genereer daarna de kit-CSS opnieuw en vernieuw de caches.

PHP-controles herhalen: `npm install` en `npm test` in `audit/` (Node.js vereist). Extra kerncontrole: `node verify-core.mjs /pad/naar/wordpress`. Dependencies en fixtures zitten niet in het installatiepakket.
