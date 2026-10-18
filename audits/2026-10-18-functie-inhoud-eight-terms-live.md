# Functie-inhoud: acht ontbrekende descriptions en archieftitels LIVE

Datum: 18 oktober 2026
Omgeving: `https://thefieldservicecompany.nl`

## Uitgevoerd

De acht nog lege termen van de vacaturetaxonomie `functie-inhoud` zijn
voorzien van een unieke `pods_archive_title` en een inhoudelijke description.
Iedere description telt 400–409 woorden en is geschreven voor kandidaten, in
de jij-vorm en met een concrete koppeling naar field service.

| Term | Archieftitel | Woorden | Gekoppelde vacatures tijdens controle |
| --- | --- | ---: | ---: |
| Sales | Field service vacatures met technische sales | 400 | 3 |
| Storingen | Field service vacatures voor storingen en service | 409 | 47 |
| Technical Support | Technical support vacatures in field service | 402 | 0 |
| Testen | Field service vacatures voor testen en opleveren | 400 | 0 |
| Training | Field service vacatures met technische training | 400 | 0 |
| Troubleshooting | Troubleshooting vacatures in field service | 400 | 0 |
| Upgrades | Field service vacatures voor upgrades en retrofits | 401 | 45 |
| Werkvoorbereiding | Field service vacatures met werkvoorbereiding | 400 | 0 |

De exacte live teksten staan in
`content/functie-inhoud/functie-inhoud-acht-termen-live.json`.

## Inhoudelijke afbakening

- **Sales** gaat uitsluitend over technische sales binnen service: spare
  parts, onderhoud, servicecontracten, advies, upgrades en het herkennen van
  kansen vanuit technische klantkennis.
- **Storingen** draait om veilig en snel herstel wanneer een installatie
  onverwacht uitvalt.
- **Troubleshooting** is afzonderlijk gepositioneerd als systematische
  diagnose van complexe, terugkerende of moeilijk verklaarbare problemen.
- **Werkvoorbereiding** omvat de voorbereiding van klantprojecten vóór vertrek
  én werkzaamheden vanuit kantoor, werkplaats of magazijn.
- **Technical Support, Testen, Training en Upgrades** zijn uitgewerkt vanuit
  hun concrete rol in serviceorganisaties en machinebouw.

## Technische uitvoering en controle

De oorspronkelijke waarden zijn vóór publicatie opgeslagen in
`work/functie-inhoud-acht-termen-2026-10-18/before.json`. Alle acht termen zijn
via WordPress REST gepubliceerd en daarna onafhankelijk opnieuw uitgelezen.
De descriptions en archieftitels kwamen bij 8 van 8 termen exact overeen.

De openbare archieven `Storingen` en `Werkvoorbereiding` zijn aanvullend op de
gerenderde frontend gecontroleerd. Beide tonen de nieuwe H1 en de nieuwe
description. Ook het lege archief Werkvoorbereiding blijft dus technisch
bereikbaar en gebruikt de generieke template correct.

Niet gewijzigd:

- termnamen, slugs, term-ID's en vacaturekoppelingen;
- Pods-definities;
- Elementor-templates;
- snippets;
- Rank Math-velden.

## Aandachtspunt

Er bestaat een gepubliceerde vacature met de functietitel
`Werkvoorbereider Elektrotechniek`, maar tijdens de controle is geen term
`Werkvoorbereider` aangetroffen onder de aparte taxonomie `functienaam`.
Beoordeel later of die functienaam als canonieke term moet worden toegevoegd
en aan passende vacatures moet worden gekoppeld. Hiervoor is nu niets
aangemaakt of gewijzigd.
