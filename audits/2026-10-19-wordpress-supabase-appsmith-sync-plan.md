# Synchronisatieplan — WordPress, Supabase en Appsmith

Datum: 19 oktober 2026  
Status: centrale werkwijze; alleen-lezen inventarisatie gestart

## Doel

WordPress, Supabase en Appsmith moeten voor vacatures en kandidaten dezelfde
canonieke betekenissen gebruiken, zodat invoer, filtering en matching niet van
verschillende namen of dubbele keuzes afhangen.

- Supabase is de canonieke datalaag.
- Appsmith beheert die Supabase-records.
- WordPress gebruikt geschikte publieke labels, archieven en filters.
- Een WordPress-zoekterm hoeft niet als extra canonieke waarde in Supabase te
  bestaan; hij mag als alias of tekstvariant naar dezelfde betekenis wijzen.
- Langere en beschrijvende zoekvragen horen bij voorkeur in sterke
  landingspagina's of Beroepengidsartikelen met een dynamisch vacatureblok,
  niet als extra filterwaarde.

## Vier soorten synchronisatie

1. **Exacte termmapping:** één WordPress-term naar één canoniek Supabase-record,
   bijvoorbeeld opleidingsniveaus en studierichtingen.
2. **Alias of synoniem:** meerdere publieke namen naar één canoniek concept,
   bijvoorbeeld Nederlandse en Engelse functie-inhoudvarianten.
3. **Afgeleid veld of band:** een publiek WordPress-label is een weergave van
   een getal of ja/nee-veld, bijvoorbeeld Dagen internationaal en
   Reisprofielen.
4. **Relatie naar een hiërarchie:** WordPress Werkgebied wijst naar
   `geographic_areas`, niet naar een tweede losse landenlijst.

## Verplichte controlematrix per taxonomie

Voor iedere WordPress-taxonomie wordt minimaal vastgelegd:

- taxonomy-slug, term-ID, naam, slug, actief/inactief en gebruiksteller;
- beschrijving, archief-URL, indexeerbaarheid en benodigde redirect;
- canonieke Supabase-tabel, record-ID of doelveld;
- mappingtype: exact, alias, afgeleid, te beoordelen of verwijderen;
- Appsmith-invoer, filter en matchgebruik;
- verschillen, conflicten en te migreren bestaande relaties;
- verificatie vóór en na uitvoering.

Mappings worden waar mogelijk in `source_term_mappings` vastgelegd. Afgeleide
velden krijgen expliciete conversieregels; ze worden niet als nep-vocabulaire
gekopieerd.

## Huidige bevestigde status

| Onderdeel | Supabase/Appsmith-model | Status |
| --- | --- | --- |
| Opleidingsniveau | `education_levels` | 4 actieve WordPress-mappings aanwezig; opnieuw valideren in eindcontrole |
| Studierichting | `education_fields` | 23 actieve WordPress-mappings aanwezig; Appsmith-repository is hierop uitgelijnd |
| Reisprofiel | Gestructureerde travel-velden | 25 termen geïnventariseerd; herzien voorstel is de gemengde lijst te vervangen door meetbare Reisduur-banden en percentages Weekenden thuis |
| Dagen internationaal | Numerieke travel-velden | Bandmapping bestaat inhoudelijk, maar moet als formele conversieregel worden vastgelegd |
| Werkgebied | `geographic_areas` en werkgebiedrelaties | Canonieke landen/regio's bestaan; volledige WordPress-mapping en relaties nog controleren |
| Mobiliteit | Aparte mobility-tabellen | Bewust buiten Reisprofiel houden |
| Overige vacaturetaxonomieën | Diverse canonieke tabellen | Bestaande centrale taxonomie-inventaris per onderwerp beoordelen en bevestigen |
| Beroepengids-spiegels | WordPress-publicatielaag | Pas opbouwen vanuit de goedgekeurde canonieke set |

De actuele Supabase-controle toont 4 mappings voor `opleidingsniveau` en 23
voor `vac_studierichting`, alle actief. Er zijn nog geen mappings voor
`reisprofiel`.

## Werkvolgorde

1. Reisprofielen en de meetbare filterdimensies `Reisduur` en `Weekenden thuis`
   besluiten, veilig omzetten en verifiëren. Overnachten wordt dan uit
   Reisduur afgeleid; het accommodatietype niet. `Hotelovernachtingen` blijft
   daarnaast een contentzoekwoord.
2. Dagen internationaal als numerieke/bandmapping formaliseren.
3. Werkgebieden, landen en regio's volledig gelijk trekken.
4. Opleidingsniveaus en studierichtingen opnieuw end-to-end valideren:
   WordPress-term, `source_term_mappings`, Supabase-record, Appsmith-optie en
   bestaande vacature-/kandidaatrelaties.
5. De overige vacaturetaxonomieën één voor één behandelen, te beginnen bij de
   filters met bestaand gebruik.
6. Pas daarna eventuele Beroepengids-spiegels en de centrale archievenpagina
   vanuit de goedgekeurde set opbouwen. Beschrijvende reisthema's kunnen als
   Beroepengidsartikel of landingspagina een dynamisch vacatureblok krijgen.
7. Een herhaalbaar alleen-lezen synchronisatierapport maken dat ontbrekende,
   inactieve, dubbele en niet-gemapte waarden meldt.

## Veiligheidsgrenzen

- Geen term of Supabase-record verwijderen op basis van alleen een naammatch.
- Eerst relaties omzetten, daarna redirects aanbrengen, pas dan oude termen
  verwijderen.
- Geïmporteerde kandidaatdata niet overschrijven zonder bron- en
  betekeniscontrole.
- WordPress, Supabase en Appsmith niet tegelijk wijzigen zonder voorafgaande
  controlematrix en terugvalplan.
- Geen persoonsgegevens, bronexports of cv-inhoud in GitHub opnemen.

Tijdens deze inventarisatie is niets gewijzigd in WordPress, Supabase of
Appsmith.
