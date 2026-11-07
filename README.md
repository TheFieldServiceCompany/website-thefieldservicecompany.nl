# The Field Service Company — website

Technische documentatie en broncode voor `dev.thefieldservicecompany.nl`.

## Status

De geïnventariseerde WordPress-snippets staan op `main` en zijn gescheiden van
ongewijzigde, gedateerde exports uit WordPress. Bestanden in deze repository
worden niet automatisch naar WordPress of Elementor uitgerold.

## Indeling

| Map | Bedoeld voor |
| --- | --- |
| `wordpress/snippets/php/` | Beheerde PHP-snippets, één bestand per snippet |
| `wordpress/snippets/javascript/` | Beheerde JavaScript-snippets |
| `wordpress/snippets/css/` | Beheerde CSS-aanpassingen |
| `wordpress/elementor/custom-code/` | Elementor Custom Code |
| `wordpress/exports/code-snippets/` | Ongewijzigde, gedateerde exports uit de Code Snippets-plugin |
| `functionality/forms/` | Formulieren |
| `functionality/vacancies/` | Vacatures |
| `functionality/candidates/` | Kandidatenfunctionaliteit |
| `functionality/integrations/` | Koppelingen |
| `functionality/automation/` | Automatisering |
| `seo/` | URL-structuur, redirects en SEO-migratie |
| `deployment/` | Dev-omgeving, overgang naar productie en rollback |

De beheerde bestanden onder `wordpress/snippets/` zijn de leesbare broncode.
Gebruik exports alleen als momentopname, herstelbron of vergelijkingsmateriaal.

Leg bij wijzigingen doel, uitvoerlocatie, afhankelijkheden, WordPress-snippet-ID
en wijzigingsreden vast.

Bewaar hier geen wachtwoorden, API-sleutels, licentiesleutels, persoonsgegevens
of databaseback-ups.

## WordPress-updatekanaal

De eigen WordPress-componenten gebruiken **TFSC Private Updates**. Lees vóór ontwikkeling of release [WORDPRESS-RELEASES.md](WORDPRESS-RELEASES.md) en de [actuele basis-/bronregistratie](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/plugin-release-registry.json). Een hoofdbranch kan afwijken van productie; complete, geteste pakketten worden afzonderlijk vrijgegeven en daarna via de gewone WordPress-updateknop geïnstalleerd.
