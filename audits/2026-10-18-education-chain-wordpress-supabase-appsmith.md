# Opleidingenketen: WordPress, Supabase en Appsmith

Datum: 18 oktober 2026  
Status: ontwerp goedgekeurd; eerste WordPress–Supabase-synchronisatie uitgevoerd en gecontroleerd

## Goedgekeurd model

Houd opleidingsniveau en studierichting in het vacature-, Supabase- en
Appsmithmodel als twee afzonderlijke gegevensdimensies:

1. **Opleidingsniveau** — het niveau waarop iemand is opgeleid of waarop een
   vacature is gericht.
2. **Studierichting** — het vakgebied van de opleiding.

Deze scheiding sluit al aan op WordPress, Supabase en Appsmith. In de
Beroepengids worden ze als twee duidelijke takken gespiegeld binnen één
hiërarchische Pods-taxonomie `beroep_opleiding`: de parenttermen
`Opleidingsniveau` en `Studierichting` krijgen daaronder respectievelijk de
vier niveaus en 23 studierichtingen. Het wordt dus geen platte menglijst.

## Besluiten na inhoudelijke bespreking

- De publieke niveau-indeling blijft compact: `VMBO / LBO`, `MBO`, `HBO` en
  `WO / Universiteit`.
- LBO wordt met VMBO in één historisch en praktisch filter ondergebracht. MBO
  blijft een afzonderlijk niveau: VMBO bereidt voor op het mbo en is er niet
  mee samengevoegd.
- Een Associate degree (Ad) wordt voor TFSC niet als afzonderlijk publiek
  vacaturefilter gebruikt, maar onder de brede HBO-selectie meegenomen. De
  precieze diplomanaam kan in de ruwe kandidaatgegevens behouden blijven.
- De brede zichtbare studierichting `ICT & Technische Informatica` blijft
  gehandhaafd. De huidige Supabase-naam `Technische Informatica / ICT` moet
  later naar die canonieke naam worden gemapt of hernoemd.
- `Mobiele werktuigen` is een geldige opleidingsrichting. Voor de termenlijst
  heeft `Mobiele werktuigentechniek` de voorkeur, met onder meer `Mobiele
  Werktuigen`, `Technicus mobiele werktuigen` en `Voertuigen en mobiele
  werktuigen` als herkenbare varianten.

## WordPress: huidige vacaturetaxonomieën

### Opleidingsniveau

De vacaturetaxonomie `opleidingsniveau` bevat vier gebruikte termen:

| WordPress-term | Term-ID | Gebruik op 63 vacatures | Voorgestelde Supabase-match |
| --- | ---: | ---: | --- |
| HBO | 69 | 41 | HBO |
| MBO | 70 | 63 | MBO |
| Universiteit | 71 | 12 | WO / Universiteit |
| LBO | 210 | 36 | VMBO / LBO |

Na het herstel van 17 oktober hebben alle 63 gepubliceerde vacatures weer hun
oorspronkelijke opleidingsniveaukoppelingen.

### Studierichting

De vacaturetaxonomie `vac_studierichting` bevat achttien termen, maar geen
enkele gepubliceerde vacature is er momenteel aan gekoppeld.

Vijftien termen zijn naamgelijk aan Supabase. Eén term is inhoudelijk dezelfde
term met een andere schrijfwijze:

- WordPress: `ICT & Technische Informatica`;
- Supabase: `Technische Informatica / ICT`.

De resterende verschillen zijn:

| Alleen WordPress | Alleen Supabase |
| --- | --- |
| Embedded Systems | Civiele Techniek |
| Robotica | Mobiele Werktuigen |

Dit zijn besluitpunten voor de canonieke lijst; er is nog niets toegevoegd,
verwijderd of hernoemd.

## Supabase: bestaande structuur

Het gewenste model bestaat al:

- `education_levels` bevat vijf actieve niveaus: Associate degree (Ad), HBO,
  MBO, VMBO / LBO en WO / Universiteit;
- `education_fields` bevat achttien actieve studierichtingen;
- `vacancy_education_requirements` koppelt vacatures afzonderlijk aan een
  niveau of studierichting en kan daarnaast belang en harde eis vastleggen;
