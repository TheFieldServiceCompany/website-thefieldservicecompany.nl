# Generieke vacatureselectie via Pods — 2 november 2026

Op gebruikersverzoek volgt snippet 204 uitsluitend de ingestelde Pods-
taxonomierelaties. Er is geen automatische termnaam-/slugkoppeling en geen
vastgelegde functiegroep. De tussentijdse specifieke aanpak uit v3.4 is
verwijderd; definitieve generieke liveversie is **3.5**.

## Regels voor uitsluitend Source vacature

1. Lees alle relationship_*-velden en gebruik hun echte taxonomy/term-ID-data,
   mits die taxonomie bij de gekozen Source hoort. Meerdere termen binnen
   één taxonomie blijven IN; de taxonomiegroepen vormen eerst AND.
2. Heeft de volledige combinatie resultaten, behoud die selectie.
3. Anders: toets elke ingestelde taxonomiegroep met de oorspronkelijke
   Elementor-filters. Laat groepen zonder geschikt aanbod weg.
4. Probeer de overblijvende groepen samen als AND.
5. Hebben ze afzonderlijk aanbod maar geen gezamenlijke doorsnede, gebruik OR
   tussen de overblijvende groepen: minstens één ingestelde relatie moet passen.
6. Geen bruikbare relatie of helemaal geen relatie: behoud het oorspronkelijke
   algemene vacatureaanbod. Ingevulde vacatures blijven uitgesloten als
   Elementor dat al voorschrijft. Overige filters, Source, sortering en
   paginering blijven behouden. Andere Sources blijven strikt. De bestaande
   aparte termarchieftak is niet gewijzigd.

Nieuwe taxonomievelden werken zodra ze als relationship_*-taxonomierelatie
in Pods zijn ingesteld en gekoppeld aan het posttype vacature. Het aanmaken
van een nieuwe groep/term alleen legt geen artikelrelatie: de beheerder
kiest die relatie in Pods. Daarna wordt hij bij de volgende niet-gecachete
render meegenomen; ververs de paginacache na beheerwijzigingen.

## Koeltechnieker, artikel 27838

Artikelindeling beroep_functienaam: alleen Overige functies (775). Onbedoelde
Service Monteur-classificatie 721 en vacature-relatie 420 verwijderd.
relationship_functienaam leeg; relationship_kennisgebied blijft Koeltechniek
325, zichtbare kennisgebiedterm 740 behouden. Er zijn 13 open vacatures in
dat kennisgebied. De tekst, titel, slug, status, auteur, featured media,
introductie en oorspronkelijke publicatiedatum zijn ongewijzigd teruggelezen.
Er is geen taxonomieterm aangemaakt en geen vacature opnieuw ingedeeld.

## Verificatie

- PHP 8.3: 18 gedragstests geslaagd, waaronder willekeurige nieuwe taxonomie,
  nieuwe relationship_*-veldnaam, beide richtingen van terugval, drie groepen,
  AND-voorrang, OR bij conflicterende groepen, lege relaties, statusuitsluiting,
  filters/paginering en bescherming tegen recursie. Geen termnamen/IDs in
  de productie-terugvallogica.
- Echte WordPress-querytests via tijdelijk beheerdersendpoint, na gebruik
  gedeactiveerd/verwijderd: kennisgebied alleen 13; kennisgebied plus lege
  functiegroep 13; passende volledige combinatie 5; twee niet-overlappende
  maar gevulde groepen 15; geen matchende relatie 47 beschikbare vacatures.
  Alle vijf ID-verzamelingen zijn gelijk aan onafhankelijk opgebouwde
  native WP_Query-verwachtingen. Bewijs in audits/evidence/generic-relationship-fallback/.
- Openbare artikelpagina na cacheverversing: 10 zichtbare items, allemaal uit
  de 13 open Koeltechniekvacatures. Drie bestaande passende artikelblokken
  zijn na v3.5 opnieuw vergeleken en exact gelijk gebleven. Eén H1 en
  oorspronkelijke canonical van het aangepaste artikel zijn bevestigd.
- Rank Math en artikelblokken zijn niet herschreven; daarom geen nieuwe
  tabel-/bannereditorcontrole nodig.

## Logging en herstel

Artikelpublicatielog, beide artikelindexen, CSV en Google Sheet (Beroepengids
rij 8 / BEROEP-007) bijgewerkt. XLSX-snapshot is rechtstreeks uit de native
Sheet geëxporteerd. Herstelkopieën: wordpress/exports/code-snippets/
2026-11-02-before-generic-relationship-fallback/.
