import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';

const [runtimeRoot, wordpressRoot, rankMathRoot] = process.argv.slice(2);
if (!runtimeRoot || !wordpressRoot || !rankMathRoot) {
  throw new Error('Usage: node tests/run.mjs /path/to/runtime/node_modules /path/to/wordpress /path/to/seo-by-rank-math');
}
const require = createRequire(path.join(path.resolve(runtimeRoot), 'loader.cjs'));
const { PHP } = require('@php-wasm/universal');
const { loadNodeRuntime } = require('@php-wasm/node');
const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 1 } }));
const pluginRoot = path.resolve(import.meta.dirname, '..');
const wordpressRepo = path.resolve(pluginRoot, '../..');

function load(source, target) {
  php.writeFile(target, fs.readFileSync(source));
}

for (const directory of ['/core', '/rank-math', '/plugin', '/snippets']) {
  php.mkdir(directory);
}
for (const file of ['plugin.php', 'class-wp-hook.php', 'formatting.php', 'kses.php']) {
  load(path.join(wordpressRoot, 'wp-includes', file), `/core/${file}`);
}
for (const file of ['tfsc-rank-math-breadcrumbs.php', 'widget-breadcrumbs.php']) {
  load(path.join(pluginRoot, file), `/plugin/${file}`);
}
load(path.join(rankMathRoot, 'includes/traits/class-hooker.php'), '/rank-math/class-hooker.php');
load(path.join(rankMathRoot, 'includes/frontend/class-breadcrumbs.php'), '/rank-math/class-breadcrumbs.php');
load(path.join(rankMathRoot, 'includes/template-tags.php'), '/rank-math/template-tags.php');
load(path.join(wordpressRepo, 'snippets/php/99-rank-math-combo-archive-breadcrumbs.php'), '/snippets/99.php');
load(path.join(wordpressRepo, 'exports/code-snippets/2026-10-17/all/rewrite-breadcrumbs-met-taxonomy-slug-v3-5.code-snippets.php'), '/snippets/199.php');
try {
  const result = await php.run({ code: fs.readFileSync(new URL('./integration.php', import.meta.url), 'utf8') });
  if (result.errors || result.exitCode !== 0) {
    throw new Error(result.errors + result.text);
  }
  console.log(result.text);
} finally {
  php.exit();
}
