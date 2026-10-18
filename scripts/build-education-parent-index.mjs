#!/usr/bin/env node

/**
 * Bouwt navigatiecontent voor de twee parenttermen van beroep_opleiding.
 *
 * Invoer: WordPress REST-array met alle beroep_opleiding-termen.
 * Uitvoer: JSON-array met { id, slug, description } voor de twee parents.
 */

const terms = JSON.parse(await new Response(process.stdin).text());

const escapeHtml = (value) =>
  String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const decodeWordPressText = (value) =>
  String(value)
    .replaceAll('&amp;', '&')
    .replaceAll('&quot;', '"')
    .replaceAll('&#039;', "'")
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>');

const configurations = {
  opleidingsniveau: {
    intro:
      'Bekijk welke opleidingsniveaus aansluiten op functies binnen field service. Vergelijk jouw opleiding, praktijkervaring en ontwikkelmogelijkheden met de niveaus die werkgevers in actuele vacatures vragen.',
    heading: 'Kies jouw opleidingsniveau',
  },
  studierichting: {
    intro:
      'Ontdek welke technische studierichtingen aansluiten op field service. Vergelijk wat je leert met de machines, systemen, bedrijfssectoren, functies en actuele vacatures waarin je die kennis kunt toepassen.',
    heading: 'Kies jouw studierichting',
  },
};

const updates = Object.entries(configurations).map(([slug, config]) => {
  const parent = terms.find(
    (term) => term.slug === slug && Number(term.parent) === 0,
  );

  if (!parent) {
    throw new Error(`Parentterm ontbreekt: ${slug}`);
  }

  const children = terms
    .filter((term) => Number(term.parent) === Number(parent.id))
    .sort((a, b) => a.name.localeCompare(b.name, 'nl'));

  if (!children.length) {
    throw new Error(`Parentterm heeft geen kinderen: ${slug}`);
  }

  const items = children
    .map(
      (term) =>
        `<li><a href="${escapeHtml(term.link)}">${escapeHtml(decodeWordPressText(term.name))}</a></li>`,
    )
    .join('\n');

  return {
    id: parent.id,
    slug,
    description: [
      `<p>${escapeHtml(config.intro)}</p>`,
      `<h2>${escapeHtml(config.heading)}</h2>`,
      `<ul class="tfsc-beroep-opleiding-index">${items}</ul>`,
    ].join('\n\n'),
  };
});

process.stdout.write(`${JSON.stringify(updates)}\n`);
