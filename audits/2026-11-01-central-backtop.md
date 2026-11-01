# Centrale Naar boven-knop — 1 november 2026

Opdracht: bestaande vacaturepijl met witte rand verplaatsen naar de footer;
alleen op pagina's langer dan twee schermhoogtes en na één scherm scrollen
met fade-in zichtbaar. Geen dubbele vacatureknop; ruimte boven mobiele CTA.

## Uitvoering

Widgets 530b4f2e (icon) en 74f5021 (HTML) van 26022 naar 25019 verplaatst.
Icon gebruikt dezelfde SVG 19332, achtergrond en rand. Afmetingen en padding
zijn gecentreerd op 60x60 / 44x44. Bestemming # wordt door de centrale
klikregeling afgehandeld met scrollTo(0), zonder ankervereiste op andere pagina's.
HTML-widget bevat verborgen inline CSS/JS en behoudt hide_desktop/tablet/mobile.

Beide templates direct vóór schrijven opnieuw gelezen. REST-readback gelijk
aan de beoogde gewijzigde Elementor-data; overige meta behouden. Alle andere
vacaturewidgets, inclusief de reparatie van het vaste paneel, zijn behouden.
Header en native Anchor Offset zijn niet gewijzigd.

## Verificatie

- 16 geïsoleerde JS-gedragstests en syntaxiscontrole geslaagd.
- Echte desktopbrowser vacature Duikboten: één knop, bij y=0 verborgen;
  bij y=864 en vensterhoogte 720 zichtbaar, zonder dat footer in beeld is.
- Computed style: witte rand rgb(255,255,255), 1 px, achtergrond
  rgba(0,53,91,0.55), klikvlak 60x60, onderrand 24 px van vensteronderkant.
- Werkelijke klik uitgevoerd: scrollpositie eindigt op 0 en knop verdwijnt.
- Mobiel 390x844: knop 44x44; knoponderrand 726, sollicitatiebalkbovenrand
  742: precies 16 px vrije ruimte. Sollicitatiebalk blijft op onderrand 844.
- Mobiel menu openen verbergt de knop; na Escape verschijnt hij weer.
- Korte Disclaimer: paginahoogte 1352, venster 720; ook onderaan bij y=632
  verborgen. Kennisbank-HVAC (5203), Beroepengids-overzicht (3294) en Blog
  (10979) tonen bij y=864 elk exact één knop.
- Openbare contact-, Disclaimer-, Beroepengids- en Kennisbankpagina geven
  HTTP 200 en bevatten elk één knop. Sollicitatieformulier HTTP 200 bevat
  geen gedeelde footerknop.
- Paginahoogte op vacature blijft 6207 px; de verplaatsing voegt geen
  lege ruimte aan de flow toe. Mobiel/desktop viewport na controle hersteld.
- Popup-/form-/bedankuitsluiting, veranderende paginahoogte, verminderde
  beweging en editoruitzondering ook geïsoleerd getest. Geen formulieren
  verstuurd en geen visuele editor geopend voor deze taak.

Bewijs: audits/evidence/central-backtop/pages.json en twee uitvoerscreenshots.

## Herstel

Exacte eerdere meta van footer en vacaturetemplate plus gerichte instructies:
wordpress/exports/elementor/2026-11-01-before-central-backtop/.
Herstel alleen de twee verplaatste widgets na controle op latere wijzigingen;
geen volledige oudere templates terugzetten als dat latere bewerkingen raakt.
