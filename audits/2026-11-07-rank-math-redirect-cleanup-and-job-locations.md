# Rank Math Free — automatische redirectopruiming en vacaturelocaties

Vervolg op verzoek van de eigenaar, 7 november 2026.

**TFSC Rank Math Breadcrumbs 0.3.0 is geïnstalleerd en actief.** De uitbreiding neemt nu ook de automatische redirectopruiming van Pro over. Rank Math Free blijft actief, Pro blijft inactief en SEO Analyzer blijft aan. TFSC Structured Data 0.5.3 is onderzocht en ongewijzigd gebleven.

## Opruiming zoals in Pro

De geïnstalleerde Pro-bron koppelt automatisch aangemaakte doorverwijzingen aan een bericht of term via `rank_math_auto_redirect`. Bij definitief verwijderen van dat object verwijdert Pro de daarin genoemde redirect-ID’s met de Free-database-API. De aansluiting gebruikt de hooks `rank_math/redirection/post_updated`, `rank_math/redirection/term_updated`, `before_delete_post` en `pre_delete_term`, op prioriteit 20.

Versie 0.3.0 bewaart diezelfde koppeling voor nieuwe automatische URL-wijzigingen. Bij definitief verwijderen ruimt zij alleen de expliciet gekoppelde regels op, via de Free-API die ook de bijbehorende redirectcache wist. Dit sluit aan op de bestaande Pro-metadata: er zijn op de site 37 berichtkoppelingen en 15 termkoppelingen aangetroffen, verspreid over alle statussen. De eerdere telling van 32 betrof alleen gepubliceerde berichten.

Verplaatsen naar de prullenbak en terugzetten behouden de regels. Een handmatig gemaakte regel zonder zo’n koppeling wordt niet verwijderd, ook niet als deze dezelfde bestemming heeft. Er wordt niet achteraf op bestemmings-URL gezocht om extra regels te wissen. Als een bestaande, door Pro gekoppelde regel later handmatig is aangepast, blijft de oorspronkelijke koppeling gelden; ook dat is het gedrag van Pro.

De opruiming werkt alleen met de redirectmodule aan. Bij actieve Pro laat de uitbreiding zowel het bijhouden van koppelingen als de opruiming aan Pro over. Ongeldige metadata wordt genegeerd. De installatie voert geen opruimronde uit en verandert geen bestaande redirect of koppeling.

## TFSC Structured Data en locaties

