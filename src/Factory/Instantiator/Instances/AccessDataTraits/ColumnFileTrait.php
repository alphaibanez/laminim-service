<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithFileDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Schema;
use Lkt\FileReader\File;

trait ColumnFileTrait
{
    use ItemWithFileDataTrait;

    /**
     * @param string $fieldName
     * @return File|null|File[]
     */
    protected function _getFileVal(string $fieldName): File|array|null
    {
        return $this->fileData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasFileVal(string $fieldName): bool
    {
        return $this->fileData->has($fieldName);
    }

    /**
     * @param string $fieldName
     * @param string|null $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setFileVal(string $fieldName, string|array $value = null): static
    {
        $this->fileData->set($fieldName, $value);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string|null $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setFileValWithHttpFile(string $fieldName, array $value = null): static
    {
        $this->fileData->addUploadingFile($fieldName, $value);
        return $this;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getInternalPath(string $fieldName): string
    {
        return $this->fileData->getInternalPath($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getPublicPath(string $fieldName): string|array
    {
        return $this->fileData->getPublicPath($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getFileName(string $fieldName, int $index = 0): string
    {
        return $this->fileData->getFileName($fieldName, $index);
    }

    public function parseFileName(string $name, FileField $field, int|null $index = null): string
    {
        return $this->fileData->parseFileName($name, $field, $index);
    }

    /**
     * @param string $fieldName
     * @param string $src
     * @return void
     */
    protected function _setInternalPath(string $fieldName, string $src)
    {
        $this->fileData->setInternalPath($fieldName, $src);
    }

    /**
     *  @deprecated
     * @param string $fieldName
     * @return FileField|null
     * @throws SchemaNotDefinedException
     */
    protected function _getFileFieldConfig(string $fieldName): ?FileField
    {
        $schema = Schema::get(static::COMPONENT);
        return $schema->getFileField($fieldName);
    }

    /**
     * @param string $fieldName
     * @return FileField|null
     * @throws SchemaNotDefinedException
     */
    protected function _getFileContent(string $fieldName, int $index = 0): ?string
    {
        return $this->fileData->getFileContent($fieldName, $index);
//        $schema = Schema::get(static::COMPONENT);
//        /** @var FileField $field */
//        $field = $schema->getField($fieldName);
//
////        if ($field->isMultiple()) {
////            $items = $this->_getFileVal($fieldName);
////
////        }
//
//        $name = $this->_getFileName($fieldName, $index);
//        return file_get_contents($field->getStorePath().'/'.$name);
    }

    /**
     * @param string $fieldName
     * @return FileField|null
     * @throws SchemaNotDefinedException
     */
    protected function _getFileExtension(string $fieldName, int $index = 0): ?string
    {
        return $this->fileData->getFileExtension($fieldName, $index);
//        $schema = Schema::get(static::COMPONENT);
//        /** @var FileField $field */
//        $field = $schema->getField($fieldName);
//
//        $name = $this->_getFileName($fieldName, $index);
//
//        return pathinfo($name, PATHINFO_EXTENSION);
    }

    /**
     * @param string $fieldName
     * @return FileField|null
     * @throws SchemaNotDefinedException
     */
    protected function _getFileLastModified(string $fieldName, int $index = 0): false|int
    {
        return $this->fileData->getFileLastModified($fieldName, $index);
//        $schema = Schema::get(static::COMPONENT);
//        /** @var FileField $field */
//        $field = $schema->getField($fieldName);
//
//        $name = $this->_getFileName($fieldName, $index);
//        return filemtime($field->getStorePath().'/'.$name);
    }

    /**
     * @param string $fieldName
     * @return FileField|null
     * @throws SchemaNotDefinedException
     */
    protected function _getFileSize(string $fieldName, int $index = 0): false|int
    {
        return $this->fileData->getFileSize($fieldName, $index);
//        $schema = Schema::get(static::COMPONENT);
//        /** @var FileField $field */
//        $field = $schema->getField($fieldName);
//
//        $name = $this->_getFileName($fieldName, $index);
//        return filesize($field->getStorePath().'/'.$name);
    }
}