<?php
require '/wordpress/wp-load.php';
$checks = [];
function check($condition, $message) {
    if (!$condition) throw new Exception('FAIL ' . $message);
    $GLOBALS['checks'][] = $message;
}
$manager = \Elementor\Plugin::instance()->widgets_manager;
$registered = $manager->get_widget_types('breadcrumbs');
check($registered instanceof \TFSC\RankMathBreadcrumbs\Widget_Breadcrumbs, 'Full Elementor widget manager registers TFSC breadcrumbs with Free active');
\Elementor\Core\Frontend\Performance::set_use_style_controls(true);
$controls = $registered->get_controls();
check(isset($controls['typography_font_weight'], $controls['text_color'], $controls['link_color'], $controls['_css_classes'], $controls['_margin'], $controls['_border_width']), 'Real Elementor resolves typography, colors and advanced controls');
check(\Elementor\Plugin::instance()->controls_manager->get_control('color')->get_settings('global')['active'] === true, 'Stored Elementor global link colors are enabled');
\Elementor\Core\Frontend\Performance::set_use_style_controls(false);

register_post_type('beroepengids', ['public'=>true,'has_archive'=>'beroepengids/artikelen','rewrite'=>['slug'=>'beroepengids/artikel'],'labels'=>['name'=>'Artikelen']]);
register_taxonomy('beroep_functienaam', 'beroepengids', ['public'=>true,'hierarchical'=>true,'rewrite'=>['slug'=>'beroepengids/functienamen'],'labels'=>['name'=>'Functienamen']]);
$termResult=wp_insert_term('Field Service Engineer','beroep_functienaam',['slug'=>'field-service-engineer']);
check(!is_wp_error($termResult),'Disposable taxonomy term created');
$term=get_term($termResult['term_id']);
$postId=wp_insert_post(['post_type'=>'beroepengids','post_status'=>'publish','post_title'=>'Field Service Engineer','post_name'=>'field-service-engineer']);
wp_set_object_terms($postId, $term->term_id, 'beroep_functienaam');
require '/snippet-99.php';
require '/snippet-199.php';
define('TFSC_COMBO_BASE','vacatures');
function tfsc_is_combo_archive() { return $GLOBALS['combo_active'] ?? false; }
function tfsc_get_combo_archive() { return $GLOBALS['combo_items'] ?? []; }

$data=['id'=>'7e94e03b','elType'=>'widget','widgetType'=>'breadcrumbs','settings'=>[
    'typography_font_weight'=>'600', 'link_color'=>'#035D92',
    '__globals__'=>['text_color'=>'globals/colors?id=text','link_color'=>'globals/colors?id=text','_border_color'=>'globals/colors?id=3a5c802f'],
    '_css_classes'=>'broodkruimel',
    '_margin'=>['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'10','left'=>'0','isLinked'=>false],
    '_border_border'=>'solid','_border_width'=>['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'1','left'=>'0','isLinked'=>false],
],'elements'=>[]];

foreach (['base','term','article','archive','combo'] as $context) {
    $combo_active=$context==='combo';
    if ($context==='term') $query=new WP_Query(['tax_query'=>[['taxonomy'=>'beroep_functienaam','terms'=>[$term->term_id]]]]);
    elseif ($context==='article') $query=new WP_Query(['p'=>$postId,'post_type'=>'beroepengids']);
    elseif ($context==='archive') $query=new WP_Query(['post_type'=>'beroepengids']);
    else $query=new WP_Query();
    $GLOBALS['wp_query']=$query;
    if ($context==='base') {
        $query->set('tfsc_taxonomy_archive','beroep_functienaam');
        $query->is_home=false; $query->is_archive=true; $query->is_404=false;
    }
    if ($context==='combo') $combo_items=[['taxonomy'=>'functienaam','term'=>$term],['taxonomy'=>'bedrijfssector','term'=>new WP_Term((object)['term_id'=>999,'taxonomy'=>'bedrijfssector','name'=>'Energie','slug'=>'energie','parent'=>0])]];
    if ($context==='article') { $GLOBALS['post']=get_post($postId); setup_postdata($GLOBALS['post']); }
    $widget=\Elementor\Plugin::instance()->elements_manager->create_element_instance($data);
    check($widget instanceof \TFSC\RankMathBreadcrumbs\Widget_Breadcrumbs, $context.' stored template data resolves without migration');
    ob_start(); $widget->print_element(); $html=ob_get_clean();
    check(strpos($html,'elementor-element-7e94e03b')!==false && strpos($html,'broodkruimel')!==false, $context.' existing widget ID and CSS class survive real Elementor rendering');
    check(strpos($html,'rank-math-breadcrumb')!==false, $context.' renders Rank Math navigation through real Elementor');
    if ($context==='term') check(strpos($html,'href="https://example.test/beroepengids/functienamen/"')!==false,'Real detail query retains clickable virtual subarchive');
    if ($context==='combo') check(strpos($html,'href="https://example.test/vacatures/functienaam/field-service-engineer/"')!==false,'Real widget preserves combo parent link');
    check(strpos($html,'class="elementor-widget-container"')!==false,$context.' preserves the CSS inner wrapper');
    $schema=(new \RankMath\Schema\Breadcrumbs())->process([],null);
    check(!empty($schema['BreadcrumbList']['itemListElement']),$context.' still generates breadcrumb schema with the TFSC filters');
}

$cssId=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'CSS fixture']);
update_post_meta($cssId,'_elementor_edit_mode','builder');
update_post_meta($cssId,'_elementor_data',wp_slash(wp_json_encode([$data])));
$cssFile=new \Elementor\Core\Files\CSS\Post($cssId);
$css=$cssFile->get_content();
check(strpos($css,'elementor-element-7e94e03b')!==false,'Real Elementor regenerates CSS for the existing widget ID');
check($widget->get_settings('typography_font_weight')==='600','Stored typography value is retained');
check(preg_match('/var\(\s*--e-global-color-text\s*\)/',$css)===1,'Saved global text color survives CSS regeneration');
check(strpos($css,'margin:0px 0px 10px 0px')!==false,'Saved margin survives CSS regeneration');
check(strpos($css,'border-width:0px 0px 1px 0px')!==false,'Saved bottom border survives CSS regeneration');
$data['settings']['typography_typography']='custom';
$cssId=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Custom typography fixture']);
update_post_meta($cssId,'_elementor_edit_mode','builder');
update_post_meta($cssId,'_elementor_data',wp_slash(wp_json_encode([$data])));
$cssFile=new \Elementor\Core\Files\CSS\Post($cssId);
$cssFile->update();
check(strpos($cssFile->get_content(),'font-weight:600')!==false,'Explicit custom typography generates its saved font weight');

