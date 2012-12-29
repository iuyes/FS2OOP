<?php
	include 'folder.class.php';
$folder=new Folder('/home/miskolee/oop/fs/test');
//var_dump($folder->get_nodes());		 
//$folder->add_folder('folder1');
//$folder->add_file('test2.php');
$folder->rm_children('test2.php');			
  // echo tempnam('/home/miskolee/oop/fs/test/','php'); 
?>
