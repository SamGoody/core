<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$cache = 'siteroller/cache/';
if($compress = false) include 'jsPacker.php';

foreach(array('css', 'js') as $folder){
	if($scripts = (array)$_POST[$folder]){
		$code=''; 
		$file = "$cache$folder/".md5(strtolower(implode($scripts))).".$folder";
		$paths .= ($paths ? '|' : '')."$folder!/$file";
		if(file_exists($root.$file)) continue;
		foreach($scripts as $script) $code .= file_get_contents($root."siteroller/classes/$script");
		if($compress){
			$packer = new JavaScriptPacker($code, 'Normal', true, false);
			$pack = $packer->pack();
			file_put_contents($root.$file, $pack);
		} else file_put_contents($root.$file, $code);
	}
}
echo $paths;

//sort($scripts); Removed since if the files are loaded in a different order, you want to rebuild the js file
?>