# Eigen pluginupdates geplaatst — 7 november 2026

**TFSC Private Updates 0.1.0 is actief op productie.** Goedgekeurde nieuwe versies van onze eigen plugins verschijnen bij **Plugins** en **Dashboard → Updates**. Het overzicht staat onder **Gereedschap → TFSC Updates**. Installeren gebeurt met de gewone WordPress-updateknop.

Automatische installatie is niet ingeschakeld. De bestaande selectie voor automatische updates is ongewijzigd en op dit moment leeg. De eigenaar kan die later per plugin in het normale WordPress-scherm instellen.

## Wat is aangesloten?

De provider herkent de elf bestaande eigen pluginbestanden en zichzelf. Een nieuwe goedgekeurde release kan voor ieder van die twaalf doelen worden gepubliceerd zonder de provider aan te passen. Het aanbod wordt bij het uitlezen van de normale WordPress-updategegevens toegevoegd.

Als beginpunt zijn drie complete, gecontroleerde ZIP-pakketten vrijgegeven:

| Plugin | Geïnstalleerd en vrijgegeven | Resultaat |
| --- | --- | --- |
| TFSC Form Bridge | 0.5.6 | Huidige beveiligde versie; geen overbodige update |
| Server Location Test Probe | 0.1.2 | Huidige beveiligde versie; geen overbodige update |
| TFSC Private Updates | 0.1.0 | Kan toekomstige eigen releases via hetzelfde kanaal ontvangen |

Voor de overige negen plugins is nog geen pakket gepubliceerd. Hun huidige werking is behouden; zij krijgen pas een melding wanneer een complete nieuwe release is getest en vrijgegeven. De eerdere auditselectie van bronbestanden wordt niet als installabel ZIP gebruikt, omdat zo'n selectie statische of binaire bestanden kan missen.

Een GitHub-commit installeert niet automatisch code. De workflow is: wijziging beoordelen → versie verhogen → volledig pakket bouwen → toepasselijke tests uitvoeren → checksum vastleggen → pakket vrijgeven via bestaande beveiligde WordPress-toegang → normale updatemelding → beheerder installeert. Compatibiliteit met een toekomstige WordPress-versie moet per relevante release worden gecontroleerd; een updatevoorziening herschrijft de plugin niet vanzelf.

## Controle vóór installatie

- Publiceren en intrekken zijn uitsluitend voor gebruikers met beheer-, installatie- én updaterechten. WordPress verzorgt authenticatie en de noncecontrole voor cookieverzoeken.
- Alleen de vaste eigen pluginbestanden worden geaccepteerd. De provider controleert ZIP-root, verwacht hoofd-PHP-bestand, versie, minimale WordPress-/PHP-versies en SHA-256. Hij weigert onveilige paden, symlinks, dubbele paden, encryptie en te grote pakketten.
- Goedkeuring wordt met een sitegebonden HMAC vastgelegd. Gewijzigde metadata, corrupte inhoud, een ander doel, een andere site-identiteit of een ingetrokken goedkeuring kunnen niet worden geïnstalleerd. Een oudere versie dan de geïnstalleerde wordt geweigerd.
- Pakketten staan in afzonderlijke niet-autoloadende databaseopties. Er is geen openbaar ZIP-downloadadres en er wordt geen GitHub-token op de website gezet. Alleen de native upgrader krijgt het gecontroleerde lokale archief, in een tijdelijke map buiten de publieke website met rechten 0600.
- De opslag bewaart maximaal drie releases per plugin. Een korte databaselock voorkomt twee gelijktijdige catalogusschrijvers. Een bestaande catalogusversie kan geen andere inhoud krijgen zonder nieuw versienummer.
- De provider beheert alleen zijn vaste private doelen. Andere updateproviders blijven behouden. Een gelijknamige WordPress.org-plugin mag deze eigen plugins niet overnemen.

De publicatiebevoegdheid blijft een bevoegdheid om uitvoerbare code te installeren. Deze controles vervangen de bronbeoordeling en functionele tests niet.

