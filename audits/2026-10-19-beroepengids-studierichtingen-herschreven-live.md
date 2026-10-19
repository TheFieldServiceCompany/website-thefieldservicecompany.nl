# Beroepengids-studierichtingen herschreven — LIVE

Datum: 19 oktober 2026
Taxonomie: `beroep_studierichting`
Omvang: 23 bestaande termen

## Doel

De eerdere descriptions legden bij iedere term grotendeels dezelfde route af:
wat de technische richting inhoudt, hoe de kennis op locatie wordt gebruikt,
welke functies aansluiten en waarin iemand zich kan verdiepen. Daardoor
voelden de teksten als herhaalde algemene opleidings- en vakkennis.

De nieuwe content beantwoordt per term de vraag:

> Wat kan ik met deze studie binnen field service en welke functie, techniek,
> sector of werkomgeving past bij mij?

Een losse term zoals `Elektrotechniek` wordt niet behandeld alsof vanzelf
duidelijk is dat de pagina over een studie gaat. De teksten wisselen daarom
`opleiding`, `studierichting`, `studie` en `achtergrond` natuurlijk af.

## Uitgevoerde wijzigingen

- alle 23 bestaande term-descriptions bewust overschreven;
- alle 23 bestaande Pods-velden `intro_taxonomy` overschreven;
- geen termen, namen, slugs, relaties, Pods, velden, archieftitels of
  taxonomie-instellingen gewijzigd;
- geen vacature- of Beroepengidsartikelen gewijzigd;
- geen handmatige links of Elementor-widgets toegevoegd.

De 23 intro's bestaan uit twee uitnodigende zinnen van 28–36 woorden. Iedere
intro benoemt de studie en legt direct de inhoudelijke relatie met field
service.

Iedere description bevat vier zelfstandige vragen, maar de koppen zijn niet
als één sjabloon over alle termen herhaald. De volledige set bevat 92
letterlijk unieke H2-koppen. De teksten tellen 261–390 woorden en leggen de
nadruk op:

- mogelijke field service functies na de studie;
- relevante machines, systemen en bedrijfssectoren;
- verschillen tussen technische en organisatorische loopbaanrichtingen;
- producttraining en realistische aanvullende kennis;
- techniek, verantwoordelijkheid, werkomgeving en reizen als keuzecriteria;
- een natuurlijke adviesroute naar The Field Service Company.

## Veilige uitvoering

Voor de eerste wijziging zijn alle live descriptions en intro's opgeslagen in:

`audits/exports/beroep-studierichtingen-before-rewrite-2026-10-19.json`

Descriptions zijn via de standaard WordPress REST API gepubliceerd. Daarbij is
per term uitsluitend `description` verstuurd. De intro's zijn via een tijdelijk
beheerders-only REST-endpoint in het al bestaande Pods-veld `intro_taxonomy`
opgeslagen. De tijdelijke Code Snippets-snippet is na verificatie gedeactiveerd
en verwijderd.

De publicatieroutine bevat een rollback voor zowel descriptions als intro's.
Die rollback was niet nodig.

## Verificatie

Na publicatie zijn alle waarden opnieuw uit LIVE gelezen en exact met de lokale
bron vergeleken:

- descriptions gewijzigd: 23;
- descriptions exact geverifieerd: 23 van 23;
- intro's gewijzigd: 23;
- intro's exact geverifieerd: 23 van 23;
- afwijkingen: 0;
- tijdelijke snippet verwijderd: ja.

Openbare, cache-omzeilende controles zijn uitgevoerd voor:

- Autotechniek;
- Elektrotechniek;
- Technische Bedrijfskunde.

Alle drie URL's gaven HTTP 200. De juiste bestaande H1 `Studierichting
[term]`, de nieuwe intro en unieke nieuwe descriptionkoppen werden gerenderd.

## Bronnen en herhaling

- canonieke tekstbron:
  `content/education-descriptions/beroep-studierichtingen.md`;
- bijgewerkte schrijfregels:
  `content/education-descriptions/reusable-taxonomy-text-prompt.md`;
- lokale validatie:
  `scripts/build-beroep-studierichtingen.mjs`;
- preflight, backup, publicatie, verificatie en rollback:
  `scripts/publish-beroep-studierichtingen.py`.
