<?php

namespace Lkt\FileUpload\FileNameGenerator;

use Lkt\FileUpload\FileSystem\FileSystem;
use Lkt\FileUpload\FileUpload;
use Lkt\FileUpload\PathResolver\PathResolver;
use Lkt\FileUpload\Util;

class Simple implements FileNameGenerator
{

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
        $this->filesystem = $upload->getFileSystem();
        $this->pathresolver = $upload->getPathResolver();

        return ($this->getUniqueFilename($source_name, $content_range));
    }

    /**
     * Get unique but consistent name
     * @param  string  $name
     * @param  string  $type
     * @param  integer $index
     * @param  array   $content_range
     * @return string
     */
    protected function getUniqueFilename(string $name, array $content_range): string
    {
        if (! is_array($content_range)) {
            $content_range = [0];
        }

        while ($this->filesystem->isDir($this->pathresolver->getUploadPath($name))) {
            $name = $this->pathresolver->upcountName($name);
        }

        $uploaded_bytes = Util::fixIntegerOverflow(intval($content_range[1] ?? $content_range[0]));

        while ($this->filesystem->isFile($this->pathresolver->getUploadPath($name))) {
            if ($uploaded_bytes == $this->filesystem->getFilesize($this->pathresolver->getUploadPath($name))) {
                break;
            }

            $name = $this->pathresolver->upcountName($name);
        }

        return $name;
    }
}
