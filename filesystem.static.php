<?php
/******************************************************************************
 ============================================================================== 
 *** this is a base funcs of native filesystem 
 *** every func is static , so the class name only is a namespace
 *** @author misko_lee
 *** @created 2012-11-11
 *** @email misko_lee@hotmail.com 
 ==============================================================================
 ******************************************************************************/ 
class FileSystemException extends Exception{
}
	
abstract class FileSystem{
	/*** Error Code of file **/
	public static const FILE_OPEN_ERROR=1;
	public static const FILE_READ_ERROR=2;
	public static const FILE_EXECUTE_ERROR=3;
	public static const FILE_WRITE_ERROR=4;
	public static const FILE_MOVE_ERROR=5;
	public static const FILE_RM_ERROR=6;
	public static const FILE_RENMAE_ERROR=7;
	public static const FILE_CHREATE_ERROR=8;
	/** Error Code of folder **/	
	public static const FOLDER_OPEN_ERROR=11;
	public static const FOLDER_READ_ERROR=12;
	public static const FOLDER_WRITE_ERROR=13;
	public static const FOLDER_MOVE_ERROR=14;
	public static const FOLDER_RM_ERROR=15;
	public static const FOLDER_RENMAE_ERROR=16;
	public static const FOLDER_CREATE_ERROR=17;
	/** Error Code of disk **/
	public static const DISK_SPACE_WARNNING=41;		
	public static const DISK_SPACE_OVERFLOW=42;
	
	
	public static function get_file_fix($fileName){
		if(strpos('.',$fileName)){
			return array_pop(explode('.',$fileName));
		}
		return null;
	}
											
	public static function file_type($fileName){
		/** we need add more fixs ****/
		$fixs['text']=array('txt','conf','ini');
		$fixs['code']=array('php','c','cpp','java','html','htm','css','js','perl','py','sh');
		$fixs['media']=array('mp3','wma','wav','flac','flash','mp4','rm','rmvb','avi');
		$fixs['office']=array('doc','xls','pdf');
		$fileFix=self::get_file_fix($fileName);
		foreach($fixs as $type=>$v){
			foreach($v as $k=>$fix){
				if($fileFix==$fix){
					return $type;
				}
			}
		}
		
		return 'unknowType';	
	}
	/*** debug fs ***/
	public static function simply_exception($code){
			throw new FileSystemException('filesystem error code :'.$code);
	}			
	public static function get_file_size($path){
		return filesize($path);
	}
	/** @param string $path  the varribute must a folder path,
	 ** if the path is file ,please use method get_file_size 
	 ** @return int return a folder size of byte**/
	public static function get_folder_size($path){
		if(strrpos('/',$path)<strlen($path)-1)
				$path=$path.'/';
		static $count;
		$files=opendir($path);
		while($file=readdir($files)){
			if(is_dir($path.$file)){
		if($file=='.' || $file=='..'){
			continue;					
		}
				self::get_folder_size($path.$file);
			}else{
			$count+=filesize($path.$file);
			}
		}
	return $count;
	}
	/*** system functions alias 
	 *** sometime i will fix this code, add permission****/
	public static function is_read($path){
		return is_readable($path);
	}
	public static function is_write($path){
		return is_writeable($path);
	}
	public static function is_execute($path){
		return is_executable($path);
	}
	public static function is_link($path){
		return is_link($path);
	}
	public static function is_open($path){
		if(file_exists($path) && self::is_read($path))
			return true;
		throw new FileSystemException(self::FILE_OPEN_ERROR);						
	}
	/*** the permission value like linux *****/
	public static function get_permission($path){
		$permission=null;
		self::is_read($path)?$permission+=4:null;
		self::is_write($path)?$permission+=2:null;
		self::is_execute($path)?$permission+=1:null;
		return $permission;	
	}
	/*** overview sys funcs ***/	
	public static function move_folder($oldPath,$newPath){
			
			if(rename($oldPath,$newPath)){return true;}
			else{
			self::simply_exception(self::FOLDER_MOVE_ERROR);
		}
	}
	public static function copy_folder($oldPath,$newPath){
			self::is_open($oldPath);
			if(copy($oldPath,$newPath)){return true;}else{
				self::simply_exception(self::FOLDER_COPY_ERROR);
			}

	}
	/*** rm everything of the path ****/
	public static function rm_folder($path){
			self::is_open($path);
		if(!is_dir($path))
			self::simply_exception(self::FOLDER_RM_ERROR);	
		$stream=opendir($path);
		while($file=readdir($stream)){
			if($file=='.' || $file=='..' || !self::is_write($path.$file)){
			echo $file;
				continue;
			}
			if(is_dir($path.$file)){
				self::rm_folder($path.$file.'/');
			}else{
				self::rm_file($file);
			}
		}
		
	}
	
	public static function move_file($oldPath,$newPath){
		self::is_open($oldPath);
		if(rename($oldPath,$newPath)){return true;}else{
			self::simply_exception(self::FILE_MOVE_ERROR);
		}
	}
	public static function copy_file($oldPath,$newPath){
			self::is_open($oldPath);	
			if(copy($oldPath,$newPath)){return true;}else{
			self::simply_exception(self::FILE_COPY_ERROR);
		}
	}
	public static function rm_file($path){
		self::is_open($path);
		if(unlink($path)){return true;}else{
			self::simply_exception(self::FILE_RM_ERROR);
		}
	}
	public static function rm_file_of_fix($path,$fix){
		self::is_open($path);
		$file=basename($path);
		$fileFix=self::get_file_fix($file);
		if($fileFix==$fix)
			return self::rm_file($path);
		return false;
		
	}
	public static function rm_file_of_match($path,$parttern){
		self::is_open($path) && self::is_write($path);
		$file=basename($path);
		if(preg_match($perttern,$path)) 
			return self::rm_file($parttern);
		return false;	

		}
	public static function create_folder($parent,$name){
		$parent=self::check_path($parent);
		self::is_open($parent)		
		if(!is_write($parent))	return false;
		if(file_exists($parent.$name))return false;
		mkdir($parent.$name);		

	}
	public static function create_file($path,$fileName){
		$path=self::check_path($path);
		self::is_open($path)		
		if(!is_dir($path)) return false;
		if(file_exists($path.$fileName))return false;
		$tempFile=tempnam($path,'');
		self::move_file($tempFile,$path.$fileName);
		self::rm_file($tempFile);
						
	}	
	public static function check_path($path){
		if(strrpos('/',$path)<strlen($path)-1)
			return $path=$path.'/';
		return $path;
	}

}



?>