- `candidate_education` gebruikt dezelfde canonieke niveaus en
  studierichtingen voor kandidaten.

De vulling is nog niet productierijp:

- Supabase bevat acht testvacatures en geen WordPress-ID-koppelingen voor
  vacatures;
- slechts één testvacature heeft twee opleidingseisen: MBO en Elektrotechniek;
- alle 3.544 kandidaat-opleidingsregels hebben een opleidingsniveau, maar nog
  geen enkele heeft een canonieke studierichting;
- `source_term_mappings` is leeg, dus de WordPress-term-ID's en -slugs zijn nog
  niet expliciet aan de Supabase-UUID's gekoppeld.

## Appsmith: bestaande ondersteuning

Appsmith heeft op de vacaturedetailpagina al twee afzonderlijke
multiselectvelden:

- `Education levels`;
- `Education fields`.

De bestaande opslagfunctie valideert de gekozen UUID's en schrijft niveaus en
studierichtingen als afzonderlijke regels naar
`vacancy_education_requirements`. Ook de vacaturematching kan al op beide
dimensies filteren. Het model hoeft daarom niet opnieuw te worden gebouwd.

Nog nodig:

- de zichtbare labels, placeholders en foutmeldingen naar goed Nederlands
  omzetten;
- de canonieke termen en WordPress-mappings vastleggen;
- de WordPress-vacatures eerst betrouwbaar aan Supabase-vacatures koppelen;
- bestaande niveaus gecontroleerd importeren en studierichtingen inhoudelijk
  per vacature beoordelen;
- kandidaat-studierichtingen later gecontroleerd normaliseren;
- de WordPress-synchronisatie en end-to-endtests toevoegen.

## Voorgestelde bronverdeling

- **Supabase** wordt de canonieke gegevensbron voor niveaus,
  studierichtingen en vacature-opleidingseisen.
- **Appsmith** wordt de beheerinterface waarmee deze gestructureerde eisen
  worden ingevuld en gewijzigd.
- **WordPress** ontvangt dezelfde keuzes voor publieke filters, SEO-archieven,
  Beroepengidsrelaties en vacatureweergave.

De koppeling moet op stabiele ID's worden gebaseerd, niet alleen op namen. De
bestaande tabel `source_term_mappings` kan daarvoor WordPress-term-ID en
taxonomy-slug aan de Supabase-UUID koppelen. Voor vacatures is daarnaast een
expliciete WordPress-post-ID-koppeling nodig in `external_identifiers`.

## Veilige uitvoeringsvolgorde

1. De vier publieke niveaus en de uitgebreide lijst met studierichtingen
   definitief beoordelen, inclusief naamvarianten en synoniemen.
2. De definitieve namen, slugs, synoniemen en sortering vaststellen.
3. WordPress-termen en Supabase-concepten via stabiele mappings koppelen.
4. De 63 WordPress-vacatures aan de juiste Supabase-vacatures koppelen of
   gecontroleerd importeren; de bestaande acht testvacatures niet als live
   gegevens behandelen.
5. De herstelde opleidingsniveaus van WordPress naar Supabase overnemen en
   exact controleren.
6. Studierichtingen per vacature inhoudelijk invullen; vacaturetekst mag een
   voorstel opleveren, maar de keuze wordt gecontroleerd.
7. Appsmith Nederlandstalig maken en de bewaar-, herlaad- en matchingwerking
   testen.
8. De gekozen waarden vanuit Supabase naar de twee WordPress-taxonomieën
   synchroniseren en REST, filters, snippets, sitemap en SEO-archieven testen.
9. Daarna de spiegelende Beroepengidsstructuur voor Opleidingen afmaken.

## Uitvoering op 18 oktober 2026

### WordPress

- Term-ID `210` zichtbaar hernoemd van `LBO` naar `VMBO / LBO`; de bestaande
  slug `lbo` en alle 36 vacaturekoppelingen zijn behouden.
- Term-ID `71` zichtbaar hernoemd van `Universiteit` naar `WO / Universiteit`;
  de bestaande slug `universiteit` en alle 12 vacaturekoppelingen zijn
  behouden.
