#!/usr/bin/env node

/** Bouw gevalideerde REST-content voor beroep_kennisgebied. */

import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const sourcePath = resolve(
  import.meta.dirname,
  '../content/beroepengids-descriptions/beroep-kennisgebieden.md',
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
  const descriptionHeading = `Kennisgebied ${name}`;

  if (!slug || !archiveTitle || !intro) {
    throw new Error(`Slug, archieftitel of intro ontbreekt bij ${name}`);
  }
  if (archiveTitle !== `${descriptionHeading} | Artikelen`) {
    throw new Error(`Onverwachte archieftitel bij ${name}: ${archiveTitle}`);
  }
  if (!/field service/i.test(intro)) {
    throw new Error(`Intro mist field service bij ${name}`);
  }

  const content = body
    .replace(/^Slug:.*$/m, '')
    .replace(/^Archive title:.*$/m, '')
    .replace(/^Intro:.*$/m, '')
    .trim();
  const blocks = [];
  let paragraph = [];

  const flushParagraph = () => {
    const value = paragraph.join(' ').replace(/\s+/g, ' ').trim();
    paragraph = [];
    if (value) blocks.push(`<p>${escapeHtml(value)}</p>`);
  };

  for (const rawLine of content.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line) {
      flushParagraph();
    } else if (line.startsWith('### ')) {
      flushParagraph();
      blocks.push(`<h2>${escapeHtml(line.slice(4).trim())}</h2>`);
    } else {
      paragraph.push(line);
    }
  }
  flushParagraph();

  const description = blocks.join('\n\n');
  const headingCount = (description.match(/<h2>/g) || []).length;
  const paragraphCount = (description.match(/<p>/g) || []).length;
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
  if (wordCount < 100 || wordCount > 180) {
    throw new Error(`${name} heeft ongewenst woordenaantal: ${wordCount}`);
  }
  if (!/field service/i.test(description)) {
    throw new Error(`Description mist field service bij ${name}`);
  }
  if (!/artikel/i.test(description)) {
    throw new Error(`Description verwijst niet naar artikelen bij ${name}`);
  }

  items.push({
    name,
    slug,
    archiveTitle,
    descriptionHeading,
    intro,
    description,
    headingCount,
    paragraphCount,
    wordCount,
  });
}

if (items.length !== 25) {
  throw new Error(`Verwacht 25 kennisgebieden, gevonden ${items.length}`);
}
if (new Set(items.map(({ slug }) => slug)).size !== items.length) {
  throw new Error('Dubbele slugs in Beroepengids-kennisgebieden');
}
if (new Set(items.map(({ intro }) => intro)).size !== items.length) {
  throw new Error('Dubbele intro in Beroepengids-kennisgebieden');
}
if (new Set(items.map(({ description }) => description)).size !== items.length) {
  throw new Error('Dubbele description in Beroepengids-kennisgebieden');
}

process.stdout.write(`${JSON.stringify(items)}\n`);
