<?php

namespace Lkt\FileUpload\FileNameGenerator;

use Lkt\FileUpload\FileSystem\FileSystem;
use Lkt\FileUpload\FileUpload;
use Lkt\FileUpload\PathResolver\PathResolver;
use Lkt\FileUpload\Util;

class Slug implements FileNameGenerator
{
    private PathResolver $pathResolver;
    private FileSystem $filesystem;

    /**
     * Get file_name
     */
    public function getFileName(string $source_name, string $type, string $tmp_name, int $index, array $content_range, FileUpload $upload): string
    {
        $this->filesystem = $upload->getFileSystem();
        $this->pathResolver = $upload->getPathResolver();

        $source_name = $this->getSluggedFileName($source_name);
        $uniqueFileName = $this->getUniqueFilename($source_name, $content_range);

        return $this->getSluggedFileName($uniqueFileName);
    }

    /**
     * Get unique but consistent name
     */
    protected function getUniqueFilename(string $name, array $content_range): string
    {
        if (! is_array($content_range)) {
            $content_range = [0];
        }

        while ($this->filesystem->isDir($this->pathResolver->getUploadPath($this->getSluggedFileName($name)))) {
            $name = $this->pathResolver->upcountName($name);
        }

        $uploaded_bytes = Util::fixIntegerOverflow(intval($content_range[1] ?? $content_range[0]));

        while ($this->filesystem->isFile($this->pathResolver->getUploadPath($this->getSluggedFileName($name)))) {
            if ($uploaded_bytes == $this->filesystem->getFilesize($this->pathResolver->getUploadPath($this->getSluggedFileName($name)))) {
                break;
            }

            $name = $this->pathResolver->upcountName($name);
        }

        return $name;
    }

    public function getSluggedFileName(string $name): string
    {
        $fileNameExploded = explode(".", $name);
        $extension = array_pop($fileNameExploded);
        $fileNameExploded = implode(".", $fileNameExploded);

        return $this->slugify($fileNameExploded) . "." . $extension;
    }


    private function slugify($text): array|string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
