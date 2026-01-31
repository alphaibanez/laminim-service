<?php

namespace Lkt\FileUpload\FileSystem;

interface FileSystem
{
    /**
     * Is it a file?
     */
    public function isFile(string $path): bool;

    /**
     * Is it a directory?
     */
    public function isDir(string $path): bool;

    /**
     * Is it a previously uploaded file?
     */
    public function isUploadedFile(string $path): bool;


    /**
     * Check if the file exists in a specified directory {@see __construct}
     */
    public function doesFileExist(string $path): bool;

    /**
     * Move file
     */
    public function moveUploadedFile(string $from_path, string $to_path): bool;

    /**
     * Write file or append to file
     */
    public function writeToFile(string $path, $stream, bool $append = false): bool;

    /**
     * Get file stream from PHP input
     * @return resource
     */
    public function getInputStream();

    /**
     * Get file stream from file
     * @return resource
     */
    public function getFileStream($path);

    /**
     * Delete path
     */
    public function unlink(string $path): bool;

    /**
     * Clear filesize cache on disk
     */
    public function clearStatCache(string $path): bool;

    /**
     * Get file size
     */
    public function getFilesize(string $path): int;
}
