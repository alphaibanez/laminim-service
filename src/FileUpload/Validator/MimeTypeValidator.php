<?php

namespace Lkt\FileUpload\Validator;

use Lkt\FileUpload\File;

class MimeTypeValidator implements Validator
{

    const INVALID_MIMETYPE = 0;

    /**
     * List of mimetypes to be considered valid
     */
    protected array $mimeTypes;

    /**
     * Determines if the upload was successful or nay
     */
    protected bool $isValid;

    /**
     * Default error message for this Validator
     */
    protected array $errorMessages = [
        self::INVALID_MIMETYPE => "The uploaded filetype (mimetype) is invalid"
    ];

    public function __construct(array $validMimeTypes)
    {
        $this->mimeTypes = $validMimeTypes;
        $this->isValid = true; //Innocent (Valid file) unless proven otherwise :)
    }


    /**
     * {@inheritdoc}
     */
    public function setErrorMessages(array $messages): void
    {
        foreach ($messages as $key => $value) {
            $this->errorMessages[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(File $file, $currentSize = null): bool
    {
        if (! in_array($file->getMimeType(), $this->mimeTypes)) {
            $this->isValid = false;
            $file->error = $this->errorMessages[self::INVALID_MIMETYPE];
        }

        return $this->isValid;
    }
}
