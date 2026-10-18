# Opleidingenketen: WordPress, Supabase en Appsmith

Datum: 18 oktober 2026  
Status: alleen-lezen controle en ontwerpvoorstel; geen data of configuratie gewijzigd

## Aanbevolen model

Gebruik `Opleidingen` als overkoepelend onderwerp of ingang in de
Beroepengids, maar houd de onderliggende gegevens in twee afzonderlijke
dimensies:

1. **Opleidingsniveau** — het niveau waarop iemand is opgeleid of waarop een
   vacature is gericht.
2. **Studierichting** — het vakgebied van de opleiding.

Deze scheiding sluit al aan op WordPress, Supabase en Appsmith. Niveau en
studierichting mogen daarom niet als gelijksoortige termen in één platte
taxonomie worden samengevoegd.

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

1. Samen de vijf niveaus en twintig unieke voorgestelde studierichtingen
   beoordelen, inclusief de vier verschillen tussen beide systemen.
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

