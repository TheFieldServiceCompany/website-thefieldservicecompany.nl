#!/usr/bin/env node

/** Bouw gevalideerde REST-content voor beroep_functienaam. */

import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const sourcePath = resolve(
  import.meta.dirname,
  '../content/beroepengids-descriptions/beroep-functienamen.md',
);
const markdown = readFileSync(sourcePath, 'utf8');

const escapeHtml = (value) =>
  value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const sections = markdown.split(/^## /m).slice(1);
const items = [];

for (const section of sections) {
  const [nameLine, ...bodyLines] = section.split(/\r?\n/);
  const name = nameLine.trim();
  const body = bodyLines.join('\n');
  const slug = body.match(/^Slug:\s*`([^`]+)`/m)?.[1];
  const archiveTitle = body.match(/^Archive title:\s*(.+)$/m)?.[1]?.trim();
  const intro = body.match(/^Intro:\s*(.+)$/m)?.[1]?.trim();
  const descriptionMarkup =
    body.match(/^Description markup:\s*(.+)$/m)?.[1]?.trim() || 'html';

  if (!slug || !archiveTitle || !intro) {
    throw new Error(`Slug, archieftitel of intro ontbreekt bij ${name}`);
  }
  if (archiveTitle !== `Functienaam ${name} | Artikelen`) {
    throw new Error(`Onverwachte archieftitel bij ${name}: ${archiveTitle}`);
  }
  if (!['html', 'plain'].includes(descriptionMarkup)) {
    throw new Error(`Onbekende descriptionopmaak bij ${name}`);
  }
  const descriptionHeading = `Functienaam ${name}`;
  if (!/field service/i.test(intro)) {
    throw new Error(`Intro mist field service bij ${name}`);
  }

  const content = body
    .replace(/^Slug:.*$/m, '')
    .replace(/^Archive title:.*$/m, '')
    .replace(/^Intro:.*$/m, '')
    .replace(/^Description markup:.*$/m, '')
    .trim();
  const blocks = [`<h2>${escapeHtml(descriptionHeading)}</h2>`];
  let paragraph = [];

  const flushParagraph = () => {
    const value = paragraph.join(' ').replace(/\s+/g, ' ').trim();
    paragraph = [];
    if (value) {
      blocks.push(
        descriptionMarkup === 'plain'
          ? escapeHtml(value)
          : `<p>${escapeHtml(value)}</p>`,
      );
    }
  };

  for (const rawLine of content.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line) {
      flushParagraph();
    } else {
      paragraph.push(line);
    }
  }
  flushParagraph();

  const separator = descriptionMarkup === 'plain' ? '\r\n\r\n' : '\n\n';
  const description = blocks.join(separator);
  const headingCount = (description.match(/<h2>/g) || []).length;
  const paragraphCount = blocks.length - 1;
  const wordCount = description
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .split(' ').length;

  if (headingCount !== 1) {
    throw new Error(`${name} heeft ${headingCount} H2-koppen; verwacht 1`);
  }
  if (!description.startsWith(`<h2>${escapeHtml(descriptionHeading)}</h2>`)) {
    throw new Error(`Description begint niet met de juiste H2 bij ${name}`);
  }
  if (paragraphCount !== 3) {
    throw new Error(`${name} heeft ${paragraphCount} alinea's; verwacht 3`);
  }
  if (wordCount < 100 || wordCount > 220) {
    throw new Error(`${name} heeft ongewenst woordenaantal: ${wordCount}`);
  }
  if (!/field service/i.test(description)) {
    throw new Error(`Description mist field service bij ${name}`);
  }
  if (!/artikel/i.test(description)) {
    throw new Error(`Description verwijst niet naar artikelen bij ${name}`);
  }
  if (!description.includes(name)) {
    throw new Error(`Description mist de functienaam ${name}`);
  }

  items.push({
    name,
    slug,
    archiveTitle,
    descriptionHeading,
    descriptionMarkup,
    intro,
    description,
    headingCount,
    paragraphCount,
    wordCount,
  });
}

if (items.length !== 32) {
  throw new Error(`Verwacht 32 functienamen, gevonden ${items.length}`);
}
if (new Set(items.map(({ slug }) => slug)).size !== items.length) {
  throw new Error('Dubbele slugs in Beroepengids-functienamen');
}

if (new Set(items.map(({ intro }) => intro)).size !== items.length) {
  throw new Error('Dubbele intro in Beroepengids-functienamen');
}
if (new Set(items.map(({ description }) => description)).size !== items.length) {
  throw new Error('Dubbele description in Beroepengids-functienamen');
}

process.stdout.write(`${JSON.stringify(items)}\n`);
