<?php

namespace Lkt\FileReader\Drivers;

use Lkt\FileReader\FileReaderException;


class DiskDriver implements FSDriverInterface
{

    /**
     * Checks if the given file exists.
     *
     * @param string $path the file path
     *
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Checks if the given $path is a file.
     *
     * @param string $path the file path
     *
     * @return bool
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Checks if the given $path is a directory.
     *
     * @param string $path the directory path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Returns the given file's contents
     *
     * @param string $path the file path
     *
     * @return string the file's contents
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function fileContents(string $path): string
    {

        if (!$this->isFile($path)) {
            throw new FileReaderException('File not found: ' . $path);
        }

        return file_get_contents($path);
    }

    /**
     * Requires the given source file and returns it's executed contents.
     * Caution! This is a potential security leak. You could even eval().
     *
     * @param string $path the file path
     *
     * @return mixed the file's executed contents
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function getRequire(string $path)
    {

        if (!$this->isFile($path)) {
            throw new FileReaderException('File not found: ' . $path);
        }

        return require $path;
    }

    /**
     * Creates a new directory under $path.
     *
     * @param string $path
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function makeDir(string $path): bool
    {

        if ($this->isDir($path)) {
            throw new FileReaderException('Directory already exists: ' . $path);
        }

        return mkdir($path);
    }

    /**
     * Deletes directory $path (if empty)
     *
     * @param string $path
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function deleteDir(string $path): bool
    {

        if (!$this->isDir($path)) {
            throw new FileReaderException('Directory not found: ' . $path);
        }

        return rmdir($path);
    }

    /**
     * Deletes a file
     *
     * @param string $path
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function deleteFile(string $path): bool
    {

        if (!$this->isFile($path)) {
            throw new FileReaderException('File not found: ' . $path);
        }

        return unlink($path);
    }

    /**
     * Renames a file or directory from $oldname to $newname
     *
     * @param string $oldname
     * @param string $newname
     * @param bool $overwrite
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function rename(string $oldname, string $newname, bool $overwrite = true): bool
    {

        if (!$overwrite && ($this->fileExists($newname) || $this->isDir($newname))) {
            throw new FileReaderException('Destination already exists.');
        }

        return rename($oldname, $newname);
    }

    /**
     * Copies a file from $source to $dest
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function copyFile(string $source, string $destination, bool $overwrite = true): bool
    {

        if (!$overwrite && $this->fileExists($destination)) {
            throw new FileReaderException('Destination file already exists.');
        }

        return copy($source, $destination);
    }

    /**
     * @param string $path
     * @param string $data
     * @param bool $overwrite
     *
     * @return int|bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function write(string $path, string $data, bool $overwrite = true)
    {

        if (!$overwrite && $this->fileExists($path)) {
            throw new FileReaderException('Destination file already exists.');
        }

        return file_put_contents($path, $data);
    }

    /**
     * @param string $path
     *
     * @return int|null
     */
    public function fileModifyTime(string $path): ?int
    {
        return filemtime($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws \Lkt\FileReader\FileReaderException
     */
    public function isWritable(string $path): bool
    {

        if (!$this->isDir($path)) {
            throw new FileReaderException('Directory not found: ' . $path);
        }

        return is_writable($path);
    }

    /**
     * @param string $pattern
     *
     * @return array
     */
    public function findFiles(string $pattern): array
    {
        return glob($pattern);
    }

}
