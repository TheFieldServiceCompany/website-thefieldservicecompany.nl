# WordPress-releases en TFSC Private Updates

Deze instructies gelden voor de eigen WordPress-componenten in dit project. Bestaande projectregels voor andere onderdelen blijven gelden.

Lees vóór ontwikkeling, verpakking of publicatie [WORDPRESS-RELEASES.md](WORDPRESS-RELEASES.md), de [centrale releasewerkwijze](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/PLUGIN-UPDATES.md) en de [live basis-/bronregistratie](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/plugin-release-registry.json). Een hoofdbranch is niet automatisch de productieversie. Behoud de gecontroleerde basis, eerdere beveiligingsreparaties, exacte pluginmap/hoofd-PHP-identiteit en actuele assets.

Maak volledige versiegebonden ZIP's, test de gewijzigde normale werkstroom en relevante beveiligings-/compatibiliteitsgrenzen, leg de beoordeelde SHA-256 vast en publiceer uitsluitend binnen de opdrachttoestemming via het bestaande TFSC-updatekanaal. Een broncommit, pakketvrijgave en installatie zijn aparte stappen. Wijzig geen auto-updatekeuzes automatisch. Geen aanval-/stresstests of echte formulierproeven op productie. Bewaar geen credentials, salts, echte formuliergegevens of private toegangslocaties in GitHub.
