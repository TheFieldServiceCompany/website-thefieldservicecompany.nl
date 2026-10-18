# Elementor-relatiequery: bestaande filters behouden

Datum: 18 oktober 2026  
Omgeving: LIVE  
Query ID: `relationship_posts`

## Probleem

Op Beroepengids-termarchieven kon een ingevulde vacature in de gerelateerde
vacaturelijst verschijnen. Het concrete voorbeeld was:

- vacature-ID 15530;
- titel: `Internationaal Service Monteur Maritiem`;
- status: term-ID 258, `Ingevuld`;
- zichtbaar op het archief `Maritieme techniek`.

Elementor had op de vacaturequery al een uitsluiting voor de status
`Ingevuld`. Actieve snippet 204 bouwde daarna de Pods-relatiequery, maar zette
die met `$query->set( 'tax_query', $relationship_query )` als volledige nieuwe
taxonomiequery. Daarmee werd de bestaande Elementor-query overschreven.

## Veilige wijziging

Vooraf is via Code Snippets een inactieve kopie gemaakt:

- snippet 240;
- `Related Posts to Query - bijv Vac Posts bij een Query op related Taxonomies. [v3.0] [BACKUP 2026-10-18 vóór behoud Elementor-filters]`.

Daarna is actieve snippet 204 via de officiële Code Snippets REST-route naar
versie 3.1 gebracht. De slotlogica is nu conceptueel:

```php
$existing_tax_query = $query->get( 'tax_query' );

$combined_tax_query = [
    'relation' => 'AND',
    $existing_tax_query,
    $relationship_query,
];

$query->set( 'tax_query', $combined_tax_query );
```

Wanneer Elementor nog geen eigen `tax_query` heeft, blijft alleen de bestaande
relatiequery actief. De geneste opbouw is bewust gekozen: zo blijven zowel de
interne relatie van de Elementor-query als de `AND`/`IN`-logica van de
relatiequery behouden.

## Functionele controle

### Studierichting Maritieme techniek

URL: `/beroepengids/studierichtingen/maritieme-techniek/`

- vóór de wijziging: zowel de open vacature *Field Service Engineer Duikboten
  | Internationaal Allround* als de ingevulde vacature *Internationaal Service
  Monteur Maritiem* stonden in de HTML;
- na de wijziging: de open vacature bleef zichtbaar en de ingevulde vacature
  verdween.

### Opleidingsniveau MBO

URL: `/beroepengids/opleidingsniveaus/mbo/`

- *Service Monteur Mechanisch Benelux | Separatoren & Decanters* bleef
  zichtbaar;
- *Field Service Engineer Duikboten | Internationaal Allround* bleef
  zichtbaar;
- de ingevulde vacature *Internationaal Service Monteur Maritiem* werd niet
  aangetroffen.

De controles zijn met unieke queryparameters rechtstreeks op de oorsprong
uitgevoerd om een verouderde QUIC/LiteSpeed-respons uit te sluiten.

## Eindstatus

- snippet 204: actief, versie 3.1;
- snippet 240: inactieve herstelkopie;
- Code Snippets `code_error`: leeg;
- vacaturedata, Pods-relaties, Elementor-template-instellingen en taxonomieën
  zijn niet gewijzigd.
