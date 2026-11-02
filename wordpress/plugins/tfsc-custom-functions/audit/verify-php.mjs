import fs from 'node:fs';
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime } from '@php-wasm/node';

const php = new PHP(await loadNodeRuntime('8.3', {emscriptenOptions: {processId: 1}}));
const plugin = fs.readFileSync(new URL('../tfsc-custom-functions.php', import.meta.url), 'utf8');
const fixturePath = new URL('./.test-fixtures/plugin/', import.meta.url).pathname;
fs.mkdirSync(fixturePath, {recursive:true});
php.mkdir(fixturePath);
php.writeFile(fixturePath + 'tfsc-custom-functions.php', plugin);
php.writeFile(fixturePath + 'tfsc-custom-stylesheet.css', 'body { color: black; }');
const code = `<?php
error_reporting(E_ALL);
set_error_handler(function($level,$message,$file,$line) { throw new Exception($message . ' at ' . $line); });
define('ABSPATH','/');
$actions=[];$filters=[];$shortcodes=[];$styles=[];$removed=[];
function add_action($hook,$callback,$priority=10,$accepted_args=1){global $actions;$actions[$hook]=[$callback,$priority,$accepted_args];}
function add_filter($hook,$callback,$priority=10,$accepted_args=1){global $filters;$filters[$hook]=[$callback,$priority,$accepted_args];}
function add_shortcode($name,$callback){global $shortcodes;$shortcodes[$name]=$callback;}
function plugin_dir_path($file){return dirname($file).'/';}
function plugin_dir_url($file){return 'https://example.test/wp-content/plugins/tfsc-custom-functions/';}
function wp_enqueue_style($handle,$url,$deps,$version,$media){global $styles;$styles[]=compact('handle','url','deps','version','media');}
function wp_deregister_style($handle){global $removed;$removed[]=$handle;}
function is_search(){global $search;return $search;}
function get_search_query(){return '<script>bad</script>&test';}
function esc_html($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function esc_url($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function content_url($s){return 'https://example.test/wp-content'.$s;}
function home_url($s){return 'https://example.test'.($s ? '/'.ltrim($s,'/') : '');}
function get_queried_object_id(){global $queried_id;return $queried_id ?? 0;}
function get_post_type($id){global $post_types;return $post_types[$id] ?? null;}
function get_post_meta($id,$key,$single){global $post_meta;return $post_meta[$id][$key] ?? '';}
function is_singular($type=''){global $singular;return $singular === $type;}
function is_page($id){global $page_id;return (int)($page_id ?? 0)===(int)$id;}
function sanitize_text_field($value){return trim(strip_tags((string)$value));}
function wp_unslash($value){return stripslashes((string)$value);}
function status_header($status){global $status_code;$status_code=$status;}
function wp_die($message,$title='',$args=[]){throw new Exception('WP_DIE: '.$message);}
function esc_html__($text,$domain='default'){return $text;}
function esc_attr($text){return htmlspecialchars((string)$text,ENT_QUOTES,'UTF-8');}
function wp_json_encode($value){return json_encode($value);}
class WP_Post { public $post_content; function __construct($text){$this->post_content=$text;} }
function get_post(){global $test_post;return $test_post;}
function wp_strip_all_tags($s){return strip_tags($s);}
function strip_shortcodes($s){return preg_replace('/\\[test[^\\]]*\\]/','',$s);}
require '${fixturePath}tfsc-custom-functions.php';
$checks=[];
function check($condition,$name){global $checks;if(!$condition){throw new Exception('FAIL: '.$name);}$checks[]=$name;}
check($actions['wp_enqueue_scripts'][1] === PHP_INT_MAX,'Styles retain final enqueue priority');
tfsc_custom_functions_enqueue_styles();
$first=end($styles);
check($first['handle']==='theme-override','Existing stylesheet handle preserved');
check(strpos($first['url'],'functions//')===false,'Stylesheet URL has no duplicate slash');
touch('${fixturePath}tfsc-custom-stylesheet.css',1700000000);clearstatcache();tfsc_custom_functions_enqueue_styles();
check(end($styles)['version']==='1700000000','CSS version follows file modification time');
touch('${fixturePath}tfsc-custom-stylesheet.css',1700000010);clearstatcache();tfsc_custom_functions_enqueue_styles();
check(end($styles)['version']==='1700000010','Next CSS save changes the version');
unlink('${fixturePath}tfsc-custom-stylesheet.css');clearstatcache();$count=count($styles);tfsc_custom_functions_enqueue_styles();
check(count($styles)===$count,'Missing CSS does not emit warnings or a broken link');
$search=false;check(tfsc_custom_functions_archive_title('Original')==='Original','Normal archive title unchanged');
$search=true;check(strpos(tfsc_custom_functions_archive_title('Original'),'<script>')===false,'Search text escaped');
$test_post=null;check(tfsc_custom_functions_reading_time()==='','No post is handled without warnings');
$test_post=new WP_Post(str_repeat('één ',199));check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Accented words counted correctly below threshold');
$test_post=new WP_Post(str_repeat('één ',200));check(tfsc_custom_functions_reading_time()==='1 minuut','One minute uses singular');
$test_post=new WP_Post(str_repeat('woord ',400));check(tfsc_custom_functions_reading_time()==='2 minuten','Longer reading time preserved');
$test_post=new WP_Post('[test word word]'.str_repeat('woord ',199));check(tfsc_custom_functions_reading_time()==='Minder dan 1 minuut.','Shortcode syntax excluded from word count');
$wp=(object)['request'=>'vacatures'];check(tfsc_custom_functions_current_page_url()==='https://example.test/vacatures','Current URL shortcode remains compatible');
unset($wp);check(tfsc_custom_functions_current_page_url()==='https://example.test','Current URL handles absent request');
check(!isset($actions['wp_head']),'No fixed font preloads compete with Elementor typography');
check(!isset($actions['elementor/frontend/after_register_styles']),'Elementor may load the icon fonts its widgets require');
check(isset($shortcodes['rm_get_estimated_time_to_read']) && isset($shortcodes['current_page_url']),'Public shortcode names preserved');
check(TFSC_CUSTOM_FUNCTIONS_VERSION==='0.5.0','Plugin version records the vacancy-context release');
$queried_id=358;$post_types=[358=>'vacature'];$post_meta=[358=>['vacaturenummer'=>' 358 ']];
check(tfsc_custom_functions_vacancy_number()==='358','Stored vacancy field is used as the business reference');
$post_meta[358]['vacaturenummer']='358<script>';
check(tfsc_custom_functions_vacancy_number()==='','Invalid vacancy values are rejected rather than guessed');
$post_meta[358]['vacaturenummer']='358';
class TFSC_Test_Widget {function get_id(){return TFSC_LEGACY_VACANCY_TRANSFER_WIDGET_ID;}}
$singular='vacature';
check(tfsc_custom_functions_remove_legacy_vacancy_transfer('legacy-script',new TFSC_Test_Widget())==='','Legacy Elementor transfer widget is suppressed on vacancies');
$singular='post';
check(tfsc_custom_functions_remove_legacy_vacancy_transfer('legacy-script',new TFSC_Test_Widget())==='legacy-script','Unrelated pages are unchanged');
$page_id=TFSC_APPLICATION_FORM_PAGE_ID;
$_SERVER['REQUEST_METHOD']='POST';
$_POST=['tfsc_vacancy_application'=>'1','vacaturenummer'=>'358'];
tfsc_custom_functions_receive_application_context();
check($_GET['vacaturenummer']==='358' && $_REQUEST['vacaturenummer']==='358','Validated POST context reaches the existing Elementor request field');
$_POST=['tfsc_vacancy_application'=>'1','vacaturenummer'=>'358<script>'];$status_code=null;$blocked=false;
try{tfsc_custom_functions_receive_application_context();}catch(Exception $error){$blocked=str_starts_with($error->getMessage(),'WP_DIE:');}
check($blocked && $status_code===400,'Invalid POST context is stopped with HTTP 400');
$_POST=['tfsc_vacancy_application'=>'1','vacaturenummer'=>'358'];
$singular='vacature';ob_start();tfsc_custom_functions_render_vacancy_context();$context=ob_get_clean();
check(strpos($context,'method="post"')!==false && strpos($context,'name="vacaturenummer" value="358"')!==false,'Application context renders as POST without a query string');
check(strpos($context,'postTitle')===false && strpos($context,'contractvorm')===false && strpos($context,'bedrijfsnaam')===false,'Redundant browser-derived vacancy fields are absent');
echo json_encode(['php'=>PHP_VERSION,'passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
`;
const result = await php.runStream({code});
const stdout = await result.stdoutText;
const stderr = await result.stderrText;
if (stderr || stdout.includes('Fatal error')) throw new Error(stderr + stdout);
console.log(stdout);
fs.writeFileSync(new URL('./php-validation.json', import.meta.url), stdout);
php.exit();
