# Centrale Naar boven-knop

LIVE sinds 1 november 2026 in gedeelde footer **25019**, container 38807074.
De bestaande vacaturewidgets zijn verplaatst: icon **530b4f2e** en
HTML-/scriptwidget **74f5021**. Ze staan niet meer in vacaturetemplate 26022.

## Beheer

- backtop-widget.json: native Elementor-iconwidget, bestaande SVG-media 19332.
- backtop.html: complete live inhoud van HTML-widget 74f5021.
- backtop.js en backtop.css: leesbare bronnen, inline samengevoegd in backtop.html.
- Codewidget is op alle schermen visueel verborgen; CSS/JS worden wel uitgevoerd.
- De iconwidget krijgt class tfsc-back-to-top en aria-label Terug naar boven.

## Gedrag

Beschikbaar op pagina's met footer_main. Alleen zichtbaar wanneer de volledige
pagina langer is dan twee vensterhoogtes en één vensterhoogte is gescrold.
24 px hysterese bij terugscrollen voorkomt flikkeren aan de grens.
ResizeObserver herberekent na wijziging van de paginahoogte.

Routes die beginnen met formulier-, formulier/, bedankt-, bedankpagina- of
thank-you- (ook hun basisroute) zijn uitgesloten. Tijdens geopende Elementor-
popups, off-canvasmenu's en native dialogs wordt de knop verborgen.

Huidige vormgeving na restyling op 1 november 2026: dekkende primaire
huisstijlkleur (fallback #00355B), dezelfde witte pijl op 22 px, witte
1px rand, hoeken van 8 px en subtiele schaduw. Klikvlak op alle schermen
48x48 px. Native hovertekst Naar boven; aria-label Terug naar boven. Rechtsonder: normaal 24 px van onderkant;
op mobiele vacatures gemeten hoogte van de vaste sollicitatiebalk plus 16 px.

Verschijnt met fade-in van 180 ms; bij prefers-reduced-motion geen animatie.
Verborgen knop is inert. Klik scrollt naar 0 zonder afhankelijkheid van een
#scroll-top-anchor en brengt focus terug naar de hoofdinhoud/kop.
In de Elementor-editor is de knop zichtbaar voor bewerking.

De header, ankerbuffer en het stabiele vacaturepaneel zijn niet gewijzigd.
Zie audits/2026-11-01-central-backtop.md en de herstelkopieën in
wordpress/exports/elementor/2026-11-01-before-central-backtop/.

Test vanuit repositoryroot: node audits/tests/backtop.test.mjs
