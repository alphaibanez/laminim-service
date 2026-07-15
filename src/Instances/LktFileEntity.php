<?php

namespace Lkt\Instances;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktFileEntity;
use Lkt\Http\Response;
use Lkt\MIME;

class LktFileEntity extends GeneratedLktFileEntity
{
    const COMPONENT = 'lkt-file-entity';

    public function read()
    {
        $fields = Schema::get(static::COMPONENT)->getAllFields();
        return $this->readFields($fields);
    }

    public function doCreate(array $data): static
    {
        $data['parentId'] = (int)$data['parent'];
        $this->feed($data);
//        LktFileEntity::feedInstance($this, $data);
        $this->save();

//        if ($data['parent']) {
//            $parent = static::getInstance($data['parent']);
//            $parent->setChildren([...$parent->getChildrenIds(), $this->getId()])->save();
//        }

        return $this;
    }

    public function doUpdate(array $data): static
    {
        $this->feed($data);
//        LktFileEntity::feedInstance($this, $data);
        return $this->save();
    }

    public function getSrcResponse(): Response
    {
        $path = static::getSchemaStorePath($this);
        if (!$path) return Response::notFound();

        $fileName = $this->getSrcName();
        if (!$fileName) return Response::notFound();

        $file = $this->getSrc();
        $storePath = $this::getSchemaStorePath($this) ?? $file->directory->path;
        $content = file_get_contents("{$storePath}/{$fileName}");
        return Response::ok($content)
            ->setContentTypeMIME(MIME::getByExtension(pathinfo($fileName, PATHINFO_EXTENSION)))
            ->enableCacheToOneYear()
            ;
    }

    public function getContainerStorageUnit()
    {
        if ($this->typeIsStorageUnit()) return $this->getId();

        return $this->getParent()?->getContainerStorageUnit();
    }
}