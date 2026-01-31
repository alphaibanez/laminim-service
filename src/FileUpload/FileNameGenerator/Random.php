<?php

namespace Lkt\FileUpload\FileNameGenerator;

use Lkt\FileUpload\FileSystem\FileSystem;
use Lkt\FileUpload\FileUpload;
use Lkt\FileUpload\PathResolver\PathResolver;
use Lkt\FileUpload\Util;

class Random implements FileNameGenerator
{

    /**
     * Maximum length of the filename
     */
    private int $name_length = 32;

    /**
     * Pathresolver
     * @var PathResolver
     */
    private $pathresolver;

    /**
     * Filesystem
     * @var FileSystem
     */
    private $filesystem;

    public function __construct(int $name_length = 32)
    {
        $this->name_length = $name_length;
    }

    /**
     * Get file_name
     * @param string $source_name
     * @param string $type
     * @param string $tmp_name
     * @param integer $index
     * @param string $content_range
     * @param  FileUpload $upload
     * @return string
     */
    public function getFileName(string $source_name, string $type, string $tmp_name, int $index, array $content_range, FileUpload $upload): string
    {
        $this->pathresolver = $upload->getPathResolver();
        $this->filesystem = $upload->getFileSystem();
        $extension = pathinfo($source_name, PATHINFO_EXTENSION);

        return ($this->getUniqueFilename($content_range, $extension));
    }

    /**
     * Get unique but consistent name
     * @param  string  $name
     * @param  string  $type
     * @param  integer $index
     * @param  array   $content_range
     * @param  string  $extension
     * @return string
     */
    protected function getUniqueFilename(array $content_range, string $extension): string
    {
        $name = $this->generateRandom() . "." . $extension;
        while ($this->filesystem->isDir($this->pathresolver->getUploadPath($name))) {
            $name = $this->generateRandom() . "." . $extension;
        }

        $uploaded_bytes = Util::fixIntegerOverflow(intval($content_range[1]));

        while ($this->filesystem->isFile($this->pathresolver->getUploadPath($name))) {
            if ($uploaded_bytes == $this->filesystem->getFilesize($this->pathresolver->getUploadPath($name))) {
                break;
            }

            $name = $this->generateRandom() . "." . $extension;
        }

        return $name;
    }

    protected function generateRandom(): string
    {
        return substr(
            str_shuffle(
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
            ),
            0,
            $this->name_length
        );
    }
}
