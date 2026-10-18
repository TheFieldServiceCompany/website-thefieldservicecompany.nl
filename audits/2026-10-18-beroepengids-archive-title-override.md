# Beroepengids — generieke archive-title-override

Datum: 18 oktober 2026

Site: `https://thefieldservicecompany.nl`

Status: uitgevoerd en functioneel gecontroleerd; onderhoudsmodus bleef actief

## Bestaande vacaturewerking

De vacaturetaxonomie-Pod `functienaam` bevatte al de groep `Archive` met het
Plain Text-veld `pods_archive_title`. Actieve snippet 186 leest dit veld voor
de shortcode `[combo_heading]`. Bij een leeg veld gebruikt de vacaturekant de
termnaam met het achtervoegsel `vacatures`.

Het Beroepengids-termtemplate 24908 gebruikt voor de H1 Elementors dynamische
tag **Archive Title**. Die loopt via filter
`elementor/utils/get_the_archive_title` in snippet 39. Dit is daarom de
centrale plaats voor de Beroepengids-override; het Elementor-template hoefde
niet te worden gewijzigd.

## Toegevoegde Pods-velden

Aan iedere actieve Beroepengids-taxonomie-Pod is exact dezelfde groep en
veldconfiguratie toegevoegd:

- groepnaam `archive`, label `Archive`, alleen beheerders;
- veldnaam en label `pods_archive_title`;
- type Plain Text, maximaal 255 tekens;
- beschrijving `Heading text for archive`;
- niet verplicht en alleen beheerders.

| Pod | Groep-ID | Veld-ID |
| --- | ---: | ---: |
| `beroep_bedrijfssector` | 27620 | 27621 |
| `beroep_functie-inhoud` | 27622 | 27628 |
| `beroep_functienaam` | 27626 | 27631 |
| `beroep_kennisgebied` | 27623 | 27633 |
| `beroep_opleidingsniveau` | 27625 | 27629 |
| `beroep_studierichting` | 27624 | 27632 |
| `beroep_werkgebied` | 27627 | 27630 |

De verouderde container-Pod `beroep_opleiding` is bewust niet aangepast.
Bestaande velden, relaties en door de beheerder gecorrigeerde veldnamen zijn
niet gewijzigd.

## Snippet 39

Actieve snippet 39 is bijgewerkt naar versie 2.0. Op een gewoon
custom-taxonomytermarchief geldt nu:

1. lees `pods_archive_title` uit WordPress-termmeta;
2. verwijder HTML en omliggende witruimte;
3. gebruik de waarde als deze niet leeg is;
4. gebruik anders ongewijzigd de normale termnaam.

Voor de wijziging is in WordPress een inactieve veiligheidskopie gemaakt:
snippet 232. Het lokale bronbestand is
`snippets/php/39-archive-title-without-prefix.php`.

## Functionele test

Term 437 (`field-service-engineer`) is gebruikt voor een volledig herstelde
test:

- leeg veld: H1 `field service engineer`;
- tijdelijk ingevuld: H1 `Tijdelijke archieftiteltest`;
- testwaarde verwijderd: H1 opnieuw `field service engineer`.

De tijdelijke admin-only testsnippet 233 is na de test gedeactiveerd. De
testterm heeft weer een lege overridewaarde. Snippet 39 bleef actief en meldde
geen codefout. De eerder nog actieve tijdelijke Rank Math-debugsnippet 231 is
tijdens de afsluitende opruimcontrole eveneens gedeactiveerd.

## Afbakening

De wijziging beïnvloedt alleen de zichtbare Elementor Archive Title/H1 op
termarchieven. Zij wijzigt niet:

- de WordPress-termnaam of slug;
- de URL of rewrite;
- breadcrumbs;
- Rank Math SEO-title of meta description;
- de virtuele taxonomy-basearchieven zoals
  `/beroepengids/functienamen/`;
- de `[archive_title]`-shortcode in snippet 203 en daarmee de bestaande
  formuleringen in tussenkoppen en lopende tekst.