De onderzochte plugin is **[TFSC Structured Data](https://github.com/TheFieldServiceCompany/tfsc-structured-data)**, versie 0.5.3. De vijf relevante geïnstalleerde PHP-bestanden komen overeen met de onderzochte bronbestanden. De GitHub-bronnen zijn gecontroleerd via de eigen repository, inclusief de bestandsversies.

De actieve uitvoer werkt als volgt:

1. `TFSC_WordPress_Data::locations()` leest de gekoppelde `locatie`-taxonomietermen van de vacature en hun adresvelden: straat, plaats, regio, postcode en land.
2. `TFSC_Schema_Builder::build_locations()` zet die gegevens om naar fysieke `jobLocation`-vermeldingen en, waar toepasselijk, voorwaarden voor thuiswerken.
3. `TFSC_Structured_Data::render()` schrijft zelfstandig JSON-LD in `wp_head`. Hiervoor is geen Pro-klasse, Pro-functie of Rank Math-schema-editor nodig.

Bronnen: [WordPress-/Pods-gegevens](https://github.com/TheFieldServiceCompany/tfsc-structured-data/blob/main/includes/class-tfsc-wordpress-data.php), [schemabouwer](https://github.com/TheFieldServiceCompany/tfsc-structured-data/blob/main/includes/class-tfsc-schema-builder.php), [zelfstandige uitvoer](https://github.com/TheFieldServiceCompany/tfsc-structured-data/blob/main/includes/class-tfsc-structured-data.php) en [veldtoewijzing](https://github.com/TheFieldServiceCompany/tfsc-structured-data/blob/main/docs/field-map.md).

**Meerdere werklocaties per vacature zijn dus beschikbaar zonder Pro.** Dit is een andere functie dan Rank Math Pro’s beheer voor meerdere bedrijfsvestigingen. De site heeft 60 locatie-termen. De gecontroleerde openbare vacature-uitvoer bevat voorbeelden met 44 fysieke werklocaties in één JobPosting. Dat aantal is een waarneming, geen ingestelde grens of aanbeveling voor de inhoud.

De organisatiegegevens voor vacatures worden uit de instellingen van TFSC Structured Data gehaald. De optionele aparte EmploymentAgency-uitvoer van deze plugin op de homepage staat uit. Het bestaande algemene bedrijfsschema van Rank Math Free is daarvan een afzonderlijke aansluiting. Er zijn geen locatie- of organisatie-instellingen veranderd.

## Tests en productiecontrole

- 27 gerichte compatibiliteitscontroles geslaagd.
- 56 controles van breadcrumbs, CSS, schema, querymatching en lokale sitemap in de tijdelijke volledige WordPress-installatie geslaagd.
- Acht baselinecontroles van de gewone Free-werkwijze geslaagd.
- Achttien aanvullende controles met echte WordPress-bewerkingen en verwijderingen geslaagd: post- en termredirects, dubbele meldingen, herhaalde URL-wijzigingen, prullenbak/herstel, definitief verwijderen, bestaande Pro-metadata, handmatige regels, cacheverwijdering, ongeldige metadata, module uit en Pro-voorrang. [Testbron](../wordpress/plugins/tfsc-rank-math-breadcrumbs/tests/cleanup-workflows.php).
- De bestaande zelfstandige TFSC Structured Data-tests zijn ongewijzigd uitgevoerd: 92 assertions geslaagd. De tests gebruiken lokale fixtures en vereisen geen Rank Math Pro.
- Op productie staan alle vier nieuwe callbacks op prioriteit 20 geregistreerd. De uitvoering is ingeschakeld met Free actief en Pro uit.
- Vóór en na installatie zijn alle 265 bestaande redirectregels, de 37 berichtkoppelingen en 15 termkoppelingen exact gelijk gebleven. Ook de hashes van de algemene Rank Math-, title- en sitemapinstellingen en de onderzochte Structured Data-bronbestanden zijn gelijk gebleven. SEO Analyzer staat nog aan.
- Opnieuw 48 openbare pagina’s gecontroleerd: status, titels, metadata, volledige schema-uitvoer, breadcrumbs en widgetmarkup zijn gelijk aan de oorspronkelijke vergelijking.
- Alle 30 openbare sitemap-/locatiebestanden zijn bereikbaar met HTTP 200. De oudere ochtendmeting is niet meer volledig bytegelijk: er zijn inmiddels vier Beroepengids- en dertien Kennisbankartikelen gepubliceerd en de volgorde van bestaande vermeldingen kan wisselen. Daarom is aanvullend de huidige Free-generator voor alle 30 typen/pagina’s in afzonderlijke geauthenticeerde verzoeken vergeleken met en zonder uitsluitend de vier nieuwe opruimcallbacks. De geparseerde XML-waarden zijn in alle 30 gevallen gelijk, na uitsluiting van tijdstempels en elementvolgorde. Deze leescontrole roept geen verwijderactie aan en vervangt geen test van Google-indexering.

De redirectbewerkings- en verwijdertests zijn uitsluitend in de tijdelijke lokale WordPress-database uitgevoerd. Er is geen productiecontent voor de test gemaakt, gewijzigd of verwijderd. De tijdelijke installatie-/leesroute is na de controle gedeactiveerd.

De actuele samenvatting van de bronhashes, locatievoorbeelden en verdere openbare vergelijking staat in [de bewijsgegevens](evidence/rank-math-cleanup-locations-2026-11-07.json).

## Installatie en herstel

Het geïnstalleerde pakket is [0.3.0](../wordpress/plugins/dist/tfsc-rank-math-breadcrumbs-0.3.0.zip). Laat Rank Math Free en deze uitbreiding actief. Er zijn geen extra handmatige beheerregels of nieuwe instellingen nodig voor de opruiming.

Het pakket 0.2.0 en de vorige bron zijn bewaard. Terugzetten naar 0.2.0 stopt de toekomstige automatische opruiming. Opnieuw activeren van Pro geeft de uitvoering weer aan Pro. Reeds als gevolg van een definitieve contentverwijdering verwijderde redirectregels worden daarmee niet automatisch teruggezet.
