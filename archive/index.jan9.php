<?php
#copyright S. Goodman (www.siteroller.net)
#Licensed under the OSI ()


$file = $_SERVER['PATH_INFO']; 
$doc = $_SERVER['DOCUMENT_ROOT'];
$fullfile = $_SERVER['PATH_TRANSLATED'];

if($_GET['edit'] || $_GET['build'] || file_exists("$doc/siteroller/cache/flags/$file")){
	//echo __file__;
	//echo $fullfile;
	require "parse.php";
	handleIncludes($fullfile);
} else {
	if(file_exists("$doc/siteroller/cache/$file")) $fullfile = "$doc/siteroller/cache/$file";
	if(substr($file,-3) == 'php') include($fullfile); else echo file_get_contents($fullfile);//"$doc/$file"
}


function handleIncludes($content = 'index.html'){
	global $doc;	
	require 'classes.php';
	
	$Cdom = new DOMParser($content, array('request'=>'include')); 
	$includes = $Cdom->get('include', array('src', 'parts', 'id'));	
	
	session_start();  
	$_SESSION['cookie'];
	
	while($includes){
		foreach($includes as $include){
			//echo "\ne1\n"; debug($include['src']);
			$Tdom = new DOMParser(pick($doc.$include['src'], 'parts.php'), array('request'=>'include'));
			//debug($include, 'include');
			if($include['parts']){
				//debug('e2');
				
				foreach(explode(',',$include['parts']) as $part){
					$part = trim($part);
					$rp = $Tdom->get("#$part", 'outerHTML', 'string');
					//debug( $session = array( $include['src'] => array( $include['parts'][] = array())));
					//debug( $session = array( $include['src'] => array( $include['id'] => array( $include['parts']=> $part))));
					//{'template.htm':{'i109287':{'footer':['testInclude']}}} //if we only allow a page to be included in its entirety once, we do not need the id.  May be even not, am trying it.
					//debug($rp, "part: ".$part.++$somethin);
					$replaces = $replaces.$rp;
				}
				//debug($replaces, 'replace');
				$Tdom = new DOMParser($replaces);
				//debug("till here, all is dandy!");
				//debug($Tdom->set(), 'tdomSet');
			}
			$cookie[$include['src']][pick($include['parts'], 'html')][] = '#'.$include['id'];//adapt to allow for more than one instance calling the same parts.
			foreach($Cdom->get('include[src='.$include['src'].']>[id]', 'id') as $el){
				$Tdom->set('#'.$el['id'], $el['content']);
				$cookie[$include['src']][pick($include['parts'], 'html')][] = '#'.$el['id'];
				//debug( $cookie);
			}
			$Cdom->set('include[src='.$include['src'].']', $Tdom->set(), 'outerHTML', 'queue');
			if ($catch > 500){ echo "your loopy!"; break; }
		}
		$Cdom->set();
		$includes = $Cdom->get('include', array('src', 'parts'));
	}
	
	
	if($cookie){   $_SESSION['cookie'] = $cookie;}else echo "no cookie here";
	 //debug($_SESSION['cookie'], 'cook2');
	
	$scripts = array(); //array('php'=>'','css'=>'','head'=>'','foot'=>'');//debug($funcs['edit'], 'funcs[edit]');//debug($scripts, 'scripts, after first merge');
	if($_GET['edit']) $scripts =array_merge_recursive($scripts, $funcs['edit']);
	
	foreach((array)$Cdom -> get('[class^~=sr_]', array('class', 'id')) as $el){
		$cs = explode('sr_', $el['class']);
		array_shift($cs);
		foreach($cs as $c){
			$c = array_shift(explode(' ', $c));
			if(!$funcs[$c]) $funcs[$c] = array('php'=>"$c/index.php",'css'=>"$c/styles.css",'head'=>"",'foot'=>array("$c/scripts.js","$c/$c.js"));
			$scripts = array_merge_recursive($scripts, $funcs[$c]);
			$scriptname .=$c;
		}; 
	}
	
	
	
	foreach($scripts as $key=>$val)$scripts[$key]=array_unique((array)$val);//debug($scripts, 'scripts');
	foreach ((array)array_shift($scripts) as $ea) if($ea)include $doc."siteroller/classes/$ea";
	foreach($scripts as $fold=>$script){
		if($script){
			$fo = fopen($doc."siteroller/cache/$fold/$scriptname.txt", 'w');
				foreach ($scripts[$fold] as $ea) fwrite($fo, file_get_contents($doc."siteroller/classes/$ea"));
			fclose($fo);
		}
	}
	if(!$Cdom->get('head')); //add head if it doesn't exist;
	$Cdom->set('head', 'bottom', "
		<script type='text/javascript' src='siteroller/cache/head/$scriptname.txt'></script>
		<link rel='stylesheet' type='text/css' href='siteroller/cache/css/$scriptname.txt'/> 
	");
	$Cdom->set('body', 'bottom', "<script type='text/javascript' src='siteroller/cache/foot/$scriptname.txt'></script>");		
	
	//ob_start('ob_gzhandler');
	echo $Cdom->set(); 
	//ob_end_flush();
}





/* Cache rules:
	1. Build if build/edit var or build flag is set.
	2. Otherwise, if cached, give cached file.  If not, include php files, file_get_contents of static pages. 
	Include rules:
	1. Check if page includes include - requires loading pages, perhaps add check to parse.php.
	1. All includes are unfolded before dealing with other functions.
	2. Includes are "included", so all php is processed as they are being included.  code accordingly.
	3. All included parent tags should have "include:1" added to the tags. (So that page can be later edited in correct place)
	Function rules:
	1. If edit var is set, add sr_edit to footerscript array. 
	2. Check for sr_.  If not found, skip to step #5.
	3. Else, build appropriate indices. Than, for each - 
		a. Check function array for list of pages to include.  
		b. if not found, check for folder.  
		c. If found, include index.php, and add scripts.js and functionname.js to headscripts array.
	3. Add default functions: (edit, ?) to footscripts array.
	4. Check footscripts and headscripts for duplicates.
	5. Build headerscripts.js footerscript.js, put in matching cache location.
*/
/* ToDo:
	1. Set flag if edit is set to true.
	2. Remove flag upon building, unless appropriate variable is set.
	3. add built file to the cache
	4. Add check in parse.php to look skip all if no include or sr_ tags.
		a. Perhaps, adjust where the check for edit and where the inclusion of funcs.php &/or parse.php should be done
	5. Add head and relevant php if they do not exist
	6. Add compression (uncomment the lines).
*/
/* Junk:
$file = relation1($_SERVER['PATH_INFO']);  //echo $file."\n".__file__;
$funcs[$c] ? (foreach($funcs[$c] as $ea) $scripts[] = $ea) : if(file_exists("siteroller/classes/$c"))
$php[] =  
$headscripts[] = 
$footscripts[] = "siteroller/classes/$c".( $funcs[$c] ? $funcs[$c] : array("$c.js", "scripts.js"));
$css[] = 
if($c == "forms"){ 
$scripts = 'window.addEvent("domready", function() { console.log("dom is ready"); $("'.$el['id'].'").setStyle("background-color", "#e1e") } )';
	$scripts = 'new FormValidator($("testForm"));';
}
$fo = fopen("siteroller")
$scripts = fopen("cache/scripts/$file", 'wb');
*/	

//what i think is happening is that when it tries to include the index file to make the CDom, it triggers the server action, which includes the page and creates a loop.
?>