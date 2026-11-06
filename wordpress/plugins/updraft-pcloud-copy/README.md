# TFSC Updraft to PCloud 0.2.3

De plug-in kopieert geslaagde lokale UpdraftPlus-back-ups naar pCloud en kan ze terughalen. UpdraftPlus bepaalt de inhoud van de archieven, maakt de back-ups en voert het websiteherstel uit.

## Verbinden

1. Upload de ZIP via **Plugins → Nieuwe plugin → Plugin uploaden**. Open daarna **Gereedschap → TFSC Updraft to PCloud → Back-ups**.
2. Open je goedgekeurde app in [pCloud My Apps](https://docs.pcloud.com/my_apps/). Vul de **Client ID** in en klik op **Save Client ID**.
3. Klik op **Authorize in pCloud and get an Access Code**. Geef de app toestemming en kopieer de **Access Code**.
4. Kies **Storage region** op basis van **Host Name** in pCloud. Vul de **Client Secret** uit My Apps en de **Access Code** in. Klik op **Connect to pCloud**. Bovenaan zie je of de koppeling gelukt is. De verbinding wordt versleuteld opgeslagen met de sleutels van de WordPress-installatie.

Vereist zijn een losse WordPress-installatie vanaf 6.5, PHP vanaf 8.1 met cURL en OpenSSL, en UpdraftPlus. Gebruik HTTPS voor WordPress-beheer.

## Doelmap en eerste proefupload

- De doelmap heet **WordPress-backups-** gevolgd door de websitehost en een korte unieke code. Het tabblad **Back-ups** toont de exacte naam. Bij de eerste upload maakt de plug-in de map aan in de opslag waartoe de gekoppelde app toegang heeft. Gebruik **Ophalen → Herstelpunten controleren** om de doelmap en beschikbare herstelpunten opnieuw op te vragen.
- Maak in UpdraftPlus een back-up met bestanden en database. Kies die onder **Uploaden** en klik op **Upload klaarzetten**. Bewaar alle lokale archiefdelen tot de upload klaar is. De status **Upload voltooid** toont de map van het herstelpunt. Pas na controle van alle delen komt er een geverifieerde voltooiingslijst in de pCloud-map.
- Als geplande WordPress-taken op dev uitstaan, verwerk je een wachtende upload of download met **Volgende uploadstap uitvoeren** of **Volgende downloadstap uitvoeren**. Elke beurt verwerkt één deel. Als een groot deel herhaaldelijk afbreekt, kan het helpen om in de expertinstellingen van UpdraftPlus kleinere archieven te laten maken.

## Automatische upload en bewaarlimiet

Stel in UpdraftPlus de eigen back-upplanning en lokale bewaartermijn in. Kies vervolgens op het tabblad **Back-ups** welke lokaal beschikbare sets automatisch naar pCloud moeten. De keuze gaat over complete, geslaagde sets van bestanden en database samen, alleen database of alleen bestanden. Het scherm kan de laatste succesvolle back-up uit UpdraftPlus en tot 30 geslaagde sets die deze versie daarna heeft gezien aanbieden.

Vink bij **Uploaddagen** de gewenste weekdagen aan. Vink alle zeven aan voor een dagelijkse upload. Kies één tijdstip of voeg een tweede, later tijdstip toe. Dezelfde tijden en back-upsoort gelden op alle gekozen dagen. Plan de tijden na de UpdraftPlus-back-ups, in de WordPress-tijdzone. Per tijdstip kijkt de plug-in naar de nieuwste passende set en slaat die over als dit de laatst bevestigde upload is. Het startmoment hangt af van de geplande taken van de hosting. Een mislukte overdracht krijgt maximaal vijf automatische pogingen per onderdeel, met vijf minuten wachttijd.

De pCloud-bewaarlimiet staat standaard op **0**, wat alles bewaart. Een ingestelde limiet bewaart de laatst geüploade herstelpunten die deze installatie sinds 0.2.0 heeft geregistreerd. Na een geslaagde upload kan de plug-in oudere geregistreerde mappen opruimen zodra hij de verwachte inhoud heeft gecontroleerd. Bij een wijziging aan de verwachte mapinhoud stopt het opruimen. De lokale UpdraftPlus-back-ups worden door UpdraftPlus beheerd.

## Terughalen

Open **Ophalen** en klik op **Herstelpunten controleren**, kies het herstelpunt en klik op **Terughalen naar UpdraftPlus**. De plug-in controleert de gedownloade archiefdelen en maakt ze pas na verificatie beschikbaar voor UpdraftPlus. Een afwijkend lokaal bestand met dezelfde naam blijft behouden en de plug-in meldt het conflict. Vink **Download alle archiefdelen opnieuw voor controle** aan om ook een identiek lokaal archief opnieuw uit pCloud te downloaden en te verifiëren. De bestaande lokale kopie blijft daarbij behouden. Het resultaat maakt onderscheid tussen gedownloade en hergebruikte delen. Start het websiteherstel zelf bij **Bestaande back-ups** in UpdraftPlus.

De volledige upload, download en herstelproef met versie 0.2.1 zijn op dev vastgelegd in [VOORTGANG.md](VOORTGANG.md).
