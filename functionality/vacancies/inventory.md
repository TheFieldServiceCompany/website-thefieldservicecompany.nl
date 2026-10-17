# Vacature-inventaris dev

Inventarisdatum: 16 oktober 2026.

- 63 gepubliceerd, 2 concept.
- Nog geen stabiele `crm_vacancy_id` UUID.
- Twee ontbrekende vacature-nummers.
- Dubbele nummers: 301, 313, 326 en 358.
- Record 22711 heeft conflicterende statustermen.
- Live-only vacature 23645 wacht op volledige bronvelden voordat migratie plaatsvindt.

## Voorgesteld model

- `crm_vacancy_id`: onveranderlijke UUID voor synchronisatie.
- `vacancy_number`: leesbaar nummer voor bezoekers en medewerkers.
- CRM/Supabase beheert operationele vacaturedata en status.
- WordPress beheert publieke tekst, SEO-slug en publicatiepresentatie waar nodig.
- Publiceren, wijzigen en sluiten is idempotent en gelogd.

Dit voorstel wordt pas definitief na vergelijking met de bestaande CRM-code en
databaseobjecten; vanuit deze website-repository worden geen Appsmith-wijzigingen gedaan.

## LIVE interactietest — 17 oktober 2026

De publieke vacaturezoeker is zonder configuratiewijzigingen op desktop en een
mobiele viewport van 390×844 pixels getest.

- De standaardquery toont 47 publieke resultaten met werkende paginering over
  vijf pagina's.
- Het functienaamfilter `Service Engineer` gaf 25 resultaten; paginering naar
  pagina 2 behield het filter en toonde andere resultaten.
- De combinatie `Service Engineer` + `Mechanisch` gaf 22 resultaten en legde
  beide filters correct in de URL vast.
- Het locatiefilter `Rotterdam` gaf 42 resultaten. Het kruis naast de gekozen
  locatie verwijderde alleen dit filter en herstelde de 47 standaardresultaten.
- `Alle filters wissen`, de Meer/Minder-knoppen en de inklapbare filtergroepen
  functioneerden.
- Op mobiel opende en sloot het filterpaneel correct, werd het resultaat direct
  bijgewerkt en kon een actief filter via de filterchip worden verwijderd.
- Een resultaatlink opende de juiste vacaturedetailpagina; de detailpagina
  bevatte een sollicitatielink.

Tijdens deze test is geen functioneel defect aangetroffen.
