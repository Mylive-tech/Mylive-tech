<?php 
	$dir = '../../../plugins/hmenu/temp_hero_files/';
	$it = new RecursiveDirectoryIterator($dir);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file){
		if($file->getFilename() === '.' || $file->getFilename() === '..'){
			continue;
		}
		if($file->isDir()){
			rmdir($file->getRealPath());
		}else{
			unlink($file->getRealPath());
		}
	}
	rmdir($dir);
?>