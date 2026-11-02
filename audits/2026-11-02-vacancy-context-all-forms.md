# Livecontrole vacaturecontext voor alle formulieren — 2 november 2026

## Resultaat

TFSC Custom Functions 0.6.0 is live. Solliciteren, advies vragen en een vraag
stellen vanaf een afzonderlijke vacaturepagina gebruiken nu alle drie een
gevalideerde POST naar een schone formulier-URL. Alleen het opgeslagen numerieke
`vacaturenummer` wordt vanuit WordPress doorgegeven.

De Supabase-resolver blijft verantwoordelijk voor de omzetting naar de interne
`vacancy_id`. De bestaande exacte URL-herkenning is niet gewijzigd of verwijderd
en blijft als fallback beschikbaar.

## Uitgevoerde opschoning

- Elementor-template 26022: HTML-widget `62cd538` fysiek verwijderd.
- Elementor Custom Code 21893: herstelbaar naar de WordPress-prullenbak
  verplaatst. Het blok scrapete het verouderde label `Vacaturenummer:` en kon
  op de timeline een registratieknop naar het adviesformulier omleiden.
- TFSC Custom Functions: 0.5.0 vervangen door 0.6.0.
- De tijdelijke WordPress-onderhoudsroute voor de pluginupdate is na gebruik
  verwijderd en gaf daarna HTTP 404.
- Algemene advies- en registratieknoppen buiten een vacaturepagina behouden hun
  eigen bestemmingen; de vacaturehandler draait alleen op `singular vacature`.

## Livecontrole

- Vacaturesitemap: 64 regels, bestaande uit 63 individuele vacatures en het
  algemene archief `/vacatures/`.
- 63/63 individuele vacaturepagina's: HTTP 200.
- 63/63: numeriek opgeslagen vacaturenummer.
- 63/63: centrale handler aanwezig.
- 63/63: drie verborgen POST-formulieren aanwezig.
- 63/63: geen oude `postTitle`, `contractvorm`, `searchParams`-route of
  Custom Code 21893 in de HTML.
- Vacaturearchief: HTTP 200 en geen oud adviesscript.
- Geldige POST met nummer 358 naar solliciteren, advies en vraag: drie keer
  HTTP 200, schone eind-URL en `form_fields[vacancy_number]=358`.
- Ongeldige POST met `358<script>`: drie keer HTTP 400.
- Er is geen formulier verzonden en er is geen persoon, activiteit of
  sollicitatie aangemaakt.

De machineleesbare controle staat in
`audits/2026-11-02-vacancy-context-all-forms-live.json`.

## Supabase-fallback

Een live, alleen-lezen resolvercontrole met uitsluitend de exacte openbare URL
van vacature 358 gaf:

- `resolution_code`: `resolved_by_url`;
- `vacancy_publication_id`: `f7278de8-9270-4311-a9b7-b886b7bb2923`;
- `vacancy_id`: `86666135-bb36-4790-9c51-cc54b918b0fe`.

Daarmee is bewezen dat de URLfallback na deze websitewijziging actief bleef.

## Herstel

- Pluginrollback: `wordpress/plugins/dist/tfsc-custom-functions-0.5.0.zip`.
- Nieuw livepakket: `wordpress/plugins/dist/tfsc-custom-functions-0.6.0.zip`,
  SHA-256 `2b51668cebefd742d149af26b4c570b71f7d0a0df958437075d3619a81a2f5ff`.
- Elementor-template vóór wijziging:
  `wordpress/exports/elementor/2026-11-02-before-all-vacancy-context-post/26022-before.json`.
- Custom Code vóór verwijdering:
  `wordpress/exports/elementor/2026-11-02-before-all-vacancy-context-post/21893-before.json`.
- Custom Code 21893 staat daarnaast herstelbaar in de WordPress-prullenbak.

Bij een noodrollback eerst plugin 0.5.0 herstellen, daarna template 26022 uit de
back-up terugplaatsen en alleen indien werkelijk nodig Custom Code 21893
herstellen. Wis of purge daarna de betrokken pagina-cache en herhaal de drie
formuliercontroles.
