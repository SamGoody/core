<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$cache = "siteroller/cache/";

foreach(array('css', 'js') as $folder){
	$scripts = $_POST[$folder];
	if($scripts){
		$code = '';
		if(!is_array($scripts))$scripts = array($scripts);
		$file = $cache.$folder.'/'.md5(implode(sort($scripts))).'.'.$folder;
		foreach ($scripts as $script) 
			$code .= file_get_contents($root."siteroller/classes/$script");
		file_put_contents($root.$file,$code);
		$packed .= "$folder!/$file|";
	}
}
/*
$scripts = $_POST['style']; $folder='css';
if(!$scripts) $scripts=array("/mochaui/css/ui.css");

$code='';
$scripts = $_POST['script'];
$folder = 'js';
if(!$scripts) $scripts=array("/mochaui/scripts/source/Core/Core.js");
if($scripts){
	$file = $cache.$folder.'/'.md5(implode(array_sort($scripts))).'.'.$folder;
	foreach ($scripts as $script) 
		$code .= file_get_contents($root."siteroller/classes/$script");
	file_put_contents($root.$file,$code);
	$code = "$folder!/$file";
}
//$file="$cache/css/".;
//var_dump($_POST);
#$code = "<doctype><html><head>";
#$code .= "<style type='text/css'>#body{width:30px}";
#
#$code .= "</style>";
#$code .="</head><body></body></html>";
*/
/*
$code .= "<script type='text/javascript'>alert('hello world');";
foreach (array_values((array)$_POST['script']) as $script) 
	$code .= file_get_contents($root."siteroller/classes/$script");
$code .= "</script>";
*/
//$code = "js!/siteroller/cache/foot/test.txt|css!/siteroller/cache/css/test.css";
$packed = $code;	
//require_once 'class.JavaScriptPacker.php';
//$packer = new JavaScriptPacker($code, 'Normal', true, false);
//$packed = $packer->pack();



		
if(!$packed) $packed = var_dump($_POST);

// Output merged code
echo $packed;

?>