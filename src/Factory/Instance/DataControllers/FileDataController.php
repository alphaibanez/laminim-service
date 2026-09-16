<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\FileUploadHelper;
use Lkt\Factory\Instantiator\SystemConnections\FileSystemConnection;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Schema;
use Lkt\FileReader\Directory;
use Lkt\FileReader\File;
use Lkt\MIME;

final class FileDataController
{
    private array $data = [];
    private array $payload = [];
    private array $httpUpload = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
        foreach ($data as $k => $datum) $this->setOriginal($k, $datum);
    }

    public function get(string $key): File|string|null
    {
        if (array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function getPublicPath(string $key): string|array
    {
        $field = $this->schema->getFileField($key);

        if (!$field->hasPublicPath()) return '';

        if ($field->isMultiple()) {
            $r = [];
            $path = $field->getPublicPath($this);
            foreach ($this->get($key) as $i => $item) {
                $r[] = $this->parseFileName($path, $field, $i + 1);
            }
            return $r;
        }

        return $this->parseFileName($field->getPublicPath($this), $field);
    }

    public function getInternalPath(string $key): string|array
    {
        $file = $this->get($key);
        return $this->item::getSchemaStorePath($this) ?? $file->directory->path;
    }

    public function getFileName(string $key, int $index = 0): string
    {
        $field = $this->schema->getFileField($key);

        if ($field->isMultiple()) {
            $items = $this->get($key);
            return trim($items[$index]->name);
        }

        $file = $this->get($key);
        return trim($file->name);
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getFileField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v !== '';
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getFileField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): File|string|null|array
    {
        if ($value === null) return null;

        if ($value instanceof File) return $value;

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') return null;

            if (str_contains($value, ';base64,')) {
                return $value;
            }
        }

        $field = $this->schema->getFileField($key);

        $storePath = $field->getStorePath($this->item);
        $directory = new Directory(FileSystemConnection::getDiskDriver(), $storePath);
        if (is_array($value)) {
            if (count($value) === 0) return null;

            $r = [];
            foreach ($value as $val) {
                $r[] = new File(FileSystemConnection::getDiskDriver(), $directory, $val);
            }
            return $r;
        }
        return new File(FileSystemConnection::getDiskDriver(), $directory, $value);
    }

    public function getOriginal(string $key): File|string|null
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function setOriginal(string $key, $value): self
    {
        $parsedValue = $this->parse($key, $value);
        $this->data[$key] = $parsedValue;
        return $this;
    }

    public function dumpPayloadIntoOriginal(): self
    {
        $this->data = [...$this->data, ... $this->payload];
        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getOriginalData(): array
    {
        return $this->data;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
            'httpUpload' => $this->httpUpload,
        ];
    }

    public function base64ToFile(string $key, File|string|null $file = null): self
    {
        $file = $file ?? $this->get($key);
        $content = $file instanceof File ? $file->path : $file;
        $base64 = explode(';base64,', $content)[1];
        $content = base64_decode($base64);

        $f = finfo_open();

        $mime_type = finfo_buffer($f, $content, FILEINFO_MIME_TYPE);
        finfo_close($f);

        $ext = MIME::getExtensionByMime($mime_type);

        $field = $this->schema->getFileField($key);
        $storePath = $field->getStorePath($this);

        $component = $this->schema->getComponent();
        $id = $this->schema->getInstanceCode($this->item);
        $storeName = "$component-$id-$key.$ext";
        $name = "$storePath/$storeName";

        file_put_contents($name, $content);

        $this->set($key, $storeName);
        return $this;
    }

    public function base64ToFiles(string $key, array $files): static
    {
        $id = $this->item->getIdColumnValue();
        $finalValue = [];

        $field = $this->schema->getFileField($key);
        $component = $this->schema->getComponent();

        foreach ($files as $i => $file) {
            $content = $file instanceof File ? $file->path : $file;
            $base64 = explode(';base64,', $content)[1];
            $content = base64_decode($base64);

            $f = finfo_open();

            $mime_type = finfo_buffer($f, $content, FILEINFO_MIME_TYPE);
            finfo_close($f);

            $ext = MIME::getExtensionByMime($mime_type);
            $storePath = $field->getStorePath($this);
            $j = $i + 1;
            $storeName = "$component-$id-$key-$j.$ext";
            $name = "$storePath/$storeName";

            file_put_contents($name, $content);
            $finalValue[] = $storeName;
        }

        $this->set($key, $finalValue);
        return $this;
    }

    public function hasPendingHttpUploads(): bool
    {
        return count($this->httpUpload) > 0;
    }

    public function addUploadingFile(string $key, array|null $value = null): self
    {
        $this->httpUpload[$key] = $value;
        return $this;
    }

    public function httpUploadToFile(string $key): self
    {
        $field = $this->schema->getFileField($key);
        $uploadData = FileUploadHelper::uploadFileField($field, $this->httpUpload[$key], $this->item, $this->schema);

        if (is_array($uploadData)) {
            $this->set($key, $uploadData['name']);
        }
        unset($this->httpUpload[$key]);
        return $this;
    }

    public function parseFileName(string $name, FileField $field, int|null $index = null): string
    {
        $fieldName = $field->getName();
        $r = str_replace(':component', $this->schema->getComponent(), $name);
        $r = str_replace(':field', $fieldName, $r);
        $r = str_replace(':id', $this->item->getIdColumnValue(), $r);
        $r = str_replace(':value', $this->getFileName($fieldName, $index - 1), $r);
        if (is_numeric($index)) $r = str_replace(':index', $index, $r);
        return $r;
    }

    public function updatedWithBase64String(string $key): bool
    {
        $field = $this->schema->getFileField($key);
        $data = $this->get($key);

        if ($field->isMultiple()) {
            $r = false;
            foreach ($data as $item) {
                $src = $item instanceof File ? $item->path : trim($item);
                if (is_string($src) && strlen($src) > 5 && str_contains($src, ';base64,')) {
                    $r = true;
                    break;
                }
            }
            return $r;
        }

        $src = $data instanceof File ? $data->path : trim($data);

        return is_string($src)
            && strlen($src) > 5
            && str_contains($src, ';base64,');
    }

    public function setInternalPath(string $key, string $src): self
    {
        $file = $this->get($key);
        if (!$file instanceof File) return $this;
        $file->directory->change($src);
        return $this;
    }

    public function getFileContent(string $key, int $index = 0): string|null
    {
        $field = $this->schema->getFileField($key);
        $name = $this->getFileName($key, $index);
        return file_get_contents($field->getStorePath().'/'.$name);
    }

    public function getFileExtension(string $key, int $index = 0): string|null
    {
        $field = $this->schema->getFileField($key);
        $name = $this->getFileName($key, $index);
        return pathinfo($field->getStorePath().'/'.$name, PATHINFO_EXTENSION);
    }

    public function getFileLastModified(string $key, int $index = 0): false|int
    {
        $field = $this->schema->getFileField($key);
        $name = $this->getFileName($key, $index);
        return filemtime($field->getStorePath().'/'.$name);
    }

    public function getFileSize(string $key, int $index = 0): false|int
    {
        $field = $this->schema->getFileField($key);
        $name = $this->getFileName($key, $index);
        return filesize($field->getStorePath().'/'.$name);
    }
}