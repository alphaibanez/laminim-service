<?php

namespace Lkt\FileUpload\FileSystem;

class Simple implements FileSystem
{
    /**
     * @see FileSystem
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * @see FileSystem
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @see FileSystem
     */
    public function isUploadedFile(string $path): bool
    {
        return is_uploaded_file($path);
    }

    /**
     * {@inheritdoc}
     */
    public function doesFileExist(string $path): bool
    {
        return file_exists($path) ? true : false;
    }

    /**
     * @see FileSystem
     */
    public function moveUploadedFile(string $from_path, string $to_path): bool
    {
        return copy($from_path, $to_path) && unlink($from_path);
    }

    /**
     * @see FileSystem
     */
    public function writeToFile(string $path, $stream, bool $append = false): bool
    {
        return file_put_contents($path, $stream, $append ? \FILE_APPEND : 0);
    }

    /**
     * @see FileSystem
     */
    public function getInputStream()
    {
        return fopen('php://input', 'r');
    }

    /**
     * @see FileSystem
     */
    public function getFileStream($path)
    {
        return fopen($path, 'r');
    }

    /**
     * @see FileSystem
     */
    public function unlink(string $path): bool
    {
        return unlink($path);
    }

    /**
     * @see FileSystem
     */
    public function clearStatCache(string $path): bool
    {
        clearstatcache(true, $path);
        return true;
    }

    /**
     * @see FileSystem
     */
    public function getFilesize(string $path): int
    {
        return filesize($path);
    }
}
