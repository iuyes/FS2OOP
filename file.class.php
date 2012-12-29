<?php 
/****** this is a file class for the file system 
 ****** this fileSystem is a abstract view to operation file
 ****** u can do more thing 
 ****** sometime i will fix this file 
 ****** @author misko_lee
 ****** @created 2012-11-11 
 *************************************************************/
require_once 'Folder.class.php';
class File{
	public funcion __construct($path){
		/** base must set **/
		$this->dir=dirname($path);
		$this->dir=FileSystem::check_path($this->parent);
		$this->parent=new Folder($this->dir);
		$this->name=basename($path);

	}
	/*** the param must a path ****/	
	public function move($newPath){
 				
		return FileSystem::move_file($this->name,$newPath);
		}
	/*** if the var has path,default elide path,only
	 *** use file name. 
	 *** so u can'nt move the file to other folder 
         ********************************************/
	public function rename($newName){
		$newName=basename($newName);
		return rename($this->name,$new);
	}
	public function open_to_string(){
		return file_get_contents($this->dir.$this->name);	
	}
	public function get_parent_folder(){
		return  $this->parent;
	}
	public function get_size(){
		return $this->size;

	}
	public function del(){
		$this->parent->rm_children($this->name);	
	}
	public function __get($key){
		if($key=='dir')
			return $this->dir->__toString();
		return $this->$key;							
	}

/** @param string ***/
protected $name;
/** @param int constrcut **/
protected $type;
/** @param int byte***/
protected $size;
/** @param int **/
protected $permission;
/** the file path @param Folder class **/
protected $dir;
protected $parent;
protected $path;
}


?>
