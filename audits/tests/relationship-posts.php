<?php
error_reporting(E_ALL);
set_error_handler(function($level,$message){throw new Exception($message);});
class WP_Term {public $term_id=1;}
class WP_Post {public $ID=100;}
function evaluate_tax($query,$record){
 if(!$query)return true;
 if(isset($query['taxonomy'])){
  $actual=$record[$query['taxonomy']]??[];$terms=$query['terms'];$overlap=array_intersect($actual,$terms);
  return ($query['operator']??'IN')==='NOT IN'?!$overlap:count($overlap)>0;
 }
 $results=[];foreach($query as $key=>$clause){if($key==='relation')continue;$results[]=evaluate_tax($clause,$record);}
 return ($query['relation']??'AND')==='OR'?in_array(true,$results,true):!in_array(false,$results,true);
}
class WP_Query {
 public $query_vars;public $posts=[];
 function __construct($args=[]){
  global $records,$last_probe,$hook;
  $this->query_vars=$args;
  if(!empty($args['_tfsc_relposts_probe'])){$last_probe=$args;$hook($this);}
  foreach($records as $id=>$record){if(evaluate_tax($args['tax_query']??[],$record))$this->posts[]=$id;}
 }
 function get($key){return $this->query_vars[$key]??null;}
 function set($key,$value){$this->query_vars[$key]=$value;}
 function have_posts(){return(bool)$this->posts;}
}
class TestPod {function exists(){return true;}function fields(){global $relations;return array_fill_keys(array_keys($relations),[]);}function field($key){global $relations;return $relations[$key]??[];}}
function add_action($name,$callback,$priority,$args){global $hook;$hook=$callback;}
function get_queried_object(){return new WP_Post;}
function get_post_type($id){return 'beroepengids';}
function pods($type,$id){global $pod_exists;return $pod_exists?new TestPod:false;}
function sanitize_key($key){return $key;}
function taxonomy_exists($taxonomy){return true;}
function get_term($id,$taxonomy){$t=new WP_Term;$t->term_id=$id;return $t;}
function is_wp_error($term){return false;}
function get_taxonomy($taxonomy){return(object)['object_type'=>$taxonomy==='blog_only'?['blog']:['vacature','blog']];}
require($argv[1]??'/candidate.php');
$checks=[];
function check($ok,$name){global $checks;if(!$ok)throw new Exception('FAIL: '.$name);$checks[]=$name;}
function setup($groups,$source='vacature',$native=true){
 global $relations,$records,$pod_exists;$pod_exists=true;$relations=[];
 foreach($groups as $tax=>$ids){foreach($ids as $id)$relations['relationship_'.$tax][]=['taxonomy'=>$tax,'term_id'=>$id];}
 $records=[
  1=>['vac_status'=>['open'],'knowledge'=>[7],'jobname'=>[10],'sector'=>[20],'machine_family'=>[42]],
  2=>['vac_status'=>['open'],'knowledge'=>[7],'jobname'=>[11],'sector'=>[20]],
  3=>['vac_status'=>['open'],'knowledge'=>[8],'jobname'=>[10],'sector'=>[21]],
  4=>['vac_status'=>['ingevuld'],'knowledge'=>[7],'jobname'=>[10],'sector'=>[20]],
  5=>['vac_status'=>['open'],'knowledge'=>[8],'jobname'=>[11],'sector'=>[20]],
 ];
 $args=['post_type'=>$source,'post_status'=>'publish','posts_per_page'=>3,'paged'=>2,'offset'=>3,'orderby'=>'date','order'=>'DESC','post__not_in'=>[99],'meta_query'=>[['key'=>'visible','value'=>1]]];
 if($native)$args['tax_query']=[['taxonomy'=>'vac_status','field'=>'slug','terms'=>['ingevuld'],'operator'=>'NOT IN']];
 return new WP_Query($args);
}
function results($query){$actual=new WP_Query($query->query_vars);return $actual->posts;}
$q=setup(['knowledge'=>[7],'jobname'=>[10]]);$before=$q->query_vars;$hook($q);check(results($q)===[1],'full combination has priority; filled vacancy excluded');
check($last_probe['paged']===1&&$last_probe['offset']===0&&$last_probe['posts_per_page']===1&&$last_probe['no_found_rows']===true,'probes check existence without display pagination or totals');
$after=$q->query_vars;unset($after['tax_query'],$before['tax_query']);check($after===$before,'display source, pagination, order, exclusion and metadata filters unchanged');
$q=setup(['knowledge'=>[7],'jobname'=>[999]]);$hook($q);check(results($q)===[1,2],'empty job-name relation falls back to populated knowledge');
$q=setup(['knowledge'=>[999],'jobname'=>[10]]);$hook($q);check(results($q)===[1,3],'empty knowledge relation falls back to populated job name without priority by taxonomy');
$q=setup(['knowledge'=>[7],'jobname'=>[999],'sector'=>[20]]);$hook($q);check(results($q)===[1,2],'remaining populated relations keep their AND combination when possible');
$q=setup(['knowledge'=>[8],'machine_family'=>[42]]);$hook($q);check(results($q)===[1,3,5],'conflicting populated relations use OR under original status exclusion');
$q=setup(['knowledge'=>[999],'jobname'=>[999]]);$before=$q->query_vars;$hook($q);check($q->query_vars===$before&&results($q)===[1,2,3,5],'no matching relation falls back to all originally eligible vacancies');
$q=setup([]);$before=$q->query_vars;$hook($q);check($q->query_vars===$before,'no relationships retain original vacancy selection');
$q=setup(['machine_family'=>[42]]);$hook($q);check(results($q)===[1],'new relationship field and arbitrary taxonomy work without a code change');
$q=setup(['knowledge'=>[7,8],'jobname'=>[10]]);$hook($q);check(results($q)===[1,3],'multiple terms within a taxonomy retain IN behavior');
$q=setup(['blog_only'=>[1]]);$before=$q->query_vars;$hook($q);check($q->query_vars===$before,'taxonomy not attached to vacancy source is ignored');
$q=setup(['knowledge'=>[7],'jobname'=>[999]],'blog');$hook($q);check(results($q)===[],'non-vacancy source retains strict relation selection');
$q=setup([],'blog');$hook($q);check($q->get('post__in')===[0],'non-vacancy source without relationships remains empty');
$q=setup([],['vacature','blog']);$hook($q);check($q->get('post__in')===[0],'mixed source is never broadened');
$q=setup(['knowledge'=>[7]]);$before=$q->query_vars;$pod_exists=false;$hook($q);check($q->query_vars===$before,'missing Pod keeps general vacancy selection');
$q=setup(['knowledge'=>[7],'jobname'=>[999]],'vacature',false);$hook($q);check(results($q)===[1,2,4],'individual probes are valid tax queries even without pre-existing Elementor tax filters');
$q=setup(['knowledge'=>[7]]);$q->set('_tfsc_relposts_probe',true);$before=$q->query_vars;$hook($q);check($q->query_vars===$before,'nested Elementor probe hook returns without recursion');
echo json_encode(['passed'=>count($checks),'checks'=>$checks],JSON_PRETTY_PRINT);
