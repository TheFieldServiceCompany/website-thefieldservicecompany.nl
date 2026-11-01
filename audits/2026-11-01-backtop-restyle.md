# Naar boven-knop: vormgeving — 1 november 2026

Op gebruikersverzoek de bestaande centrale footerknop verfijnd.
Footer 25019, iconwidget 530b4f2e en CSS in HTML-widget 74f5021 gewijzigd.

## Resultaat

- Dekkend donkerblauw via globale primary-kleur, fallback #00355B.
- Witte rand 1 px, hoekradius 8 px, subtiele schaduw (iets sterker bij hover).
- Klikvlak overal 48x48 px, bestaande witte SVG-pijl 22x22 px.
- aria-label Terug naar boven; native title Naar boven.
- Scroll-JavaScript bytegelijk gebleven. Geen wijziging aan drempels,
  plaatsing, ankerbuffer, header of vacaturepaneel.

## Verificatie

Live footer-meta teruggelezen en vergeleken; andere meta behouden.
Echte desktopbrowser: gemeten 48x48, pijl 22x22, achtergrond rgb(0,53,91),
witte 1px rand, radius 8px en schaduw rgba(0,20,35,0.18) 0 3 12.
Echte mobiele vacaturepagina op 390x844: eveneens 48x48, dezelfde kleur/rand,
16 px boven de vaste sollicitatiebalk. Werkelijke klik brengt pagina naar
y=0 en verbergt knop. Toegankelijke naam en title gecontroleerd op mobiele
pagina en zonder authenticatie op openbare Kennisbankpagina.
Viewport na de controle hersteld. Screenshots opgeslagen.

De eerste attributenserialisatie is tijdens de controle gecorrigeerd naar
Elementors komma-gescheiden formaat; uiteindelijke aria-label en title
zijn expliciet teruggelezen in de openbare HTML.

## Herstel

wordpress/exports/elementor/2026-11-01-before-backtop-restyle/ bevat de
oorspronkelijke footer-meta en CSS/HTML. Herstel gericht alleen de icon-
settings en HTML-widgetinhoud na controle op latere wijzigingen, gevolgd
door cacheverversing. Centrale plaatsing en scrollgedrag blijven daarmee
behouden.
