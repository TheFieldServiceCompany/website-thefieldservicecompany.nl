#!/usr/bin/env node

/**
 * Zet de gecontroleerde Markdown-concepten om naar WordPress-termcontent.
 *
 * Uitvoer: één JSON-array met { name, slug, description, intro }.
 * Het script schrijft zelf niets naar WordPress en bevat geen inloggegevens.
 */

import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');

const sources = [
  resolve(root, 'content/education-descriptions/opleidingsniveaus.md'),
  resolve(root, 'content/education-descriptions/studierichtingen.md'),
];

const escapeHtml = (value) =>
  value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const markdownSectionToHtml = (section) => {
  const lines = section.split(/\r?\n/);
  const blocks = [];
  let paragraph = [];

  const flushParagraph = () => {
    if (!paragraph.length) return;

    const text = paragraph.join(' ').replace(/\s+/g, ' ').trim();
    paragraph = [];

    if (!text) return;
    if (text.startsWith('Feitencheck:')) return;
    if (text.startsWith('Interne linkdoelen:')) return;

    blocks.push(`<p>${escapeHtml(text)}</p>`);
  };

  for (const rawLine of lines) {
    const line = rawLine.trim();

    if (!line) {
      flushParagraph();
      continue;
    }

    if (/^Slug(?: vacatureterm)?:/.test(line)) continue;
    if (/^Beoogde zichtbare naam:/.test(line)) continue;

    if (line.startsWith('### ')) {
      flushParagraph();
      blocks.push(`<h2>${escapeHtml(line.slice(4).trim())}</h2>`);
      continue;
    }

    paragraph.push(line);
  }

  flushParagraph();

  return blocks.join('\n\n');
};

const introFor = (name, type) => {
  if (type === 'level') {
    return `Ontdek welke fieldservicefuncties aansluiten op ${name}, welke praktijkkennis werkgevers vragen en hoe je vanuit dit opleidingsniveau kunt doorgroeien.`;
  }

  return `Ontdek hoe ${name} aansluit op field service, welke kennis je in het werk toepast en welke functies en technische omgevingen bij deze studierichting passen.`;
};

const items = [];

for (const source of sources) {
  const markdown = readFileSync(source, 'utf8');
  const sections = markdown.split(/^## /m).slice(1);

  const type = source.endsWith('opleidingsniveaus.md')
    ? 'level'
    : 'direction';

  for (const section of sections) {
    const [heading, ...bodyLines] = section.split(/\r?\n/);
    const name = heading.trim();
    const body = bodyLines.join('\n');
    const slugMatch = body.match(
      /^Slug(?: vacatureterm)?:\s*`([^`]+)`/m,
    );

    if (!slugMatch) {
      throw new Error(`Geen slug gevonden voor ${name} in ${source}`);
    }

    const description = markdownSectionToHtml(body);

    if (!description.includes('<h2>') || !description.includes('<p>')) {
      throw new Error(`Onvolledige HTML-conversie voor ${name}`);
    }

    items.push({
      name,
      slug: slugMatch[1],
      type,
      intro: introFor(name, type),
      description,
    });
  }
}

if (items.length !== 27) {
  throw new Error(`Verwacht 27 termen, gevonden ${items.length}`);
}

if (new Set(items.map(({ slug }) => slug)).size !== items.length) {
  throw new Error('Dubbele slug in de onderwijscontent');
}

process.stdout.write(`${JSON.stringify(items)}\n`);
