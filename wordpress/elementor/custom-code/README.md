# Elementor Custom Code

Plaats hier de beheerde bronbestanden van Elementor Custom Code, één bestand per
codeblok. Vermeld in de bestandsheader minimaal:

- de naam en het Elementor Custom Code-ID;
- de uitvoerlocatie;
- de laadvolgorde of prioriteit;
- de afhankelijkheden;
- de status op DEV en LIVE.

Een export of kopie uit Elementor blijft een momentopname; dit is de beheerde
bronlocatie.

## Verwijderd: adviesknoppen 21893

Elementor Custom Code 21893 (`Script Advies Buttons VarLink Build`) is op
2 november 2026 naar de WordPress-prullenbak verplaatst. Het blok las een
verouderd zichtbaar vacaturenummer en voegde dit als queryparameter toe. TFSC
Custom Functions 0.6.0 verzorgt vacaturegebonden advies nu via POST; algemene
adviesknoppen behouden hun gewone link. De volledige herstelkopie staat in
`wordpress/exports/elementor/2026-11-02-before-all-vacancy-context-post/21893-before.json`.
