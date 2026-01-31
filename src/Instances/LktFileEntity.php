<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktFileEntity;
use Lkt\Http\Response;
use Lkt\MIME;

class LktFileEntity extends GeneratedLktFileEntity
{
    const COMPONENT = 'lkt-file-entity';

    public static $schemaStorePath = null;
    public static $schemaPublicPath = null;

    public function read()
    {
        $fields = Schema::get(static::COMPONENT)->getAllFields();
        return $this->readFields($fields);
    }

    public function doCreate(array $data): static
    {
        LktFileEntity::feedInstance($this, $data, 'create');
        $this->save();

        if ($data['parent']) {
            $parent = static::getInstance($data['parent']);
            $parent->setChildren([...$parent->getChildrenIds(), $this->getId()])->save();
        }

        return $this;
    }

    public function doUpdate(array $data): static
    {
        LktFileEntity::feedInstance($this, $data, 'update');
        return $this->save();
    }

    public function getSrcResponse(): Response
    {
        $path = static::getSchemaStorePath($this);
        if (!$path) return Response::notFound();

        $fileName = $this->getSrcName();
        if (!$fileName) return Response::notFound();

        $file = $this->getSrc();
        $content = file_get_contents($file->path);
        return Response::ok($content)
            ->setContentTypeMIME(MIME::getByExtension(pathinfo($fileName, PATHINFO_EXTENSION)))
            ->enableCacheToOneYear()
            ;
    }


    public static function getSchemaStorePath($instance): string
    {
        if (is_callable(static::$schemaStorePath)) {
            return call_user_func(static::$schemaStorePath, $instance);
        }
        return '';
    }


    public static function getSchemaPublicPath(LktFileEntity|null $instance = null): string
    {
        if ($instance instanceof LktFileEntity) {
            if ($instance->typeIsUnit() || $instance->typeIsDir()) return '';
        }

        if (is_callable(static::$schemaPublicPath)) {
            return call_user_func(static::$schemaPublicPath, $instance);
        }
        return '';
    }
}