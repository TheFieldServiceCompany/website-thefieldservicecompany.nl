# Vacature-opleidingsniveaus: descriptions LIVE

Datum: 18 oktober 2026  
Status: uitgevoerd en gecontroleerd

## Doel

De vier vacaturearchieven voor opleidingsniveau voorzien van korte,
informatieve descriptions die het niveau beschrijven zonder het te verwarren
met een opleiding of studierichting.

Doeltaxonomie: `opleidingsniveau`  
Niet gewijzigd: `beroep_opleidingsniveau`

## Gepubliceerde termen

| Term | Slug | Gekoppelde vacatures tijdens audit | Woorden |
| --- | --- | ---: | ---: |
| VMBO / LBO | `lbo` | 36 | 309 |
| MBO | `mbo` | 63 | 305 |
| HBO | `hbo` | 41 | 293 |
| WO / Universiteit | `universiteit` | 12 | 305 |

## Redactionele keuzes

- De teksten behandelen opleidingsniveau, niet een specifieke opleiding.
- Studierichting, ervaring, certificaten en productkennis worden als aparte
  factoren benoemd.
- VMBO / LBO legt de nadruk op praktisch en hands-on technisch werk. VMBO
  wordt niet als beroepsopleiding gepresenteerd; LBO is als historische term
  uitgelegd.
- MBO beschrijft de combinatie van praktijk en vaktheorie zonder ieder
  MBO-profiel hetzelfde te maken.
- HBO beschrijft analyse en systeemdenken zonder automatisch leidinggeven,
  senioriteit of meer verantwoordelijkheid te claimen.
- WO / Universiteit wordt alleen gekoppeld aan functies waarin academische
  technische diepgang werkelijk relevant kan zijn.
- Iedere tekst heeft vier unieke vraaggerichte H2-koppen en gebruikt
  `field service` als twee woorden.

De compacte herbruikbare prompt in
`content/education-descriptions/reusable-taxonomy-text-prompt.md` was het
redactionele uitgangspunt.

## Veilige uitvoering en controle

De oude descriptions zijn voor publicatie opgeslagen in:

`work/vacancy-education-level-descriptions-live-2026-10-18/before.json`

De publicatiescript had automatische rollback voor iedere reeds gewijzigde
term. Na publicatie zijn alle vier descriptions opnieuw via WordPress REST
gelezen en exact met de bron vergeleken.

Daarna zijn de openbare archieven voor HBO, MBO, VMBO / LBO en WO /
Universiteit met cache-omzeiling gecontroleerd. Op alle vier pagina's worden
de vier bedoelde H2-koppen gerenderd.

## Bron

`content/education-descriptions/vacature-opleidingsniveaus-live.md`
