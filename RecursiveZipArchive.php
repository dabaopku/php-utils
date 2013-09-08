<?php

class RecursiveZipArchive extends ZipArchive
{
	public function addDir($path, $name, $ignore=array())
	{
		if(in_array(basename($path), $ignore))
			continue;

		$this->addEmptyDir($name);
		
		$this->addDirContent($path, $name, $ignore);
	}
	
	private function addDirContent($path, $name, $ignore)
	{
		$name.=DIRECTORY_SEPARATOR;
		$path.=DIRECTORY_SEPARATOR;
		
		$dir=opendir($path);
		
		while($file=readdir($dir))
		{			
			if(in_array($file, array('.','..')) || in_array($file, $ignore))
				continue;
			
			if(filetype($path.$file)=='dir')
			{
				$this->addDir($path.$file, $name.$file, $ignore);
			}
			else 
			{
				$this->addFile($path.$file, $name.$file);
			}
		}
		
		closedir($dir);
	}
}