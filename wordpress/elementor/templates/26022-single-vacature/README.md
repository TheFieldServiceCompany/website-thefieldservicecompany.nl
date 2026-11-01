# Vacaturetemplate: vast titel- en knoppenpaneel

LIVE sinds de reparatie op 1 november 2026. Document **26022**, single | vacature.

- Wrapper: 044c4a6, class scroll-sticky-wrapper.
- Binnenpaneel: 9d55293. Elementor sticky is hier leeg/uitgeschakeld.
- HTML-widget: 2c4849c.
- scroll-sticky.html: complete live HTML inclusief inline CSS en JS.
- scroll-sticky-base.css en scroll-sticky.js: leesbare bronnen van die inhoud.
- Custom CSS-veld van widget 2c4849c is leeg; alle paneelstijl staat inline.

## Werking

De wrapper heeft voor bezoekers altijd position:fixed, ook als hij verborgen
is. Hij neemt daardoor geen ruimte in de artikeltekst in. Bovenrand volgt
de werkelijk gemeten onderrand van header 17275 (inclusief beheerbalk),
links en breedte volgen de artikelkolom. De bestaande sticky-trigger bepaalt
zichtbaarheid: tonen bij het bereiken van de headeronderrand; verbergen pas
16 px verder terug. Dit voorkomt wisselen bij kleine scrollbewegingen.

Er is één eigenaar van de paneelpositie; Elementors tweede stickyregel op
het binnenpaneel is uitgeschakeld. De oude resize-dispatch is verwijderd.
ResizeObserver volgt scherm-/inhoudswijzigingen, MutationObserver volgt het
activeren van de native header bij herladen op een bewaarde scrollpositie.

In de Elementor-editor staat de wrapper zichtbaar in de normale documentflow.
Voor bezoekers is het verborgen paneel inert. prefers-reduced-motion slaat
de korte zichtbaarheidsanimatie over.

De gedeelde header blijft sticky, de native Anchor Offset blijft 90 px en
scrollheadersnippet 359 blijft uit. De onderste mobiele sollicitatiebalk en
alle bestaande knoppen, bestemmingen en overige templatevelden zijn behouden.

## Herstel

Herstelkopie: wordpress/exports/elementor/2026-11-01-before-vacancy-panel-fix/26022.json.
Herstel gericht de drie aangepaste instellingen: HTML en custom_css van
widget 2c4849c plus sticky van container 9d55293. Controleer vooraf op
nieuwere wijzigingen. Wis daarna Elementor- en LiteSpeed-cache.

De eerdere editorreparatie van 31 oktober is historisch gedocumenteerd in
audits/2026-10-31-vacaturetemplate-elementor-en-formuliercontext.md.
De huidige reparatie en controles staan in audits/2026-11-01-vacancy-panel-flicker.md.
