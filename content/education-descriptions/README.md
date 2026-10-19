# Opleidingsdescriptions voor vacatures en Beroepengids

Status: de 23 vacaturedescriptions staan sinds 18 oktober 2026 bij
`vac_studierichting` op LIVE. Op 19 oktober 2026 zijn daarnaast alle 23
descriptions en intro's van `beroep_studierichting` bewust herschreven rond de
vraag wat iemand met de betreffende studie binnen field service kan doen. De
vier vacaturetermen van `opleidingsniveau` hebben eveneens gecontroleerde live
descriptions; `beroep_opleidingsniveau` is daarbij niet gewijzigd.

## Doel

Deze map documenteert de opleidingscontent voor de vacaturetaxonomieën en de
spiegelende Beroepengids-taxonomieën. Het bestand `studierichtingen.md` bevat
de nieuwe, lange descriptions die op `vac_studierichting` zijn gepubliceerd.
`beroep-studierichtingen.md` bevat de afzonderlijke, actuele contentlaag voor
`beroep_studierichting`. Deze Beroepengids-teksten zijn geen kopie van de
vacaturedescriptions: zij verbinden iedere opleiding of studie expliciet aan
mogelijke field service functies, sectoren en loopbaankeuzes.

De compacte, herbruikbare schrijf- en publicatieprompt staat in
`reusable-taxonomy-text-prompt.md`. Gebruik die voor nieuwe taxonomyteksten,
zodat stijl, inhoudelijke controle en de keuze van de juiste WordPress-
taxonomie vooraf vastliggen.

De twee gescheiden vacaturetaxonomieën `opleidingsniveau` en
`vac_studierichting` zijn de canonieke bron voor de termen en relaties. De
teksten worden pas gepubliceerd nadat de spiegelende hiërarchische
Pods-structuur en de relatievelden zijn ingericht en getest.

## Omvang

- 4 descriptions voor opleidingsniveaus;
- 23 descriptions voor vacaturestudierichtingen;
- 23 descriptions en 23 uitnodigende intro's voor Beroepengids-
  studierichtingen;
- gerealiseerde lengte per description: 450–486 woorden exclusief koppen en
  redactionele notities.

De Beroepengids-descriptions van 19 oktober bevatten 261–390 woorden en ieder
vier unieke vraaggerichte H2-koppen. De intro's bevatten 28–36 woorden.

## Publicatie Beroepengids-studierichtingen op 19 oktober 2026

- Alle 23 bestaande descriptions zijn bewust overschreven; er zijn geen
  termen, slugs, Pods of velden toegevoegd of gewijzigd.
- Alle 23 bestaande velden `intro_taxonomy` zijn vervangen door een korte,
  uitnodigende intro met een directe inhoudelijke link naar field service.
- De 92 H2-koppen zijn letterlijk uniek en variëren per studierichting.
- De teksten behandelen primair wat iemand met de opleiding, studierichting of
  studieachtergrond binnen field service kan doen. Uitgebreide uitleg over het
  uitvoeren van technische handelingen is bewust vermeden.
- Iedere tekst eindigt met een eigen adviesroute en een natuurlijke verwijzing
  naar The Field Service Company als specialist in field service functies.
- Canonieke bron: `beroep-studierichtingen.md`; herstelbestand:
  `../../audits/exports/beroep-studierichtingen-before-rewrite-2026-10-19.json`.

## Publicatievoorwaarden

1. Geen teksten opslaan bij de vacaturetermen als ze voor de Beroepengids zijn
   bedoeld.
2. De testterm `Opleiding A` niet hergebruiken als definitieve term.
3. Eerst de taxonomie en slugs vastleggen; daarna teksten importeren.
4. Per term controleren dat de H1 door de template wordt geleverd en dat de
   description alleen H2-koppen gebruikt.
5. Na publicatie de openbare pagina, interne links, canonieke URL, metadata en
   mobiele weergave controleren.
6. Pas na inhoudelijke koppeling vacatures of beroepsartikelen tonen; lege of
   onjuiste relaties niet verhullen met tekst.
7. Voeg onder de description een dynamische TFSC-CTA toe. Toon de
   vacaturevariant alleen wanneer de term gekoppelde, actuele vacatures heeft;
   gebruik anders een route naar relevante functies of studierichtingen.

## Publicatie vacature-studierichtingen op 18 oktober 2026

- Alle 23 termen van `vac_studierichting` zijn via WordPress REST voorzien
  van de gecontroleerde description uit `studierichtingen.md`.
- Iedere tekst is 437–472 woorden lang en bevat exact vier vraaggerichte
  H2-koppen.
- Iedere tekst gebruikt zowel `opleiding [term]` als `studierichting [term]`
  en schrijft `field service` consequent als twee woorden.
- Algemene koppen zijn vervangen door zoekvragen waarin de opleiding,
  studierichting, relevante techniek, machines of sectoren herkenbaar zijn.
