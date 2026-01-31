<?php

namespace Lkt\FileUpload\PathResolver;

interface PathResolver
{
    /**
     * Get absolute final destination path
     */
    public function getUploadPath(?string $name = null): string;

    /**
     * Ensure consistent name
     */
    public function upcountName(?string $name): string;
}
