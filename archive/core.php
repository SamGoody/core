<?php

global $corey;
if(!$corey){
$corey = true;
require "parse.php";

function handleIncludes($content = 'parts.php'){
	$Cdom = new DOMParser($content, array('request'=>'include')); 
	$includes = $Cdom->get('include', array('src', 'parts')); 
	while($includes){
		foreach($includes as $include){
			$Tdom = new DOMParser(pick($include['src'], 'parts.php'), array('request'=>'include'));
			foreach($Cdom->get('include[src='.$include['src'].']>[id]', 'id') as $el) $Tdom->set('#'.$el['id'], $el['content']);
            $Cdom->set('include[src='.$include['src'].']', $Tdom->set(), 'outerHTML', 'queue');
		}
		$Cdom->set();
		$includes = $Cdom->get('include', array('src', 'parts'));
	}
	if(!$Cdom->get('head'));//add head;
	foreach((array)$Cdom -> get('[class^~=sr_]', array('properties'=>'class, id')) as $el){
		$cs = explode('sr_', $el['class']);
		array_shift($cs);
		foreach($cs as $c){
			$c = array_shift(explode(' ', $c));
			$Cdom->set('head', 'bottom', "
				<script type='text/javascript' src='siteroller/$c/$c.js'></script>
				<script type='text/javascript' src='siteroller/$c/scripts.js'></script> 
				<link rel='stylesheet' type='text/css' href='siteroller/$c/$c.css'/> 
			"); 
			if($c == "forms"){ 
				//$scripts = 'window.addEvent("domready", function() { console.log("dom is ready"); $("'.$el['id'].'").setStyle("background-color", "#e1e") } )';
				$scripts = 'new FormValidator($("testForm"));';
			}
		}; 
	}
	$Cdom->set('body', 'bottom', '<script>'.$scripts.'</script>');
	ob_start('ob_gzhandler');
	echo $Cdom->set(); 
	ob_end_flush();
}
handleIncludes(basename($_SERVER['PHP_SELF']));

//$includes = $Cdom->get('include', array('properties'=>'src, parts')); 
//debug($Cdom->get('include[src='.$include['src'].']>[id]', array('id')));die();
#foreach($Cdom->get('include[src='.$include['src'].']>[id]', array('properties'=>'id')) as $el) debug($el, 'without tdom');  
//$includes = $Cdom->get('include', array('properties'=>'src, parts'));
//debug($includes, 'includes');	
 #$Cdom->set('include[src='.$include['src'].']', 'outerHTML', 'queue');				
die;}

