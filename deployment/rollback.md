# Rollback

## Herstelpunt

Vlak vóór de productieovergang wordt op **LIVE** een volledige UpdraftPlus-back-up
gemaakt van database, plugins, thema's, uploads en overige bestanden. Deze back-up
wordt naar Google Drive gestuurd en gemarkeerd zodat alleen handmatige verwijdering
mogelijk is.

## Wanneer terugdraaien

Rollback is nodig wanneer één van deze controles faalt en niet direct veilig kan
worden hersteld:

- **LIVE** of `/wp-admin/` is niet bereikbaar;
- pagina's, media of interne links wijzen nog naar **DEV**;
- vacatures of zoekfilters zijn onvolledig;
- formulieren versturen niet of bewaren geen Elementor-inzending;
- essentiële plugins of themafuncties veroorzaken fouten.

## Terugdraaien

1. Houd het migratievenster actief zodat tijdens herstel geen nieuwe inzendingen
   ontstaan.
2. Open UpdraftPlus op **LIVE** en selecteer de volledige back-up van vlak vóór de
   migratie.
3. Herstel database, plugins, thema's, uploads en overige bestanden.
4. Meld opnieuw aan met de oorspronkelijke **LIVE**-gebruikersgegevens.
5. Sla de permalinks opnieuw op en leeg de caches.
6. Controleer startpagina, vacatures, media, formulieren en Elementor-inzendingen.
7. Hef het migratievenster pas op nadat **LIVE** weer volledig werkt.

De aparte laatste export van **LIVE**-inzendingen en uploads blijft buiten Git
bewaard, ook nadat de website technisch is teruggedraaid.
