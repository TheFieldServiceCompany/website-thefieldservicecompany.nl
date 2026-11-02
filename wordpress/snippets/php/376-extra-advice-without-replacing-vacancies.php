<?php

/**
 * TFSC — advies als extra blok in het vacatureoverzicht (v1.0).
 *
 * HOE WERKT DIT?
 * - Alleen de vacature-Loop Grid 44137129 (template 25495).
 * - Elementor Alternate Template staat UIT; de query houdt tien vacatures.
 * - Na de vijfde vacature voegen we het bestaande adviesontwerp 20568 toe.
 * - Daardoor blijven alle vacatures en de normale pagina-aantallen behouden.
 * - Geen extra blok bij minder dan zes vacatures; geen formulierverzendingen.
 * - Het adviesontwerp blijft gewoon in Elementor te bewerken.
 * - Blog, Beroepengids en Kennisbank worden niet aangepast.
 *
 * Terugdraaien: deactiveer deze snippet. Laat Alternate Template uit om geen
 * vacature te vervangen. Native Static item position is niet gebruikt: in de
 * praktijktest ontbrak daarmee een vacature op de vervolgpagina's.
 */
add_filter('elementor/widget/render_content', function($html,$widget){
 if($widget->get_id()!=='44137129' || $widget->get_name()!=='loop-grid' || !class_exists('DOMDocument'))return $html;
 $dom=new DOMDocument();$old=libxml_use_internal_errors(true);
 $dom->loadHTML('<?xml encoding="UTF-8"><div id="tfsc-extra-advice-root">'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);
 libxml_clear_errors();libxml_use_internal_errors($old);$xp=new DOMXPath($dom);
 $grid=$xp->query('//*[contains(concat(" ",normalize-space(@class)," ")," elementor-loop-container ")]')->item(0);
 if(!$grid)return $html;
 $cards=[];foreach($grid->childNodes as $node){if($node instanceof DOMElement && strpos(' '.$node->getAttribute('class').' ',' e-loop-item ')!==false)$cards[]=$node;}
 if(count($cards)<6 || strpos($html,'tfsc-extra-advice')!==false)return $html;
 $ad=Elementor\Plugin::$instance->frontend->get_builder_content_for_display(20568,true);
 if(!$ad)return $html;
 $adDom=new DOMDocument();$old=libxml_use_internal_errors(true);$adDom->loadHTML('<?xml encoding="UTF-8"><div id="tfsc-ad-root">'.$ad.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($old);
 $ax=new DOMXPath($adDom);$root=$ax->query('//*[@id="tfsc-ad-root"]')->item(0);if(!$root)return $html;
 foreach(iterator_to_array($root->childNodes) as $node){if($node instanceof DOMElement && $node->hasAttribute('data-elementor-id'))$node->setAttribute('class','elementor elementor-20568 tfsc-extra-advice');$grid->insertBefore($dom->importNode($node,true),$cards[5]);}
 $out='';foreach($xp->query('//*[@id="tfsc-extra-advice-root"]')->item(0)->childNodes as $node)$out.=$dom->saveHTML($node);
 return $out;
},20,2);
