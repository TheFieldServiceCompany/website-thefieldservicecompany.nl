# Gedeelde scrollheader — livegang 1 november 2026

Opdracht: header verdwijnt bij omlaag scrollen, verschijnt bij omhoog scrollen;
het vaste vacaturepaneel volgt. Gebruiker vroeg uitdrukkelijk om een back-up.

## Uitvoering en herstel

Toegevoegd: actieve front-end Code Snippet **359**, versie 1.0.0.
Header 17275 en vacaturetemplate 26022 zijn vooraf via REST veiliggesteld.
Hun volledige meta en modified-velden zijn na livegang bytegelijk met de
verse nulmeting. Pluginbestanden en overige snippets zijn niet gewijzigd.
De JS/CSS is apart beheerd in wordpress/elementor/custom-code/shared-scroll-navigation.

Terugzetten: alleen snippet 359 deactiveren en de LiteSpeed-paginacache
verversen. De ongewijzigde Elementor-regeling neemt dan weer over.
De templatekopieën dienen als extra herstelbron, niet als verplichte
terugzetstap. Zie wordpress/exports/elementor/2026-11-01-before-scroll-navigation.

## Verificatie

- 17 geïsoleerde JS-gedragstests geslaagd; PHP 8.3- en JS-syntaxis geldig.
- Actieve live snippet teruggelezen en exact gelijk met broncode.
- Vier openbare pagina's (vacature, Kennisbank, Beroepengids en Blog): HTTP 200,
  exact één controller en één stylesheet per pagina.
- Echte browser, ingelogde beheerweergave:

| Scherm en richting | Header | Bovenrand vacaturepaneel |
| --- | --- | --- |
| Desktop omlaag | verborgen, onderrand 24 px boven beheerbalkonderrand 32 px | 32 px |
| Desktop omhoog | zichtbaar, onderrand 102 px | 102 px |
| Tablet 820 px omlaag | verborgen; headerhoogte 60 px | 32 px |
| Tablet 820 px omhoog | zichtbaar, onderrand 92 px | 92 px |
| Mobiel 390 px omlaag | verborgen, onderrand -8 px | 0 px |
| Mobiel 390 px omhoog | zichtbaar, onderrand 50 px | 50 px |

- Mobiele onderste sollicitatiebalk blijft op de onderrand van het venster
  (844 px). Documentbreedte 375 px bij viewport 390 px: geen horizontale overflow.
- Mobiel hoofdmenu geopend en via Escape gesloten; aria-expanded=true en
  focus binnen de header houden die zichtbaar.
- Kennisbank, mobiel: aangeklikte HVAC-kop op circa 130 px, headeronderrand 50 px.
- Beroepengids, desktop: aangeklikte kop op circa 168 px, headeronderrand 102 px.
- Blog, desktop: aangeklikte kop op circa 168 px, headeronderrand 102 px.
- Blog: header na omlaag scrollen verborgen; Tab toont de header weer.
- Geen consolefouten in de gecontroleerde blogsessie.
- Homepage gebruikt header 26507 en valt buiten deze regeling.
- Elementor-editoruitsluiting is in gedragstests gecontroleerd, verminderde
  animatie in de bron; er is in deze opdracht geen visuele editor geopend.
- Geen formulieren verstuurd. Tijdelijke cacheverversingssnippets 360 en 361 na gebruik
  gedeactiveerd en verwijderd. Browserviewport na de controle hersteld.

De browsercontroles vonden plaats met een ingelogde sessie; de beheerbalk
is dus echt getest. Zonder beheerbalk is de rekenlogica afzonderlijk getest
in de 17 gedragsscenario's; de openbare HTML is zonder authenticatie opgehaald.
