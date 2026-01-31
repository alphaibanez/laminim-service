<?php

namespace Lkt\FileReader;

use Lkt\FileReader\Drivers\FSDriverInterface;

class Directory extends FSAbstract{

	/**
	 * Directory constructor.
	 *
	 * @param \Lkt\FileReader\Drivers\FSDriverInterface $driver
	 * @param string                                           $path
	 */
	public function __construct(FSDriverInterface $driver, string $path){
		parent::__construct($driver);

		$this->path = $path;
	}

	/**
	 * @param string $path
	 *
	 * @return \Lkt\FileReader\Directory
	 */
	public function change(string $path):Directory{
		$this->path = $path;

		return $this;
	}

	/**
	 * Reads a directory and returns the contents as an array of \stdClass
	 *
	 * @return array
	 * @throws \Lkt\FileReader\FileReaderException
	 */
	public function read():array{

		if(!$this->filereader->isDir($this->path)){
			throw new FileReaderException('Directory not found: '.$this->path);
		}

		$dir = scandir($this->path);

		$filelist = [];

		if(is_array($dir)){

			foreach($dir as $file){

				if(in_array($file, ['.', '..'], true)){
					continue;
				}

				$filelist[] = new File($this->filereader, $this, $file);
			}
		}

		return $filelist;
	}

	/**
	 * @param string|null $subdir
	 *
	 * @return bool
	 */
	public function create(string $subdir = null):bool{

		if($subdir && $this->filereader->isDir($this->path)){
			return $this->filereader->makeDir($this->path.DIRECTORY_SEPARATOR.$subdir);
		}

		return $this->filereader->makeDir($this->path);
	}

	/**
	 * @param string|null $subdir
	 *
	 * @return bool
	 */
	public function delete(string $subdir = null):bool{

		if($subdir && $this->filereader->isDir($this->path)){
			return $this->filereader->deleteDir($this->path.DIRECTORY_SEPARATOR.$subdir);
		}

		return $this->filereader->deleteDir($this->path);
	}

	/**
	 * @param string $newname
	 * @param bool   $overwrite
	 *
	 * @return \Lkt\FileReader\Directory
	 * @throws \Lkt\FileReader\FileReaderException
	 */
	public function rename(string $newname, bool $overwrite = true):Directory{

		if(!$this->filereader->rename($this->path, $newname, $overwrite)){
			throw new FileReaderException('cannot rename '.$this->path.' to '.$newname); // @codeCoverageIgnore
		}

		return $this->change($newname);
	}

}
