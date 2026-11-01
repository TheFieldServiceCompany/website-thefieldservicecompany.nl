<?php
error_reporting(E_ALL);
set_error_handler(function($level,$message){throw new Exception($message);});
class WP_Term {public $term_id=1;public $taxonomy='kennisgebied';}
class WP_Post {public $ID=100;}
class WP_Query {
 public $query_vars;public $posts=[];
 function __construct($args=[]){global $probe_match,$last_probe;$this->query_vars=$args;$last_probe=$args;$this->posts=$probe_match?[1]:[];if(!empty($args['_tfsc_relposts_probe'])){global $hook;$hook($this);}}
 function get($key){return $this->query_vars[$key]??null;}
 function set($key,$value){$this->query_vars[$key]=$value;}
 function have_posts(){return (bool)$this->posts;}
}
class TestPod {function exists(){return true;}function fields(){global $fields;return $fields;}function field($key){global $relations;return $relations[$key]??[];}}
function add_action($name,$callback,$priority,$args){global $hook;$hook=$callback;}
function get_queried_object(){global $object;return $object;}
function get_post_type($id){return 'beroepengids';}
function pods($type,$id){global $pod_exists;return $pod_exists?new TestPod:false;}
function sanitize_key($key){return $key;}
function taxonomy_exists($taxonomy){return true;}
function get_term($id,$taxonomy){return new WP_Term;}
function is_wp_error($term){return false;}
function get_taxonomy($taxonomy){return (object)['object_type'=>['vacature','blog']];}
require ( $argv[1] ?? '/candidate.php' );
$checks=[];
function check($ok,$name){global $checks;if(!$ok)throw new Exception('FAIL: '.$name);$checks[]=$name;}
function setup_query($source='vacature'){
 global $object,$pod_exists,$fields,$relations,$probe_match;
 $object=new WP_Post;$pod_exists=true;$fields=['relationship_kennisgebied'=>[]];
 $relations=['relationship_kennisgebied'=>[['taxonomy'=>'kennisgebied','term_id'=>7]]];$probe_match=true;
 return new WP_Query(['post_type'=>$source,'post_status'=>'publish','posts_per_page'=>3,'paged'=>2,'offset'=>3,'orderby'=>'date','order'=>'DESC','post__not_in'=>[99],'meta_query'=>[['key'=>'visible','value'=>1]],'tax_query'=>[['taxonomy'=>'vac_status','field'=>'slug','terms'=>['ingevuld'],'operator'=>'NOT IN']]]);
}
$q=setup_query();$before=$q->query_vars;$hook($q);
check($q->get('tax_query')===['relation'=>'AND',$before['tax_query'],['relation'=>'AND',['taxonomy'=>'kennisgebied','field'=>'term_id','terms'=>[7],'operator'=>'IN','include_children'=>true]]],'matching vacancies retain exact AND selection and existing status exclusion');
check($last_probe['paged']===1&&$last_probe['offset']===0&&$last_probe['posts_per_page']===1&&$last_probe['no_found_rows']===true,'existence probe ignores pagination without counting all rows');
unset($before['tax_query']);$after=$q->query_vars;unset($after['tax_query']);check($after===$before,'display pagination, order, source, exclusions and meta query preserved');
$q=setup_query();$before=$q->query_vars;$probe_match=false;$hook($q);check($q->query_vars===$before,'zero matches fall back to exact original Elementor selection');
$q=setup_query();$before=$q->query_vars;$relations=[];$hook($q);check($q->query_vars===$before,'missing relationships fall back for vacancies');
$q=setup_query();$before=$q->query_vars;$fields=[];$hook($q);check($q->query_vars===$before,'missing fields fall back for vacancies');
$q=setup_query();$before=$q->query_vars;$pod_exists=false;$hook($q);check($q->query_vars===$before,'missing Pod falls back for vacancies');
$q=setup_query('blog');$probe_match=false;$hook($q);check($q->get('tax_query')['relation']==='AND','non-vacancy queries retain relationship restriction');
$q=setup_query('blog');$relations=[];$hook($q);check($q->get('post__in')===[0],'non-vacancy queries without relationships remain empty');
$q=setup_query(['vacature','blog']);$relations=[];$hook($q);check($q->get('post__in')===[0],'mixed sources are not broadened');
$q=setup_query();$q->set('_tfsc_relposts_probe',true);$before=$q->query_vars;$hook($q);check($q->query_vars===$before,'nested Elementor probe hook returns without recursion or mutation');
echo json_encode(['passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
