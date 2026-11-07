# Regressietests voor de reparaties

Deze map gebruikt dezelfde geïsoleerde uitvoering als de [oorspronkelijke auditproef](../security-tests-2026-11-07/README.md), met het [nieuwe bronmanifest](../evidence/custom-plugin-security-fixed-manifest-2026-11-07.json).

Anders dan de nulmeting verwachten deze tests dat de vier misbruiken worden geblokkeerd. Het normale uploadscenario controleert ook bewijzen, registratie, workeraflevering, stagingopruiming en het behoud van oude niet-ondertekende regels voor beoordeling. Ontvanger en B2-antwoorden zijn lokale fixtures. De herkomstherkenning voor de geënsceneerde PHP-upload is uitsluitend in de test vervangen.

```text
node run.mjs 8.5 /bron/eigen-plugins /bron/wordpress /bron/sqlite-database-integration /pakketten/node_modules ../evidence/custom-plugin-security-fixed-manifest-2026-11-07.json /bron/afhankelijkheden /uitvoer
```

Herhaal met `8.3` voor de tweede geteste runtime. De twee afzonderlijke repository-unittests staan bij de eigen pluginbronnen. De volledige WordPress-proef controleert de database-lock ook met een verouderde WordPress-optiecache. Dit is een gerichte regressieproef, geen gelijktijdige netwerklasttest.