- Aan `vac_studierichting` zijn vijf termen toegevoegd:
  - `Industriële Automatisering` — term-ID `564`;
  - `Civiele Techniek` — term-ID `565`;
  - `Mobiele Werktuigentechniek` — term-ID `566`;
  - `Koude- en Klimaattechniek` — term-ID `567`;
  - `Operationele Techniek` — term-ID `568`.
- De eindcontrole via WordPress REST geeft exact vier niveaus en 23
  studierichtingen. De nieuwe studierichtingen hebben nog geen
  vacaturekoppelingen; inhoudelijke indeling volgt afzonderlijk.

### Supabase

- `Associate degree (Ad)` is gedeactiveerd als zelfstandig filter. De term had
  nul kandidaat- en nul vacaturekoppelingen; er is dus geen bestaande relatie
  verloren gegaan.
- De vier actieve niveaus hebben nu een vaste sortering: VMBO / LBO, MBO, HBO
  en WO / Universiteit.
- `Technische Informatica / ICT` is hernoemd naar
  `ICT & Technische Informatica`.
- `Mobiele Werktuigen` is hernoemd naar `Mobiele Werktuigentechniek`.
- Embedded Systems, Robotica, Industriële Automatisering, Koude- en
  Klimaattechniek en Operationele Techniek zijn toegevoegd. Civiele Techniek
  en Mobiele Werktuigentechniek bestonden inhoudelijk al in Supabase.
- `source_term_mappings` bevat nu vier unieke mappings voor
  `opleidingsniveau` en 23 unieke mappings voor `vac_studierichting`; iedere
  WordPress-term-ID verwijst naar precies één Supabase-UUID.
- Alle betrokken tabellen hadden en houden RLS ingeschakeld.

### Appsmith-controle

- De bestaande vacaturefunctie levert na de synchronisatie vier niveauopties
  en 23 studierichtingopties.
- De bestaande kandidaatfunctie levert dezelfde vier niveauopties en 23
  studierichtingopties.
- Er bestaan geen kandidaat- of vacaturekoppelingen naar het gedeactiveerde
  Ad-record.
- De Appsmith-UI is nog niet gewijzigd. Nederlandse labels, opslagtests,
  matchingtests en verdere kandidaat-/vacaturevulling zijn de volgende stap.

### Pods en Beroepengids

Pods is nog niet gewijzigd. Het goedgekeurde ontwerp gebruikt één
hiërarchische Beroepengids-taxonomie `beroep_opleiding` met de parenttermen
`Opleidingsniveau` en `Studierichting`. De vacaturetaxonomieën blijven wel
gescheiden. Er komt geen gewone Beroepengidspagina: de ingang
`/beroepengids/opleidingen/` wordt hetzelfde soort virtueel taxonomy-
basisarchief als functienamen, functie-inhoud en bedrijfssectoren. De volledige
snippetimpact en veilige uitvoeringsvolgorde staan in
`2026-10-18-beroepengids-opleidingen-pods-snippet-impact.md`.

## Redactionele voorbereiding op 18 oktober 2026

- Een herbruikbare productieprompt vastgelegd met eisen voor feitelijke
  onderwijsinformatie, natuurlijke SEO, AI-leesbare vraagkoppen, jij-vorm en
  concrete koppeling aan field service.
- Alle 23 studierichtingen en alle vier opleidingsniveaus hebben een unieke
  conceptdescription van 450–486 woorden gekregen.
- Iedere tekst bevat exact vier vraaggerichte H2-secties. Er zijn geen formele
  `u`-aansprekingen, verboden opvulzinnen of dubbele bodyregels aangetroffen.
- De vier niveauteksten mogen verwijzen naar actuele TFSC-vacatures omdat hun
  WordPress-termen koppelingen hebben. De studierichtingteksten doen nog geen
  dergelijke claim: `vac_studierichting` heeft nog nul vacaturekoppelingen.
- Publicatie wacht op de goedgekeurde Pods-structuur. De vacaturetaxonomieën
  worden niet misbruikt als opslagplaats voor Beroepengidsdescriptions.
- Bronnen, publicatievoorwaarden en dynamische CTA-regels staan in
  `content/education-descriptions/README.md`; de teksten zelf staan in
  `opleidingsniveaus.md` en `studierichtingen.md` in dezelfde map.