$GLOBALS['wp_query']=new WP_Query(['p'=>$postId,'post_type'=>'beroepengids']);
$GLOBALS['post']=get_post($postId);
$GLOBALS['post']->post_content='<a href="https://example.org/subject" data-schema-attribute="about mentions">Subject &amp; topic</a> <a href="https://example.org/other" data-schema-attribute="mentions">Other</a> <a href="https://example.org/unmarked">Unmarked</a>';
$input=['WebPage'=>['@type'=>'WebPage','about'=>['@id'=>'https://example.test/#organization']],'Other'=>['@type'=>'Article']];
$marked=apply_filters('rank_math/json_ld',$input,null);
check($marked['WebPage']['about'][0]===['@id'=>'https://example.test/#organization'],'Marked schema preserves the original organization reference');
check(count($marked['WebPage']['about'])===2 && count($marked['WebPage']['mentions'])===2,'Only marked links add entities and a dual marker adds both properties');
check($marked['WebPage']['mentions'][0]===['@type'=>'Thing','name'=>'Subject &amp; topic','sameAs'=>'https://example.org/subject'],'Existing marker text, URL and order remain unchanged');
check($marked['Other']===$input['Other'],'Unrelated schema nodes remain intact');
check(\TFSC\RankMathBreadcrumbs\preserve_marked_entities([])===[],'Missing WebPage does not create new schema');
$GLOBALS['post']->post_content='<a data-schema-attribute="mentions">Missing URL</a> <a href="https://example.org/plain">Plain link</a>';
check(\TFSC\RankMathBreadcrumbs\preserve_marked_entities($input)===$input,'Unmarked links and missing destinations are ignored');
$GLOBALS['wp_query']=new WP_Query(['post_type'=>'beroepengids']);
$GLOBALS['post']->post_content='<a href="https://example.org/subject" data-schema-attribute="mentions">Subject</a>';
check(\TFSC\RankMathBreadcrumbs\preserve_marked_entities($input)===$input,'Archive pages do not inherit singular link markers');

$_SERVER['REQUEST_URI']='/old-page?kind=fixture';
$rule=['sources'=>[['pattern'=>'old-page?kind=fixture','comparison'=>'exact']]];
check(apply_filters('rank_math/redirection/redirection_match',false,$rule)===true,'Free engine matches the exact stored URI including its query');
$rule['sources'][0]['pattern']='old-page?kind=different';
check(apply_filters('rank_math/redirection/redirection_match',false,$rule)===false,'A different query cannot match the exact redirect');
check(apply_filters('rank_math/redirection/redirection_match',true,$rule)===true,'An existing match from another filter is preserved');
check(apply_filters('rank_math/redirection/redirection_match',false,false)===false,'Missing redirect data is safely ignored');

rank_math()->settings->set('titles','use_multiple_locations',false);
rank_math()->settings->set('sitemap','local_sitemap',true);
check(apply_filters('rank_math/sitemap/locations',false)===true,'Saved single-location sitemap is enabled with Free');
$kml=new \RankMath\Local_Seo\KML_File();
check(strpos($kml->local_sitemap_content(),'https://example.test/locations.kml')!==false,'Actual Free generator emits the existing local KML sitemap link');
rank_math()->settings->set('sitemap','local_sitemap',false);
check(apply_filters('rank_math/sitemap/locations',false)===false,'A disabled sitemap stays disabled');
check(apply_filters('rank_math/sitemap/locations',true)===true,'Another sitemap provider retains its enabled state');
rank_math()->settings->set('titles','use_multiple_locations',true);
rank_math()->settings->set('sitemap','local_sitemap',true);
check(apply_filters('rank_math/sitemap/locations',false)===false,'Compatibility does not claim support for multiple locations');

define('RANK_MATH_PRO_VERSION','fixture');
$GLOBALS['wp_query']=new WP_Query(['p'=>$postId,'post_type'=>'beroepengids']);
check(\TFSC\RankMathBreadcrumbs\preserve_marked_entities($input)===$input,'Active Pro owns entity generation without duplicate fallback entities');
$_SERVER['REQUEST_URI']='/old-page?kind=fixture';
$rule['sources'][0]['pattern']='old-page?kind=fixture';
check(\TFSC\RankMathBreadcrumbs\match_query_redirect(false,$rule)===false,'Active Pro retains ownership of query matching');
rank_math()->settings->set('titles','use_multiple_locations',false);
check(\TFSC\RankMathBreadcrumbs\enable_single_location_sitemap(false)===false,'Active Pro retains ownership of the local sitemap switch');
echo json_encode(['wordpress'=>$wp_version,'elementor'=>ELEMENTOR_VERSION,'php'=>PHP_VERSION,'passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
