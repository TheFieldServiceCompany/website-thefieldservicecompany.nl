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
| Sales | Field service vacatures met functie-inhoud Sales | 400 | 3 |
| Storingen | Field service vacatures met functie-inhoud Storingen | 409 | 47 |
| Technical Support | Field service vacatures met functie-inhoud Technical Support | 402 | 0 |
| Testen | Field service vacatures met functie-inhoud Testen | 400 | 0 |
| Training | Field service vacatures met functie-inhoud Training | 400 | 0 |
| Troubleshooting | Field service vacatures met functie-inhoud Troubleshooting | 400 | 0 |
| Upgrades | Field service vacatures met functie-inhoud Upgrades | 401 | 45 |
| Werkvoorbereiding | Field service vacatures met functie-inhoud Werkvoorbereiding | 400 | 0 |

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

## Controle titelschema van de volledige taxonomie

Na instructie is voor de acht termen het exacte schema toegepast:
`Field service vacatures met functie-inhoud [term]`. Het woord `term` is een
placeholder; er staan geen aanhalingstekens in de live titels. De aangepaste
H1 van Sales is op de gerenderde frontend gecontroleerd.

Van alle 29 functie-inhoudtermen voldoen er nu 9 exact aan het schema:
`Adviseren` en de acht termen uit bovenstaande tabel. De overige 20 zijn
alleen gecontroleerd en bewust niet gewijzigd. Hun huidige waarden zijn:

| Term | Huidige `pods_archive_title` |
| --- | --- |
| Commissioning | Field Service Vacatures met functie-inhoud Commissioning |
| Diagnose | Field Service Vacatures met functie-inhoud Diagnose |
| Field service | Field Service Vacatures met functie-inhoud Field Service |
| Inbedrijfstellen | Field Service Vacatures met functie-inhoud Inbedrijfstellen |
| Inspectie | Field Service Vacatures met functie-inhoud Inspectie |
| Installatie | Field Service Vacatures met functie-inhoud Installatie |
| Instructie | Field Service Vacatures met functie-inhoud Instructie |
| Kalibratie | Field Service Vacatures met functie-inhoud Kalibratie |
| Leidinggeven | Field Service Vacatures met functie-inhoud Leidinggeven |
| Maintenance | Field Service Vacatures met functie-inhoud Maintenance |
| Modificaties | Field Service Vacatures met functie-inhoud Modificaties |
| Onderhoud | Field Service Vacatures met functie-inhoud Onderhoud |
| Overhalen | Field Service Vacatures met functie-inhoud Overhalen |
| Planning | Field Service Vacatures met functie-inhoud Planning |
| PLC Analyse | Field Service Vacatures met functie-inhoud PLC Analyse |
| PLC Programmeren | Field Service Vacatures met functie-inhoud PLC Programmeren |
| Programmeren | Field Service Vacatures met functie-inhoud Programmeren |
| Rapportages | Field Service Vacatures met functie-inhoud Rapportages |
| Reparatie | Field Service Vacatures met functie-inhoud Reparatie |
| Revisie | Field Service Vacatures met functie-inhoud Revisie |

Bij deze twintig zit de schema-afwijking in de hoofdletters van
`Field Service Vacatures`. Bij de term `Field service` wijkt daarnaast de
hoofdletter van `Service` in de ingevoegde termnaam af van de actuele
WordPress-termnaam. Correctie hiervan vereist apart akkoord.

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
