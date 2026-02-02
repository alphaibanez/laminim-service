<?php

namespace Lkt\Factory\Instantiator\Helpers;

use Lkt\FileUpload\File;
use Lkt\FileUpload\FileSystem\Simple;
use Lkt\FileUpload\FileUpload;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Schema;

class FileUploadHelper
{
    /**
     * @param FileField $field
     * @param array $file
     * @param AbstractInstance $instance
     * @param Schema $schema
     * @return array|null
     * @throws UnsetFieldStorePathException
     */
    public static function uploadFileField(FileField $field, array $file, AbstractInstance $instance, Schema $schema): ?array
    {
        $storePath = $field->getStorePath($instance);
        if ($storePath === ''){
            throw UnsetFieldStorePathException::getInstance($field->getName(), $schema->getComponent());
        }

        // Simple validation (max file size and allowed mime types)
        $validator = new \Lkt\FileUpload\Validator\Simple($field->getMaxFileSize(), $field->getSupportedFormats());

        // Simple path resolver, where uploads will be put
        $pathResolver = new \Lkt\FileUpload\PathResolver\Simple($storePath);

        // The machine's filesystem
        $filesystem = new Simple();

        // FileUploader itself
        $fileUpload = new FileUpload($file, $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileUpload->setPathResolver($pathResolver);
        $fileUpload->setFileSystem($filesystem);
        $fileUpload->addValidator($validator);
        $fileUpload->setFileNameGenerator(new LktFieldFileNameGenerator($field, $schema, $instance));

        // Doing the deed
        [$files, $headers] = $fileUpload->processAll();

        /** @var File[] $filesLoaded */
        $filesLoaded = $fileUpload->getFiles();

        if ($files[0]->completed) {
            $files[0]->name = $filesLoaded[0]->getFilename();
            $files[0]->size = $filesLoaded[0]->getSize();

            return [
                'name' => $filesLoaded[0]->getFilename(),
                'extension' => $filesLoaded[0]->getExtension(),
                'mimeType' => $filesLoaded[0]->getMimeType(),
                'size' => $filesLoaded[0]->getSize(),
                'headers' => $headers,
            ];
        }
        return null;
    }
}