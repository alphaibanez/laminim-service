<?php
namespace Lkt\FileUpload;


use Lkt\FileUpload\FileNameGenerator\FileNameGenerator;
use Lkt\FileUpload\FileSystem\FileSystem;
use Lkt\FileUpload\PathResolver\PathResolver;
use Lkt\FileUpload\Validator\Validator;

class FileUploadFactory
{
    /**
     * Validator to be used in the factory
     * @var Validator[]
     */
    protected $validators;

    /**
     * PathResolver to be used in the factory
     * @var PathResolver
     */
    protected $pathresolver;

    /**
     * FileSystem to be used in the factory
     * @var FileSystem
     */
    protected $filesystem;

    /**
     * FileNameGenerator to be used in the factory
     * @var FileNameGenerator
     */
    protected $fileNameGenerator;

    /**
     * Construct new factory with the given modules
     */
    public function __construct(
        PathResolver $pathresolver,
        FileSystem $filesystem,
        $validators = [],
        ?FileNameGenerator $fileNameGenerator = null
    ) {
        $this->pathresolver = $pathresolver;
        $this->filesystem = $filesystem;
        $this->validators = $validators;
        $this->fileNameGenerator = $fileNameGenerator;
    }

    /**
     * Create new instance of FileUpload with the preset modules
     */
    public function create(array $upload, array $server): FileUpload
    {
        $fileupload = new FileUpload($upload, $server);
        $fileupload->setPathResolver($this->pathresolver);
        $fileupload->setFileSystem($this->filesystem);
        if (null !== $this->fileNameGenerator) {
            $fileupload->setFileNameGenerator($this->fileNameGenerator);
        }

        foreach ($this->validators as $validator) {
            $fileupload->addValidator($validator);
        }

        return $fileupload;
    }
}
