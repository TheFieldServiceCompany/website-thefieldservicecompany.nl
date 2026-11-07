# Eigen WordPress-pluginreleases

TFSC Private Updates 0.1.0 draait op productie. Alle twaalf eigen componenten zijn op het private releasekanaal aangesloten. Goedgekeurde hogere versies verschijnen onder **Plugins** en **Dashboard → Updates**; het overzicht staat onder **Gereedschap → TFSC Updates**. De voorziening schakelt automatisch installeren niet in.

| Bestaande pluginidentiteit | Live/gecontroleerde basis op 7 november 2026 | Bronref |
| --- | --- | --- |
| `tfsc-custom-functions/tfsc-custom-functions.php` | 0.6.0 | [`0479d7e`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/0479d7e2353a8ce9ae66e2b56f7056122120f5a4/wordpress/plugins/tfsc-custom-functions) |
| `tfsc-media-filters/tfsc-media-filters.php` | 1.0.0 | [`0479d7e`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/0479d7e2353a8ce9ae66e2b56f7056122120f5a4/wordpress/plugins/tfsc-media-filters) |
| `tfsc-rank-math-breadcrumbs/tfsc-rank-math-breadcrumbs.php` | 0.3.1 | [`90ac87b`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/90ac87bfe48b9313d9426be08f350814ea74a289/wordpress/plugins/tfsc-rank-math-breadcrumbs) |
| `tfsc-schema-read-endpoint/tfsc-schema-read-endpoint.php` | 2.1.0 | [`0479d7e`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/0479d7e2353a8ce9ae66e2b56f7056122120f5a4/wordpress/plugins/tfsc-schema-read-endpoint) |
| `updraft-pcloud-copy/updraft-pcloud-copy.php` | 0.2.4 | [`0479d7e`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/0479d7e2353a8ce9ae66e2b56f7056122120f5a4/wordpress/plugins/updraft-pcloud-copy) |
| `tfsc-private-updates/tfsc-private-updates.php` | 0.1.0 | [`80066c3`](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/tree/80066c3dbc7bbdbd48b6847b0678e4dd56a6bc6e/wordpress/plugins/tfsc-private-updates) |

De updaterbron en testresultaten staan in [PR 11](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/pull/11); de breadcrumbbasis staat op de vermelde bronref. Behoud de live CSS van Custom Functions bij nieuw werk. Updraft to pCloud is live 0.2.4; 0.2.5 in de hoofdbranch voegt lokale back-upopruiming toe en mag niet als een reeds geïnstalleerde beveiligingsfix worden voorgesteld.

Volledige huidige ZIP's, SHA-256 en geïnstalleerde bestandshashes staan in de [besloten centrale registratie](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/plugin-release-registry.json). Controleer de actuele live toestand bij volgend werk; dit is de vastgelegde basis van 7 november.

Volg de [centrale werkwijze](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/PLUGIN-UPDATES.md): gecontroleerde basis → codewijziging en nieuwe versie → compleet pakket → relevante functionele en beveiligingstests → beoordeelde checksum → vrijgeven via bestaande beveiligde WordPress-REST-toegang → controleren → normale WordPress-update. Archiveer de exacte release en werk registratie en plaatsingsverslag bij. Een GitHub-commit publiceert geen pakket; een pakketvrijgave installeert niets.

Het [publicatiehulpmiddel](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/blob/80066c3dbc7bbdbd48b6847b0678e4dd56a6bc6e/wordpress/plugins/tfsc-private-updates/tools/publish-release.py) geeft standaard alleen een lokaal voorbeeld en controleert de vooraf vastgelegde checksum. Gebruik bestaande bevoegde toegang; zet geen GitHub-token, WordPress-wachtwoord, salts of echte bedrijfsgegevens in de repository of het pakket.
