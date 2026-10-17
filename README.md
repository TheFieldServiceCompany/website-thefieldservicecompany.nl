# The Field Service Company — website

Technische documentatie en broncode voor `dev.thefieldservicecompany.nl`.

## Status

De eerste inventaris van dev is uitgevoerd op 16–17 oktober 2026. De huidige eigen
plugins, actieve Code Snippets, Elementor Custom Code (zonder geheimen), het
pluginmanifest en de belangrijkste auditbevindingen zijn vastgelegd. De live-site
is nog niet vervangen en de bestaande formulierstroom blijft actief totdat de
nieuwe connector end-to-end is getest.

## Indeling

| Map | Bedoeld voor |
| --- | --- |
| `snippets/php/` | PHP-snippets |
| `snippets/javascript/` | JavaScript-snippets |
| `snippets/css/` | CSS-aanpassingen |
| `snippets/wordpress/` | WordPress-specifieke snippets |
| `snippets/README.md` | Functie, status en afhankelijkheden van alle eigen code |
| `functionality/content-taxonomy-logic.md` | Besluiten over locaties, taxonomieën, Beroepengids en content |
| `functionality/forms/` | Formulieren |
| `functionality/vacancies/` | Vacatures |
| `functionality/candidates/` | Kandidatenfunctionaliteit |
| `functionality/integrations/` | Koppelingen |
| `functionality/automation/` | Automatisering |
| `wordpress/` | Plugins, licentie-afhankelijkheden, Elementor en configuratie |
| `seo/` | URL-structuur, redirects en SEO-migratie |
| `deployment/` | Dev-omgeving, overgang naar productie en rollback |
| `wp-content/plugins/` | Eigen WordPress-plugins zoals aangetroffen op dev |
| `audits/` | Gedateerde, niet-gevoelige auditrapporten |

Bij het toevoegen van snippets leggen we ook doel, uitvoerlocatie,
afhankelijkheden, datalogica, testwijze en wijzigingsreden vast.

Bewaar hier geen wachtwoorden, API-sleutels, licentiesleutels, persoonsgegevens of databaseback-ups.