## Getest en live gecontroleerd

De [uitvoerbare tests](private-updates-tests-2026-11-07/README.md) draaien met een nieuwe wegwerpinstallatie van WordPress 7.1.4 op PHP 8.3.33 en 8.5.10. De test gebruikt de echte WordPress REST-router, gebruikersrollen, updategegevens, plugininformatie en `Plugin_Upgrader::bulk_upgrade()`, zoals het Plugins-scherm die gebruikt.

Beide runtimes slagen voor de aanvalstests en de normale update. De proef installeert een onschuldige fixture van 1.0.0 naar 1.0.1, vergelijkt de geïnstalleerde versie en controleert behoud van actieve pluginidentiteit, database-instellingen en auto-updateselectie. Ook de expliciete native auto-updatekeuze wordt gecontroleerd zonder die op productie aan te zetten. De volledige aantallen en controles staan in de [bewijsgegevens](evidence/private-plugin-updates-2026-11-07.json).

Op productie zijn de twee geïnstalleerde updaterbestanden vergeleken met de geteste bron. De bestaande 31 actieve plugins zijn behouden; alleen de updater is toegevoegd. Formuliermappings, diagnostiekinstellingen, probe-allowlist en probesleutel zijn gelijk gebleven. De drie vrijgegeven pakketchecksums komen overeen met de gecontroleerde ZIP's.

De privé-API weigert anonieme lees- en publicatieverzoeken. Een downloadroute bestaat niet. Het tijdelijke installatiehulpmiddel is weer inactief en zijn oorspronkelijke code is teruggezet; de tijdelijke routes geven 404. De homepage en sollicitatie-/adviespagina's geven HTTP 200. Er zijn geen testformulieren, kandidaten, CRM-gegevens of cloudtestbestanden op productie aangemaakt.

Er is geen kunstmatige productierelease geïnstalleerd om een nieuwe versie te demonstreren. De echte update is in de wegwerpomgeving uitgevoerd; productie heeft dezelfde geteste updater en de drie huidige basispakketten. Een volledige toekomstige formulierinzending, CRM-keten of WordPress-multisiteworkflow is hiermee niet bewezen.

## Beheer en herstel

- [Pluginbron en technische documentatie](../wordpress/plugins/tfsc-private-updates/README.md).
- [Gecontroleerd installatiepakket 0.1.0](../wordpress/plugins/dist/tfsc-private-updates-0.1.0.zip).
- [Publicatiehulpmiddel voor volgende releases](../wordpress/plugins/tfsc-private-updates/tools/publish-release.py): standaard alleen een lokaal voorbeeld; `--publish` geeft exact het pakket met de vooraf vastgelegde checksum vrij.
- [Eerder geplaatste Form Bridge- en probereparaties](2026-11-07-custom-plugin-security-fixes.md).

Neem catalogus en pakketopties mee in de databasebackup. Na het wijzigen van de WordPress-authenticatiesalts moeten gecontroleerde pakketten opnieuw worden vrijgegeven. Intrekken verwijdert het aanbod en blokkeert een oud pakket-ID; het herstelt geen al geïnstalleerde code. De native WordPress-upgrader verzorgt installatie en zijn tijdelijke back-up. Bewaar daarnaast een code- en databasebackup voor functionele problemen die later aan het licht komen. De updater deactiveren verwijdert geen andere plugins of hun instellingen.

De bron en tests zijn in GitHub vastgelegd; de bestaande bron-PR's voor Form Bridge en de probe zijn afzonderlijke concept-PR's. Plaatsing op de website en het samenvoegen van bronbranches zijn afzonderlijke handelingen.

## WordPress-bronnen

- [Plugin-updatecontrole](https://developer.wordpress.org/reference/functions/wp_update_plugins/).
- [Lokale levering aan de upgrader via `upgrader_pre_download`](https://developer.wordpress.org/reference/hooks/upgrader_pre_download/).
- [Native plugin-bulkupdate](https://developer.wordpress.org/reference/classes/plugin_upgrader/bulk_upgrade/).
