<?php

namespace Lkt\FileUpload\Validator;

use Lkt\FileUpload\File;

interface Validator
{
    /**
     * Overwrite the default error messages
     * @param array $messages
     * @return void
     */
    public function setErrorMessages(array $messages): void;

    /**
     * Validate upload
     * @param  File     $file
     * @param  null|int $current_size
     * @return bool
     */
    public function validate(File $file, $current_size = null): bool;
}
