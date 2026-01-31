<?php

namespace Lkt\FileUpload;

use Lkt\MIME;

class File extends \SplFileInfo
{
    /**
     * Preset no errors
     * @var mixed
     */
    public $error = 0;

    /**
     * Preset no errors
     * @var mixed
     */
    public $errorCode = 0;

    /**
     * Preset unknown mime type
     */
    protected string $mimeType = 'application/octet-stream';

    protected string $clientFileName;

    /**
     * Is the file completely downloaded
     */
    public bool $completed = false;

    public function __construct($fileName, string $clientFileName = '')
    {
        $this->setMimeType($fileName);
        $this->clientFileName = $clientFileName;
        parent::__construct($fileName);
    }

    protected function setMimeType($fileName)
    {
        if (file_exists($fileName)) {
            $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileName);
        }
    }

    /**
     * Returns the "original" name of the file
     */
    public function getClientFileName(): string
    {
        return $this->clientFileName;
    }

    public function getMimeType(): string
    {
        if ($this->getType() !== 'file') {
            throw new \Exception('You cannot get the mimetype for a ' . $this->getType());
        }

        return $this->mimeType;
    }

    /**
     * Does this file have an image mime type?
     */
    public function isImage(): bool
    {
        return MIME::isImage($this->mimeType);
    }
}
