<?php
# this works: view-source:http://localhost/siteRoller/wysiwyg/update.php?page=../faq4.php&id=else&content=why%20and%20why%20not
require_once "parse.php";
extract($_POST);
if($page == "/") $page = "/index.php";
$page = relation($page); //can give root relative path, in this case: "/f1/content/shalomtravel" or "/nfsn/content/shalomtravel";
$content = stripslashes($content);
$dom = new DOMParser($page);

if(!$dom->get("#$id")){ $new = true; $content = "\n<p id=\"$id\">$content</p>\n"; debug($id, "new element, eh?");}//debug($content, 'content');
$handle = fopen($page, "wb");
fwrite($handle, (!$new ? $dom->set("#$id", $content) : $dom->set(":root < :first-child", $content, 'after')));
fclose($handle);
echo "OK\n";
echo $content;

?>