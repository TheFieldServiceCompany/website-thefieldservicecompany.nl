# Beveiligingsreparaties geplaatst — 7 november 2026

**Form Bridge 0.5.6 en Server Location Test Probe 0.1.2 zijn actief op productie.** De vier eerder gereproduceerde problemen worden door de herhaalde tests geblokkeerd. De oorspronkelijke audit blijft als historische nulmeting bewaard.

## Gerepareerd

- Form Bridge accepteert documenten alleen uit de servermatige uploadverzameling van de adapter. Een ingestuurde bestandsverwijzing zonder nieuwe upload wordt genegeerd. Nieuwe documenten krijgen een privébewijs dat inzending, veld, positie en exacte verwijzing bindt. Worker en B2-client controleren dit vóór verwerking. Een bewijs kan niet naar een andere inzending of een ander bestand worden gekopieerd.
- Serverdiagnoses en browsermeldingen krijgen eigen server-ID’s. Een aangeleverd correlatie-ID kan geen bestaande serverregel of outboxstatus selecteren. Het verband blijft als gegevensveld bewaard.
- Vroege diagnostiek heeft een beperkte invoerbewerking, maximaal dertig gebeurtenissen per afzender/formulier per vast tijdvak van tien minuten en een gezamenlijke bovengrens. Bij overschrijding wordt het loggen overgeslagen; de formulierverwerking blijft doorgaan. De limiet telt bestandsmetadata en weigert dus geen echt document alleen omdat de multipart-upload groter is dan 64 KiB.
- De serverprobe gebruikt de geverifieerde sleutelidentiteit voor alle headervarianten. Zij deelt een bytebudget van 2 MiB en een transferdeadline van twintig seconden over redirects en herhaalde pogingen. Een callback breekt te grote downloads af. Blokkerende DNS-resolutie kan buiten een harde systeemdeadline vallen.

Tellerupdates worden afgeschermd met een korte databaselock op basis van `INSERT IGNORE`. WordPress `add_option()` gebruikt een upsert en is bij een verouderde objectcache niet voldoende om één eigenaar te kiezen. De proef controleert dat een bestaande lock niet wordt overschreven. Ook de eerste aanmaak van de bewijs-sleutel gebruikt de database-uniciteit, zodat gelijktijdige uploads niet verschillende sleutels kunnen vastleggen.

## Gecontroleerd

| Controle | Uitkomst |
| --- | --- |
| WordPress 7.1.4, PHP 8.3.33 en 8.5.10 | Per runtime 208 beveiligingscontroles, 16 begrensde stresstests en 17 normale upload-/workercontroles voltooid |
| Vervalsing bestaand uploadbestand | Geen nieuw document; gewone formulierinzending wordt niet geblokkeerd |
| Normale nieuwe PDF | Adapter → bewijs → echte worker → nagebootste ontvanger/B2-bevestiging → bevestigde registratie en stagingopruiming geslaagd |
| Bewijs vervalsen/kopiëren of sleutel verwijderen | Verwerking geweigerd; een leescontrole maakt geen nieuwe sleutel |
| Oude niet-ondertekende wachtrijfixture | Voor beoordeling behouden, niet blind geüpload of gewist |
| Bestaande Form Bridge-unittests | 148 asserties geslaagd |
| Probe parser-/invoerproef | Negen asserties geslaagd; twee resolvergevallen expliciet overgeslagen onder PHP-WASM |
| Probe statische beveiligingscontrole | Zes PHP-bestanden en de bestaande patrooncontroles geslaagd |
| Directe PHP-aanroepen in volledige fixture | 59 bestanden zonder ongewenste uitvoer |
| Normale probe op echte productieserver | HEAD naar eigen homepage: HTTP 200, nul bodybytes |

Voor de lokale uploadfixture is alleen de PHP-uploadherkenning vervangen: er komt daar geen echte multipart-upload via een webserver binnen. Ontvanger en B2 zijn nagebootst; er zijn geen echte formulieren of CRM-/cloudtestgegevens op productie aangemaakt. PHP-WASM ondersteunt geen echte `dns_get_record()`-resolutie. De native HEAD-proef bevestigt de normale netwerkroute op de echte server, geen volledige DNS-aanvalstest. De oorspronkelijke uitgebreide auditbeperkingen blijven van toepassing.

## Productie en herstel

Vóór plaatsing waren er nul actieve wachtrij-items en nul actieve documentregels. Daardoor hoefden geen oude documenten automatisch te worden vertrouwd. De 43 afgeleverde en 21 voor beoordeling bewaarde outboxregels zijn behouden. De hashes van formuliermappings, diagnostiekinstellingen, probe-allowlist en probesleutel zijn gelijk gebleven. Het tijdelijke beheerhulpmiddel is gedeactiveerd en zijn oorspronkelijke code is teruggezet; de twee tijdelijke routes geven weer 404. De publieke homepage en sollicitatie-/adviespagina’s geven 200.

De geïnstalleerde code is per bestand vergeleken met de geteste bron en de twee ZIP-pakketten. De [bewijsgegevens](evidence/custom-plugin-security-fixes-2026-11-07.json) bevatten hashes en geschoonde resultaten. De [vaste bronmanifestatie](evidence/custom-plugin-security-fixed-manifest-2026-11-07.json) hoort bij de [regressietests](security-fixes-2026-11-07/README.md).

De interne uploadbewijssleutel en de bewijsmetadata horen bij de databasebackup. Verlies leidt tot weigeren van documentverwerking en beoordeling, niet tot een onveilige fallback. Zet de kwetsbare versies niet als normale herstelstap terug. Bewaar bij een storing de wachtrij en stagingbestanden en herstel gericht de reparatiecode. De pre-updatebronnen zijn apart bewaard; deze reparatie verwijdert geen bedrijfsgegevens.

## Bronnen en pakketten

- [Form Bridge-bronwijziging, PR 42](https://github.com/TheFieldServiceCompany/tfsc-crm-ats/pull/42), gebaseerd op de bestaande branche die de live plugin bevat.
- [Probe-bronwijziging, PR 1](https://github.com/TheFieldServiceCompany/server-location-test-probe-wordpress/pull/1).
- [Form Bridge 0.5.6](../wordpress/plugins/dist/tfsc-form-bridge-0.5.6.zip).
- [Serverprobe 0.1.2](../wordpress/plugins/dist/server-location-test-probe-0.1.2.zip).

De bron-PR’s staan als concept ter beoordeling; plaatsing op de website is afzonderlijk uitgevoerd en hierboven gecontroleerd. De nieuwe Update URI’s identificeren de eigen plugins. De afzonderlijk gevraagde [updatevoorziening is inmiddels gebouwd en geplaatst](2026-11-07-private-plugin-updates.md); een identificatieheader alleen bezorgt geen releases.