- Autotechniek is inhoudelijk herschreven rond de overdracht van mechanica,
  elektronica, koeling, sensoren en diagnose naar industriële machinebouw.
- Voor het schrijven is per richting gecontroleerd op concrete technische
  raakvlakken, passende field service functies en geloofwaardige sectoren.
- De bestaande live descriptions zijn vooraf opgeslagen. De publicatiescript
  ondersteunt automatische rollback bij een schrijffout.
- Na publicatie zijn alle 23 vacaturetermen opnieuw uit WordPress gelezen en
  exact vergeleken met de lokale bron. Het openbare vacaturearchief voor
  Autotechniek is daarnaast op de gerenderde frontend gecontroleerd.
- Een eerste publicatieronde was abusievelijk gericht op
  `beroep_studierichting`. Die wijziging is volledig hersteld vanuit commit
  `bba82c6` en daarna voor alle 23 Beroepengids-termen geverifieerd.

## Publicatie vacature-opleidingsniveaus op 18 oktober 2026

- Alle vier termen van `opleidingsniveau` zijn voorzien van een nieuwe of
  volledig herschreven description.
- De teksten behandelen het niveau zelf en maken duidelijk dat studierichting,
  werkervaring en productkennis afzonderlijke factoren zijn.
- VMBO / LBO is praktisch en historisch correct beschreven: VMBO is
  voorbereidend onderwijs en LBO een oude benaming.
- MBO legt de verbinding tussen praktijk en vaktheorie; HBO tussen analyse en
  systeemdenken; WO / Universiteit tussen academische diepgang en specifieke
  complexe toepassingen.
- HBO en WO zijn niet automatisch gekoppeld aan meer verantwoordelijkheid of
  senioriteit.
- De teksten zijn bewust compact gehouden op 293–309 woorden.
- Voor publicatie zijn de bestaande vier descriptions opgeslagen. Daarna zijn
  alle vier REST-resultaten en alle vier gerenderde archieven gecontroleerd.

## Archieftitels vacatureopleidingen op 18 oktober 2026

- Alle vier vacature-opleidingsniveaus gebruiken als Pods-archieftitel
  `Field service vacatures voor opleidingsniveau [term]`.
- Alle 23 vacature-studierichtingen gebruiken
  `Field service vacatures voor studierichting [term]`.
- De titels zijn opgeslagen in `pods_archive_title`; termnamen en slugs zijn
  niet gewijzigd.
- Snippet 186 versie 4.5.4 gebruikt dit veld ook als H1 op een
  single-term-combo-archief. Combinaties van meerdere termen behouden hun
  bestaande compacte titelopbouw.
- Inactieve herstelkopie van de eerdere snippetversie: ID 242.

## Canonieke termen

Opleidingsniveaus: VMBO / LBO, MBO, HBO en WO / Universiteit.

Studierichtingen: Autotechniek, Bedrijfsautotechniek, Chemische Technologie,
Civiele Techniek, Elektronica, Elektrotechniek, Embedded Systems,
Energietechniek, ICT & Technische Informatica, Industriële Automatisering,
Installatietechniek, Koude- en Klimaattechniek, Luchtvaarttechniek, Maritieme
Techniek, Mechatronica, Mobiele Werktuigentechniek, Operationele Techniek,
Procestechniek, Robotica, Scheepsbouwkunde, Technische Bedrijfskunde,
Technische Natuurkunde en Werktuigbouwkunde.

## Onderzoeksbasis

De teksten vermijden instelling-specifieke curriculumclaims. Voor de algemene
onderwijsstructuur en technische competenties zijn onder meer deze primaire of
landelijke bronnen gebruikt:

- Rijksoverheid — opbouw en profielen van het vmbo:
  <https://www.rijksoverheid.nl/documenten/vraag-en-antwoord/voortgezet-onderwijs/hoe-zit-het-vmbo-in-elkaar>
- Rijksoverheid — mbo-niveaus en opleidingsroutes:
  <https://www.rijksoverheid.nl/themas/onderwijs/middelbaar-beroepsonderwijs/passendonderwijs-in-het-mbo/vraag-en-antwoord/middelbaar-beroepsonderwijs/uit-welke-mbo-opleidingen-kan-ik-kiezen>
- Rijksoverheid — associate degree, hbo-bachelor en wo:
  <https://www.rijksoverheid.nl/themas/onderwijs/werken-in-het-onderwijs/vraag-en-antwoord/hoger-onderwijs/wat-zijn-de-bachelor-master-en-associate-degree-in-het-hoger-onderwijs>
- SBB — kwalificatiedossiers voor actuele mbo-beroepen en kerntaken:
  <https://kwalificatie-mijn.s-bb.nl/>
- Vereniging Hogescholen — landelijk domeinprofiel HBO Engineering:
  <https://www.vereniginghogescholen.nl/system/profiles/documents/000/000/273/original/HBO-Engineering_domeinprofiel_2022_def.pdf>


