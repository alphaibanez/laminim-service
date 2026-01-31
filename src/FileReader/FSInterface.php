<?php
/
namespace Lkt\FileReader;

interface FSInterface{

	public function __get(string $name);

	public function info():array;

	/**
	 * @return \Lkt\FileReader\Directory|\Lkt\FileReader\File
	 */
	public function rename(string $newname, bool $overwrite = true);

}
