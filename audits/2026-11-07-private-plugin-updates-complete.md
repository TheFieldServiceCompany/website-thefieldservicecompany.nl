# Alle twaalf pluginreleases aangesloten

**Op 7 november 2026 zijn ook de negen ontbrekende basispakketten vrijgegeven. Alle twaalf eigen componenten tonen nu dezelfde geïnstalleerde en vrijgegeven versie en de status Bijgewerkt.** De 96 aanwezige pluginbestanden, bestaande instellingen en auto-updateselectie zijn behouden. De aanvankelijke drie pakketten blijven ongewijzigd.

| Plugin | Geïnstalleerd én vrijgegeven |
| --- | --- |
| Cache Warmer | 0.6.4 |
| Combo Archive Manager | 0.5.0 |
| Crawler Log | 1.4.5 |
| Custom Functions | 0.6.0 |
| Form Bridge | 0.5.6 |
| Mediafilters | 1.0.0 |
| Rank Math Breadcrumbs | 0.3.1 |
| Schema Read Endpoint | 2.1.0 |
| Server Location Test Probe | 0.1.2 |
| Structured Data | 0.5.3 |
| Updraft to pCloud | 0.2.4 |
| Private Updates | 0.1.0 |

De negen nieuwe archieven bevatten de volledige actuele pluginmap, met CSS en overige aanwezige bestanden. Alleen de oude beperkte bronselectie verpakken zou assets missen. De volledige boom is daarom eerst als metadata gelezen, beoordeeld en vervolgens met een vaste lijst bestandshashes opgehaald. De 87 reeds gecontroleerde bronbestanden zijn identiek aan de gerepareerde auditbasis; de extra bestanden zijn onder meer CSS, licentie- en Git-ignore-documentatie. De complete bronlijst telt 96 bestanden.

Vrijgeven betekent dat WordPress het pakket kent en een hogere versie kan aanbieden. Deze aansluiting vervangt geen huidige code en maakt geen kunstmatige update nodig. De native updategegevens bieden geen van deze gelijke huidige versies onnodig opnieuw aan. De automatische installatie blijft uit; bestaande keuzes zijn niet veranderd.

## Vastgelegd voor toekomstig ontwikkelwerk

De [centrale releasewerkwijze](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/PLUGIN-UPDATES.md), [registratie](https://github.com/TheFieldServiceCompany/architecture/blob/main/wordpress/plugin-release-registry.json) en twaalf exacte ZIP's staan op de hoofdbranch van het besloten architectuurproject. De registratie koppelt pluginidentiteit, huidige basisversie, bronproject/-ref, archiefchecksum en geïnstalleerde bestandshashes.

In alle zeven bronprojecten staan op de hoofdbranch een startinstructie voor ontwikkelagenten, `WORDPRESS-RELEASES.md` en een zichtbare verwijzing in de README:

- [Website: zes eigen componenten](https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl/blob/main/WORDPRESS-RELEASES.md).
- [Cache Warmer](https://github.com/TheFieldServiceCompany/cache-warmer/blob/main/WORDPRESS-RELEASES.md).
- [Combo Archive Manager](https://github.com/TheFieldServiceCompany/tfsc-combo-archive-manager/blob/main/WORDPRESS-RELEASES.md).
- [Crawler Log](https://github.com/TheFieldServiceCompany/tfsc-ai-crawler-log/blob/main/WORDPRESS-RELEASES.md).
- [Form Bridge in CRM/ATS](https://github.com/TheFieldServiceCompany/tfsc-crm-ats/blob/main/WORDPRESS-RELEASES.md).
- [Serverprobe](https://github.com/TheFieldServiceCompany/server-location-test-probe-wordpress/blob/main/WORDPRESS-RELEASES.md).
- [Structured Data](https://github.com/TheFieldServiceCompany/tfsc-structured-data/blob/main/WORDPRESS-RELEASES.md).

De bestanden zijn na plaatsing teruggelezen en met de bedoelde inhoud vergeleken. Deze documentatie publiceert geen codewijzigingen op WordPress en verandert de bestaande Appsmith Git Sync- of CRM-regels niet. De bron-PR's zijn niet automatisch samengevoegd.

De instructies waarschuwen concreet voor bronverschillen. Crawler Log heeft live 1.4.5 op zijn beveiligingsbranch, terwijl de hoofdbranch nog 1.3.0 bevat. Form Bridge staat op zijn vermelde pluginbranch; Custom Functions heeft een live CSS-variant. Updraft to pCloud 0.2.5 in GitHub bevat extra lokale back-upopruiming: die functionele uitbreiding is niet een nog ontbrekende reparatie uit deze beveiligingsaudit en blijft buiten deze basisvrijgave.

## Aanvalstests en veiligheidsupdates

Met de complete actuele bron is op WordPress 7.1.4, PHP 8.3.33 en 8.5.10 opnieuw getest:

| Per runtime | Uitkomst |
| --- | --- |
| Negen volledige basispakketten | 75 controles; negen echte native WordPress-updates; alle uitgepakte bestanden en instellingen behouden |
| Extra updateraanvallen | 33 controles; nonce-/rechten-, invoer-/ZIP-, goedkeurings-, context-/downgrade- en lockaanvallen geweigerd |
| Bestaande updaterproef | 60 controles en de oorspronkelijke native fixture-update geslaagd |
| Algemene beveiligingsregressie | 208 controles geslaagd |
| Begrensde stressproef | 16 controles geslaagd |
| Normale document-/workerketen | 17 controles geslaagd met nagebootste ontvanger/B2 |
| Directe PHP-aanroepen | 60 bestanden zonder ongewenste uitvoer |

De vier aangetoonde problemen uit de audit zijn gerepareerd en live gecontroleerd in Form Bridge 0.5.6 en Server Location Test Probe 0.1.2. De huidige hashes komen opnieuw overeen met de geteste reparatiecode. Er zijn in de extra updaterproeven geen nieuwe gereproduceerde lekken gevonden. De [testbronnen en uitvoermethode](private-updates-onboarding-2026-11-07/README.md) en [bewijsgegevens](evidence/private-plugin-updates-complete-2026-11-07.json) leggen de controles vast.

Dit zijn afzonderlijke controles en asserties, geen telling van verschillende soorten lekken. De echte native updates en aanvalspayloads zijn alleen in de wegwerpomgeving uitgevoerd. De cookieproef roept de echte WordPress-controle aan, maar is geen volledige browsersessie. Een volledige toekomstige formulier-/CRM-/cloudketen, cloudherstel, WAF-omzeiling, massale gelijktijdige HTTP-belasting of multisite is niet door deze ronde bewezen.

## Productiecontrole

Na vrijgave zijn alle twaalf geïnstalleerde/vrijgegeven versies en pakketchecksums opnieuw gelezen en vergeleken met de archieven. De normale WordPress-updategegevens bevatten geen onnodig aanbod voor deze gelijke versies. De volledige bestandsboom en vastgelegde instellingenvingerafdrukken zijn gelijk gebleven. De tijdelijke alleen-lezen bronhulp is gedeactiveerd en haar oorspronkelijke code is hersteld. De private API blijft voor onbevoegden gesloten.

Voor een volgende wijziging geldt nu: gecontroleerde basis → nieuwe versie → volledig pakket → relevante functionele en beveiligingstests → checksum vastleggen → pakket vrijgeven → normale WordPress-update → controle en registratie bijwerken. Een commit alleen komt niet vanzelf op de website terecht.
