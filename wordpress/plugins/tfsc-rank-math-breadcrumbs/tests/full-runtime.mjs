import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';

const [modulesRoot, wordpressRoot, rankMathRoot, elementorRoot, sqliteRoot] = process.argv.slice(2);
if (!modulesRoot || !wordpressRoot || !rankMathRoot || !elementorRoot || !sqliteRoot) {
  throw new Error('Usage: node tests/full-runtime.mjs /path/to/node_modules /path/to/wordpress /path/to/seo-by-rank-math /path/to/elementor /path/to/sqlite-database-integration');
}
const require = createRequire(path.resolve(modulesRoot, 'loader.cjs'));
const { PHP } = require('@php-wasm/universal');
const { loadNodeRuntime } = require('@php-wasm/node');
const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 2 } }));
const pluginSource = path.resolve(import.meta.dirname, '..');
const wordpressRepo = path.resolve(pluginSource, '../..');

function copyDirectory(source, target) {
  php.mkdir(target);
  for (const entry of fs.readdirSync(source, { withFileTypes: true })) {
    if (entry.isDirectory()) copyDirectory(path.join(source, entry.name), target + '/' + entry.name);
    else if (entry.isFile()) php.writeFile(target + '/' + entry.name, fs.readFileSync(path.join(source, entry.name)));
  }
}
php.mkdir('/wordpress');
for (const entry of fs.readdirSync(wordpressRoot, { withFileTypes: true })) {
  if (entry.isFile() && entry.name.endsWith('.php') && entry.name !== 'wp-config.php') {
    php.writeFile('/wordpress/' + entry.name, fs.readFileSync(path.join(wordpressRoot, entry.name)));
  }
}
copyDirectory(path.join(wordpressRoot, 'wp-admin'), '/wordpress/wp-admin');
copyDirectory(path.join(wordpressRoot, 'wp-includes'), '/wordpress/wp-includes');
php.mkdir('/wordpress/wp-content/plugins');
php.mkdir('/wordpress/wp-content/themes');
copyDirectory(sqliteRoot, '/wordpress/wp-content/plugins/sqlite-database-integration');
copyDirectory(elementorRoot, '/wordpress/wp-content/plugins/elementor');
copyDirectory(rankMathRoot, '/wordpress/wp-content/plugins/seo-by-rank-math');
php.mkdir('/wordpress/wp-content/plugins/tfsc-rank-math-breadcrumbs');
for (const name of ['tfsc-rank-math-breadcrumbs.php', 'widget-breadcrumbs.php']) {
  php.writeFile('/wordpress/wp-content/plugins/tfsc-rank-math-breadcrumbs/' + name, fs.readFileSync(path.join(pluginSource, name)));
}
php.writeFile('/wordpress/wp-content/db.php', fs.readFileSync(path.join(sqliteRoot, 'db.copy')));
php.writeFile('/wordpress/wp-config.php', `<?php
define('DB_NAME','test'); define('DB_USER','test'); define('DB_PASSWORD','test'); define('DB_HOST','localhost');
define('DB_CHARSET','utf8'); define('DB_COLLATE','');
define('WP_HOME','https://example.test'); define('WP_SITEURL','https://example.test');
define('WP_MEMORY_LIMIT','512M'); define('WP_MAX_MEMORY_LIMIT','512M');
define('WP_DEBUG',true); define('WP_DEBUG_DISPLAY',false); define('WP_DEBUG_LOG',true);
define('AUTOMATIC_UPDATER_DISABLED',true); define('DISABLE_WP_CRON',true);
define('AUTH_KEY','local-fixture'); define('SECURE_AUTH_KEY','local-fixture');
define('LOGGED_IN_KEY','local-fixture'); define('NONCE_KEY','local-fixture');
$table_prefix='wp_'; if (!defined('ABSPATH')) define('ABSPATH',__DIR__.'/'); require ABSPATH.'wp-settings.php';
`);
for (const [id, relative] of [
  [99, 'snippets/php/99-rank-math-combo-archive-breadcrumbs.php'],
  [199, 'exports/code-snippets/2026-10-17/all/rewrite-breadcrumbs-met-taxonomy-slug-v3-5.code-snippets.php'],
]) {
  php.writeFile('/snippet-' + id + '.php', fs.readFileSync(path.join(wordpressRepo, relative)));
}
async function run(code) {
  const result = await php.run({ code, relativeUri: '/', $_SERVER: { HTTP_HOST: 'example.test', REQUEST_URI: '/', HTTPS: 'on', SERVER_PROTOCOL: 'HTTP/1.1', REQUEST_METHOD: 'GET' } });
  console.log(result.text);
  if (result.errors) console.error(result.errors);
  if (result.exitCode !== 0) throw new Error('PHP integration test failed with exit code ' + result.exitCode);
  return result;
}
try {
  await run(`<?php
define('WP_INSTALLING',true);
require '/wordpress/wp-load.php';
require '/wordpress/wp-admin/includes/upgrade.php';
wp_install('Breadcrumb test','localadmin','local@example.test',false,'','fixture-password');
update_option('active_plugins',['elementor/elementor.php','seo-by-rank-math/rank-math.php','tfsc-rank-math-breadcrumbs/tfsc-rank-math-breadcrumbs.php']);
update_option('permalink_structure','/%postname%/');
update_option('rank-math-options-general',['breadcrumbs'=>'on','breadcrumbs_home'=>'on','breadcrumbs_home_label'=>'Home','breadcrumbs_home_link'=>'https://example.test/','breadcrumbs_separator'=>'›']);
echo 'Installed disposable in-memory WordPress';
`);
  const phpTest = fs.readFileSync(new URL('./full-runtime.php', import.meta.url), 'utf8');
  await run(phpTest);
} finally {
  if (php.fileExists('/wordpress/wp-content/debug.log')) {
    console.log('PHP debug log', php.readFileAsText('/wordpress/wp-content/debug.log').slice(-5000));
  }
  php.exit();
}
