# Vacaturetemplate: scrollpaneel

- Omgeving: LIVE, thefieldservicecompany.nl.
- Document: `26022`, **single | vacature**; conditie `include/singular/vacature`.
- Paneel: container `044c4a6`, class `scroll-sticky-wrapper`.
- Code: HTML-widget `2c4849c`, binnen het paneel.
- `scroll-sticky.html`: actuele HTML-inhoud van die widget na de editorreparatie.
- `scroll-sticky-base.css`: bestaande Custom CSS van dezelfde widget, ongewijzigd.
- Status op 31 oktober 2026: via WordPress REST opgeslagen en teruggelezen.

De editoruitzondering staat bewust inline in het HTML-blok: de editor krijgt
haar bij het renderen van de widget, zonder afhankelijkheid van regeneratie
van een extern CSS-cachebestand. Zij geldt alleen onder Elementors editormarkeringen.
In de editor staat het paneel zichtbaar in de normale documentflow, zonder
hoge z-index die de bewerking van andere elementen kan hinderen.

Voor bezoekers blijven de oorspronkelijke basis-CSS en de scroll-/resize-/
orientationlogica behouden. In de JavaScript-code is alleen een vroege
editoruitzondering toegevoegd. Bestaande logregels zijn niet aangepast.

Controles: tien geïsoleerde JavaScript-scenario’s en zes CSS-cascadecontroles
geslaagd; slechts één `settings.html` gewijzigd; andere templategegevens en
formuliercode behouden. REST-readback komt exact overeen. De openbare voorbeeld-
vacature geeft HTTP 200. De aanvullende GUI-controle is inmiddels geslaagd: het paneel is zichtbaar
en selecteerbaar in de desktop- en mobiele Elementor-editor; op de desktop-
vacaturepagina verschijnt het na scrollen en verdwijnt het weer bovenaan.
Er is geen afzonderlijke mobiele bezoekersproef uitgevoerd.

Herstelbron: `wordpress/exports/elementor/2026-10-31/26022-2c4849c-scroll-sticky-before.html`.
Herstel alleen de HTML van widget `2c4849c`, met een actuele read vóór schrijven.
Een volledige eerdere templatekopie terugzetten kan latere bewerkingen overschrijven.

Zie `audits/2026-10-31-vacaturetemplate-elementor-en-formuliercontext.md` voor
opdracht, toestemming, API-toegang, bevindingen en resterende controle.
