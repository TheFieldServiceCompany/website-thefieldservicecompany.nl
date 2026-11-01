# Vacaturepaneel: omslag en flikkeren hersteld — 1 november 2026

Gebruiker meldde een titel-/knoppenblok dat niet direct op de sticky header
aansloot en bij het omslagpunt flikkerde met ruimte onder het blok.

## Vastgestelde oorzaak

Browserreproductie op de vacature Field Service Engineer Duikboten:
bij y=468 was de triggeronderrand -4 px en het zichtbare paneel stond op
239 px, terwijl de header op 102 px eindigde. Bij 22 px terugscrollen
werd de wrapper weer absolute en veranderde de paginahoogte van 6400
naar 6207 px. De wrapper wisselde absolute/sticky en nam in zichtbare
toestand ruimte in de flow in; het binnenpaneel had bovendien een tweede
Elementor-stickyregeling. De scripttrigger gebruikte vensterbovenkant 0
in plaats van de headeronderrand.

## Wijziging

Uitsluitend drie instellingen in document 26022 gewijzigd: widget
2c4849c HTML en custom_css, container 9d55293 sticky. Voor bezoekers is
de wrapper steeds fixed, met zichtbaarheid op basis van de bestaande
trigger en 16 px hysterese. Positie en breedte volgen header en artikelkolom.
De binnenste Elementor-sticky is uitgeschakeld; overige offsets zijn
bewaard als hersteldata. Geen kunstmatige resize-events meer.
Inline editoruitzondering houdt het paneel zichtbaar en bewerkbaar.

REST-readback is structureel gelijk aan de beoogde drie wijzigingen.
Andere template-meta is behouden. De header blijft sticky, Anchor Offset
90 blijft ingesteld en snippet 359 blijft inactief.

## Verificatie

- 9 geïsoleerde JS-gedragstests geslaagd, inclusief drempel, hysterese,
  kolombreedte, beheerbalk, mobiel/resize en editoruitzondering.
- Werkelijke browser: desktop y=338–374, zeven kleine heen-/terugbewegingen.
  Paginahoogte steeds 6207 px. Paneelbovenrand steeds 102 px, gelijk aan
  de headeronderrand inclusief beheerbalk. Zichtbaarheid wijzigt alleen
  bij de bedoelde afzonderlijke grenzen.
- Mobiel 390 px: vier grensbewegingen, paginahoogte steeds 8344 px,
  paneelbovenrand 50 px gelijk aan headeronderrand; paneel- en kolombreedte
  beide 355 px. Onderste sollicitatiebalk blijft op 844 px.
- Tablet 820 px: paneel en header sluiten op 92 px aan.
- Herladen op bewaarde scrollpositie getest: paneel sluit na native
  headeractivatie op 102 px aan. Dit is apart gesynchroniseerd via
  observatie van headerclass/-stijl. Viewport daarna hersteld.
- Openbare vacature na cacheverversing HTTP 200, nieuwe paneelcode aanwezig,
  binnenste native sticky top ontbreekt, oude resize-dispatch ontbreekt.
- Geen formulieren verstuurd. Editoruitzondering in code en tests
  gecontroleerd; de visuele editor is voor deze reparatie niet geopend.

Geometriebewijs: audits/evidence/vacancy-panel-flicker/.

## Herstel

Verse oorspronkelijke template-meta en gerichte herstelinstructies:
wordpress/exports/elementor/2026-11-01-before-vacancy-panel-fix/.
Herstel alleen de drie instellingen na controle op latere wijzigingen;
zet niet zonder noodzaak een volledig ouder template terug.
