# Updraft naar pCloud 0.1.0

De plug-in kopieert de laatst geslaagde lokale UpdraftPlus-back-up naar pCloud. Je kiest een dag en tijd of laat WordPress het elke dag proberen. UpdraftPlus blijft de archieven maken en terugzetten.

## Installeren

1. Open **WordPress → Plugins → Nieuwe plugin → Plugin uploaden**. Kies de ZIP en activeer de plug-in.
2. Open **Gereedschap → Updraft naar pCloud**.
3. Ga naar [pCloud My Apps](https://docs.pcloud.com/my_apps/), log in en maak een app voor deze koppeling. Bewaar de appgegevens privé.
4. Sla de appsleutel (Client ID) op in WordPress. Open daarna de link **Geef toegang in pCloud en haal de eenmalige code op**. Geef toestemming en kopieer de code die pCloud toont.
5. Kies de opslagregio van je account en vul het appgeheim en de eenmalige code in. Klik op **pCloud koppelen**. WordPress bewaart de verkregen toegangssleutel versleuteld met de sleutels van je installatie. Na wijziging van die WordPress-sleutels moet je pCloud opnieuw koppelen.

De eerste versie is bedoeld voor een losse WordPress-installatie met WordPress 6.5 of hoger, PHP 8.1 of hoger en de PHP-extensies cURL en OpenSSL. Gebruik een HTTPS-verbinding voor je WordPress-beheer.

## Eerste proef

1. Maak met UpdraftPlus één back-up waarin je **de bestanden én database** aanvinkt. De archieven moeten lokaal bij UpdraftPlus beschikbaar blijven tot de upload klaar is.
2. Klik op **Nieuwste back-up klaarzetten voor upload** en open de statuspagina opnieuw om de voortgang te zien. WordPress verwerkt de onderdelen op de achtergrond.
3. Zoek in pCloud de map die bij **Laatste volledige upload** staat. Daarin staan de originele UpdraftPlus-archieven en `VOLTOOID.json`. Dat laatste bestand bevat de gecontroleerde SHA-1-sommen.
4. Download alle originele archieven uit die map en upload ze bij **UpdraftPlus → Bestaande back-ups** op een aparte WordPress-testinstallatie. Gebruik daar **Herstellen**. Het bestand `VOLTOOID.json` hoort niet bij de UpdraftPlus-archieven.

Als één archiefdeel herhaaldelijk te groot blijkt voor de beschikbare uploadtijd, verklein bij **UpdraftPlus → Instellingen → Expertinstellingen** de instelling **Archieven splitsen** en maak een nieuwe back-up. Een groot database-archief of een groot individueel mediabestand kan alsnog een aparte beperking zijn.

## Automatisch uploaden

- Laat UpdraftPlus de volledige back-up maken en plan het uploadmoment daarna. Kies een dag en tijd, vink **Automatisch uploaden** aan en sla op. Een voorbeeld is UpdraftPlus op zondag om 02.00 uur en uploaden vanaf 04.00 uur.
- Het gekozen tijdstip gebruikt de WordPress-tijdzone. WordPress moet geplande taken uitvoeren. Zonder een aparte crontrigger kan de start opschuiven tot het volgende websitebezoek. Op de gekozen dag blijft WordPress na het gekozen tijdstip zoeken naar een nieuw herstelpunt tot er een is klaargezet.
- Per beurt verwerkt WordPress één archiefdeel. Bij onderbreking probeert het hetzelfde onderdeel na vijf minuten opnieuw, tot vijf pogingen. Een archiefdeel dat al met dezelfde controlesom in pCloud staat, wordt overgeslagen.
- De uploader kopieert het laatste succesvolle UpdraftPlus-resultaat zoals het is. Een losse databaseback-up levert dus alleen een databasekopie op. Gebruik voor volledige herstelpunten een UpdraftPlus-schema waarbij bestanden en database samen in dezelfde back-up worden gemaakt.
- De pCloud-mappen blijven bewaard totdat je ze zelf opruimt. Dit zijn aparte UpdraftPlus-archieven per herstelpunt. Een map zonder `VOLTOOID.json` is nog niet als compleet bevestigd.

De planning, retry en controle van archiefdelen zijn lokaal met nagebootste WordPress- en pCloud-antwoorden getest. De pCloud-koppeling, grootste upload en herstel moeten nog op een echte installatie worden geprobeerd.
