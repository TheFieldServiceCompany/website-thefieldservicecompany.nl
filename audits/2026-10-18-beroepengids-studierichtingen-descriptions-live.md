# Beroepengids studierichtingen: descriptions LIVE

Datum: 18 oktober 2026  
Status: foutieve eerste publicatie volledig hersteld

> Correctie: deze teksten waren bedoeld voor `vac_studierichting`, niet voor
> `beroep_studierichting`. Alle 23 bestaande Beroepengids-descriptions zijn
> exact hersteld uit commit `bba82c6`. Het definitieve resultaat staat in
> `2026-10-18-vacature-studierichtingen-descriptions-live.md`.
> Alle onderstaande controles beschrijven de tijdelijke, foutieve toestand en
> zijn geen weergave van wat nu op de Beroepengids staat.

## Tijdelijk resultaat van de foutieve ronde

Alle 23 termen van de Pods-taxonomie `beroep_studierichting` hebben een unieke,
publicatieklare description. WordPress heeft na de schrijfronde alle 23
doelteksten exact teruggegeven.

De tekst voor Autotechniek is volledig herschreven. De tekst legt nu concreet
uit hoe mechanica, elektronica, aandrijftechniek, koeltechniek, sensoren en
diagnose uit een auto terugkomen in separatoren, compressoren,
verpakkingsmachines, pompen en andere industriële machines.

## Vaste redactionele eisen

Voor iedere tekst is afzonderlijk gecontroleerd dat:

- de werkelijke term zowel met `opleiding` als `studierichting` wordt gebruikt;
- `field service` als twee woorden wordt geschreven;
- de vier H2-koppen zelfstandig begrijpelijke zoekvragen zijn;
- koppen de richting koppelen aan concrete machines, technieken, functies of
  sectoren;
- de technische overdracht wordt uitgelegd in plaats van alleen beweerd;
- de tekst kandidaten, studenten en studiekiezers aanspreekt in de jij-vorm;
- de toon energiek en motiverend blijft zonder onbewezen superlatieven;
- het verschil tussen een passende achtergrond en een harde opleidingseis
  duidelijk blijft;
- generieke opvulzinnen en handmatige vacaturelinks ontbreken.

## Automatische kwaliteitscontrole

De publicatiecontrole gaf voor iedere term:

- exact vier H2-koppen;
- 437 tot en met 472 woorden bodytekst;
- minimaal drie natuurlijke vermeldingen van `field service`;
- een expliciete combinatie `opleiding [term]`;
- een expliciete combinatie `studierichting [term]`;
- geen aaneengeschreven `fieldservice`;
- geen eerder afgekeurde generieke formuleringen.

## Veilige uitvoering

De bestaande descriptions zijn voor de eerste wijziging lokaal opgeslagen.
De publicatiescript controleerde eerst alle 23 slugs en teksten. Bij een fout
tijdens het schrijven zou de script reeds gewijzigde termen automatisch naar
hun oorspronkelijke description terugzetten.

Eindresultaat van de onafhankelijke herlezing:

- gepubliceerd: 23;
- via REST geverifieerd: 23;
- afwijkingen: 0.

## Frontendsteekproef

Met cache-omzeilende openbare requests zijn de H1 en nieuwe H2-koppen bekeken
voor:

- Autotechniek;
- Industriële Automatisering;
- Technische Natuurkunde;
- Mobiele Werktuigentechniek.

Alle vier archieven tonen de juiste H1 `Studierichting [term]` en de vier
bijbehorende descriptionskoppen. De bestaande vacature- en relatieblokken zijn
behouden.

## Bronnen en herstel

- Canonieke tekstbron:
  `content/education-descriptions/studierichtingen.md`
- Vaste redactionele regels:
  `content/education-descriptions/editorial-prompt.md`
- Publicatie- en validatiescript buiten de repository:
  `work/publish_study_direction_descriptions.py`
- Lokale voor-situatie en controlerapport:
  `work/study-direction-descriptions-live-2026-10-18/`
