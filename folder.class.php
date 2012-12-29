<?php 
/*** this class to operation folder on folder 
 *** include on file system
 *** @author misko lee
 *** @creared 2012-11-11
 ******************************************************/
 include_once 'filesystem.static.php';

class Folder{
	public function __construct($path){
		$path=FileSystem::check_path($path);
		$this->path=$path;
		$this->get_nodes();
		
	}
	public function get_size(){
		return FileSystem::get_folder_size($this->path);
	}
	public function size_format($model='auto'){
		$size=$this->size();
		switch($model){
			case 'auto':{
				$type='Byte';
				if($size>1024){
					$type='KB';
					$size/=1024;
					if($size>1024){
						$size/=1024;
						$type='MB';
						if($size>1024){
							$size/=1024;
							$type='GB';
						}
					}
				}
			
			$size.=$type;
			}break;
			case 'KB':{
				$size/=1024;
				$size.='KB';
			}break;
			case 'MB':{
				$size/=(1024*1024);
				$size.='MB';
			}break;
			case 'GB':{
				$size/=(1024*1024*1024);
				$size.='GB';
			}break;

		}
		return $size;
	}
	/*** u can rm file or folder ***/	
	public function rm_children($path){
		if(is_dir($path)){
			 FileSystem::rm_folder($this->path.$path);
		}else{
			FileSystem::rm_file($this->path.$path);
		}
	}
				
	public function add_folder($fileName){
		return FileSystem::create_folder($this->path,$fileName);
	}
	public function add_file($fileName){
		return FileSystem::create_file($this->path,$fileName);
	}
	public function __call($method,$vars){
		$methodType=substr($method,0,3);
		$var=substr($method,3,strlen($method)-1);
		switch($method){
			case 'get':{
				$this->__get($var);
			}break;
			case 'set':{
				$this->__set($var,$vars[0]);
			}break;
			
		}
	}
	public function  get_nodes(){
		$stream=opendir($this->path);
		while($file=readdir($stream)){
			if($file=='.' || $file=='..'){
			continue;
		}
			$fileInfo['name']=$file;
			$fileInfo['type']='file';
			if(is_dir($this->path.$file))
				$fileInfo['type']='folder';
			$this->nodes[]=$fileInfo;
	}
	return $this->nodes;	
}

public function get_permission(){
	return FileSystem::get_permission($this->path);

}
private $name;
private $path;
private $parent;
private $nodes;
private $files;
/** @param int value like linux ****/
private $permission;
}


?>
