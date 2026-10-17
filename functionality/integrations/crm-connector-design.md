# Ontwerp `tfsc-crm-connector`

## Kernregel

WordPress/Elementor is eerst de duurzame ontvangstlaag. Een externe storing mag
nooit leiden tot verlies van formulierwaarden of uploads.

## Stroom

1. Elementor maakt de lokale inzending en bewaart uploads.
2. De connector valideert formulier-ID, velden, toestemming en bestandmetadata.
3. De connector maakt een outboxrecord met een UUID als extern submission-ID.
4. Een achtergrondtaak verstuurt server-side naar een beveiligde Supabase Edge
   Function of officiële CRM-API.
5. De ontvanger verwerkt idempotent op submission-ID.
6. Alleen na bevestigde verwerking krijgt de outbox status `succeeded`.
7. Bij tijdelijke fouten volgt retry met exponentiële back-off; permanente
   fouten gaan naar `dead_letter` of `review`.

## Statussen

`received`, `pending`, `sending`, `succeeded`, `retry`, `dead_letter`, `review`.

## Opslag

- Gebruik de Elementor submission als canonieke bron waar beschikbaar.
- Bewaar in de outbox alleen referenties, technische status en foutcategorieën.
- Geen persoonsgegevens of bestandsinhoud in gewone logs.
- Verwijder uploads niet voordat de remote verwerking aantoonbaar is bevestigd.
- Geen Supabase service-role key of databasewachtwoord in browsercode.

## Idempotentie en matching

- Unieke index op extern submission-ID.
- Kandidatenmatch op genormaliseerde e-mail, telefoon en LinkedIn.
- Eén betrouwbare match mag koppelen, maar bestaande gegevens niet stilzwijgend
  overschrijven.
- Geen of meerdere plausibele matches gaan naar menselijke review.
- Een vacaturegerichte inzending maakt of koppelt exact één Application.

## Minimale tests

- Registratie en sollicitatie met/zonder CV.
- PDF, DOC/DOCX en ODT; ongeldig en te groot bestand.
- Bestaande, nieuwe en ambigue kandidaat.
- Dubbele verzending en retry na tijdelijke storing.
- Uitgeschakelde CRM/Supabase-verbinding terwijl lokale data behouden blijft.
- Correcte kandidaat, document, Application en auditstatus in het CRM.
