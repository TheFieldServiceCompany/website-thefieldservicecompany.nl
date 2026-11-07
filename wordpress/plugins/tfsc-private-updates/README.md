# TFSC Private Updates 0.1.0

Eigen, gecontroleerde pluginreleases verschijnen onder **Plugins** en **Dashboard → Updates**. **Gereedschap → TFSC Updates** toont de geïnstalleerde en laatst vrijgegeven versie. Installeren gebeurt met de gewone WordPress-knop. Automatische installatie wordt niet ingeschakeld; de eigenaar kan later de normale WordPress-instelling per plugin kiezen.

## Vrijgeven is een aparte stap

Een GitHub-commit wordt niet vanzelf productiecode. Bouw en test eerst een versiegebonden ZIP, beoordeel de wijziging en publiceer pas daarna het goedgekeurde pakket via de bestaande beveiligde WordPress-toegang. De beheerder krijgt vervolgens de gewone updatemelding. Er zijn geen GitHub-tokens, openbare downloadlinks, extra accounts of een afzonderlijke MCP-verbinding nodig.

De voorziening herkent de elf bestaande TFSC-plugins en zichzelf. Een plugin krijgt pas een updateaanbod wanneer er een hoger, goedgekeurd pakket is gepubliceerd. Voor nog niet gepubliceerde plugins toont het overzicht dat er nog geen release is aangesloten. Identieke huidige versies veroorzaken geen onnodige update.

## Beveiligingsmodel

- Publiceren en intrekken vereisen zowel `manage_options`, `install_plugins` als `update_plugins`; normale WordPress REST-authenticatie geldt, inclusief noncecontrole bij cookiegebruik.
- Alleen de vaste lijst eigen pluginbestanden mag worden aangeboden. ZIP-root, hoofd-PHP-bestand en versie moeten bij de doelplugin passen. Archieven met padtraversal, dubbele paden, symlinks, encryptie of te veel/te grote bestanden worden geweigerd. Grenzen: 2 MiB ZIP, 10 MiB uitgepakt, 300 entries, 128 KiB hoofd-PHP-bestand.
- Een bestaande versie kan niet ongemerkt andere code krijgen. Maak voor gewijzigde inhoud een nieuw versienummer.
- Goedgekeurde metadata is met een sitegebonden HMAC ondertekend; de sleutel wordt afgeleid van de WordPress-authenticatiesalts. Het pakket krijgt een SHA-256-checksum. Beide worden vóór installatie gecontroleerd, evenals WordPress-/PHP-vereisten en de doelplugin. Downgrades worden geweigerd.
- Archieven staan in afzonderlijke niet-autoloadende databaseopties, niet op openbare downloadadressen. WordPress krijgt een interne pakketidentiteit; de downloadhook levert het geverifieerde archief lokaal aan de native upgrader. Tijdelijke ZIP’s moeten buiten de publieke website-/uploadmappen staan en krijgen rechten 0600. De bestanden worden door WordPress na de update opgeruimd.
- Er worden maximaal drie releases per plugin bewaard. De catalogus heeft een korte database-lock met één eigenaar. Intrekken verwijdert het aanbod, ook voor een oud gecachet pakket-ID; het draait geïnstalleerde code niet automatisch terug.
- Alleen eigen pluginvermeldingen worden door deze provider beheerd. Overige commerciële/WordPress.org-updates blijven hun bestaande provider gebruiken. Een gelijknamig WordPress.org-aanbod kan onze vaste private targets niet overnemen.
- De updater installeert alleen na een normale bevoegde updateactie, of bij een echte cron-update nadat de eigenaar die specifieke plugin expliciet voor auto-updates heeft aangevinkt.

De voorziening bewaart goedgekeurde uitvoerbare code. Wie de publicatiebevoegdheid krijgt, krijgt daarom dezelfde vertrouwenspositie als iemand die plugins mag installeren. Dit is geen automatische codebeoordelaar, antivirus of garantie voor compatibiliteit met toekomstige versies.

## Publicatie-API voor beheer

`GET /wp-json/tfsc-updates/v1/releases` geeft het versieoverzicht aan bevoegde beheerders; bevat geen ZIP-inhoud of signing proof.

`POST /wp-json/tfsc-updates/v1/releases` accepteert één JSON-object:

```json
{
  "plugin": "tfsc-form-bridge/tfsc-form-bridge.php",
  "archive": "BASE64_VAN_HET_GECONTROLEERDE_ZIPBESTAND",
  "sha256": "SHA256_VAN_EXACT_DAT_ZIPBESTAND",
  "tested": "7.1.4",
  "notes": "Beknopte gebruikersgerichte wijzigingsnotities."
}
```

De versie en minimale vereisten worden uit het ingesloten hoofd-PHP-bestand gelezen. De ZIP moet de bestaande pluginmap bevatten; hernoem die niet. De aanroep keurt het pakket goed maar installeert het niet direct.

`DELETE /wp-json/tfsc-updates/v1/releases/RELEASE_ID` trekt een release in. Hiervoor gelden dezelfde beheerdersrechten. Hetzelfde ongewijzigde pakket kan later opnieuw worden goedgekeurd. De webinterface is bedoeld voor het bekijken en installeren; publiceren en intrekken gebeuren via de beheer-API.

## Vereisten en herstel

WordPress 6.5+, PHP 8.1+, de PHP ZIP-extensie, schrijfrecht in een tijdelijke map buiten de publieke website en de gebruikelijke WordPress-rechten voor pluginupdates. Deze versie is voor de huidige losse TFSC-installatie gebouwd; netwerkinstallaties/multisite zijn niet als beheerworkflow getest.

Neem catalogus en pakketopties mee in de databasebackup en bewaar de WordPress-salts bij een volledige herstelactie. Saltrotatie maakt oude vrijgaven ongeldig; publiceer de gecontroleerde pakketten opnieuw. Bij een probleem kan deze updater worden gedeactiveerd; hij verwijdert dan geen andere plugins of hun gegevens. Laat WordPress zijn normale updateback-up/herstel uitvoeren bij een mislukte installatie. Een expliciete code-/databasebackup blijft nodig voor functionele problemen die pas later zichtbaar worden.

De actuele aanvalstests en native installatietest staan bij het website-auditrapport. De gewone WordPress-bulkupdater die het Plugins-scherm gebruikt is daadwerkelijk getest, inclusief het behoud van actieve status en instellingen.
