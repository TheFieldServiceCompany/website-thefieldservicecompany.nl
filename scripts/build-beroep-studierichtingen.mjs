#!/usr/bin/env node

/** Bouw gevalideerde REST-content voor beroep_studierichting. */

import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const sourcePath = resolve(
  import.meta.dirname,
  '../content/education-descriptions/beroep-studierichtingen.md',
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
const allHeadings = [];

for (const section of sections) {
  const [nameLine, ...bodyLines] = section.split(/\r?\n/);
  const name = nameLine.trim();
  const body = bodyLines.join('\n');
  const slug = body.match(/^Slug:\s*`([^`]+)`/m)?.[1];
  const intro = body.match(/^Intro:\s*(.+)$/m)?.[1]?.trim();

  if (!slug || !intro) {
    throw new Error(`Slug of intro ontbreekt bij ${name}`);
  }
  if (!/field service/i.test(intro)) {
    throw new Error(`Intro mist field service bij ${name}`);
  }

  const content = body
    .replace(/^Slug:.*$/m, '')
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
      const heading = line.slice(4).trim();
      allHeadings.push({ name, heading });
      blocks.push(`<h2>${escapeHtml(heading)}</h2>`);
    } else {
      paragraph.push(line);
    }
  }
  flushParagraph();

  const description = blocks.join('\n\n');
  const headingCount = (description.match(/<h2>/g) || []).length;
  const wordCount = description
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .split(' ').length;

  if (headingCount !== 4) {
    throw new Error(`${name} heeft ${headingCount} H2-koppen; verwacht 4`);
  }
  if (wordCount < 250 || wordCount > 500) {
    throw new Error(`${name} heeft ongewenst woordenaantal: ${wordCount}`);
  }
  if (!/field service/i.test(description)) {
    throw new Error(`Description mist field service bij ${name}`);
  }

  items.push({ name, slug, intro, description, headingCount, wordCount });
}

if (items.length !== 23) {
  throw new Error(`Verwacht 23 studierichtingen, gevonden ${items.length}`);
}

const duplicateHeadings = allHeadings.filter(
  ({ heading }, index) =>
    allHeadings.findIndex((candidate) => candidate.heading === heading) !== index,
);
if (duplicateHeadings.length) {
  throw new Error(
    `Dubbele H2-koppen: ${duplicateHeadings.map(({ heading }) => heading).join(' | ')}`,
  );
}

if (new Set(items.map(({ slug }) => slug)).size !== items.length) {
  throw new Error('Dubbele slugs in Beroepengids-studierichtingen');
}

process.stdout.write(`${JSON.stringify(items)}\n`);
