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
echo json_encode(['wordpress'=>$wp_version,'elementor'=>ELEMENTOR_VERSION,'php'=>PHP_VERSION,'passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
