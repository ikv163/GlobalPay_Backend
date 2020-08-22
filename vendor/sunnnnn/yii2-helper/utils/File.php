<?php
namespace sunnnnn\helper\utils;

use Yii;
/**
 * 
* @use: 文件相关处理类
* @date: 2017-4-21 上午10:31:21
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class File {
	/**
	 * 获取某个目录下所有文件
	* @date: 2017-1-22 上午9:59:43
	* @author: sunnnnn
	* @param unknown $path 文件路径 \Yii::getAlias('@app')
	* @param string $child 是否包含对应的目录
	* @return NULL|multitype:string
	 */
	public function getFiles($path, $child = false){
		$files=array();
		if(!$child){
			if(is_dir($path)){
				$dp = dir($path);
			}else{
				return null;
			}
			while ($file = $dp->read()){
				if($file != "." && $file != ".." && is_file($path.$file)){
					$files[] = $file;
				}
			}
			$dp->close();
		}else{
			$this->scanFiles($files, $path);
		}
		return $files;
	}
	
	/**
	 * 
	* @date: 2017-1-22 上午10:00:21
	* @author: sunnnnn
	* @param unknown $files 结果
	* @param unknown $path 路径
	* @param string $childDir 子目录名称
	 */
	public function scanFiles(&$files, $path, $childDir = false){
		$dp = dir($path);
		while ($file = $dp->read()){
			if($file != "." && $file != ".."){
				if(is_file($path.$file)){//当前为文件
					$files[] = $file;
				}else{//当前为目录
				    $this->scanFiles($files[$file], $path.$file.DIRECTORY_SEPARATOR, $file);
				}
			}
		}
		$dp->close();
	}
	
	/**
	 * 创建目录
	* @date: 2017-2-7 下午3:07:28
	* @author: sunnnnn
	* @param unknown $path 路径
	 */
	public function createFolder($path)  {  
		if (!file_exists($path))  {  
		    $this->createFolder(dirname($path));  
            mkdir($path, 0777, true);  
		}  
	}
	
	/**
	 * 删除目录
	* @date: 2017-2-7 下午3:08:55
	* @author: sunnnnn
	* @param unknown $path 路径
	* @return boolean
	 */
	public function deleteFolder($path){
		if (!is_dir($path)) return false;
		 
		$path = rtrim($path,'/').'/';
		$dir_obj = opendir($path);
	
		while ($dir = readdir($dir_obj)){
			if ($dir != '.' && $dir != '..'){
				$file = $path.$dir;
				if (is_dir($file)){
				    $this->deleteDir($file);
				}elseif (is_file($file)){
					unlink($file);
				}
			}
		}
		closedir($dir_obj);
		rmdir($path);
	}
	
	/**
	 * 创建文件
	* @date: 2017-2-7 下午3:10:09
	* @author: sunnnnn
	* @param unknown $file 文件完整路径
	* @param string $content 内容
	 */
	public function createFile($file, $content=''){
		$folder = dirname($file);
		if (!is_dir($folder)){
		    $this->createFolder($folder);
		}
		 
		file_put_contents($file,$content);
	}
	
	/**
	 * 删除文件
	* @date: 2017-2-7 下午3:11:22
	* @author: sunnnnn
	* @param unknown $file 文件完整路径 Yii::getAlias('@backend')
	 */
	public function deleteFile($file){
		if (file_exists($file)){
			@unlink($file);
		}
	}
	
	/**
	 * 
	* @date: 2017-4-21 上午10:31:50
	* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
	* @param unknown $file 文件
	* @param unknown $path 存放路径
	* @param string $base_name 是否使用原文件名
	* @throws \Exception 
	* @return string
	 */
	public function upload($file, $path, $base_name = true, $detail = false){
		if(empty($file)) throw new \Exception('文件不存在');
		
		$save_path = './uploads/'.trim($path, '/').'/';
		if (!file_exists($save_path)){
		    $this->createFolder($save_path);
		}
		
		$fileName = empty($base_name) ? date('Ymd').'-'.uniqid().'.'.$file->extension : $file->baseName.'.'.$file->extension;
		$success = $file->saveAs($save_path.$fileName);
		if($success){
		    if($detail === true){
		        return [
		            'path' => ltrim($save_path, '.').$fileName,
		            'name' => $file->baseName,
		            'extension' => $file->extension,
		            'size' => $file->size,
		            'type' => $file->type,
		        ];
		    }else{
		        return ltrim($save_path, '.').$fileName;
		    }
		}
		throw new \Exception('文件上传失败');
	}
	
}