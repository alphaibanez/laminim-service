<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\Attributes\LaminimUse;
use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Traits\ItemWithFileDataTrait;

#[LaminimUse]
class FileFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];
        $r[] = "public function get{$this->data->methodName}() { return \$this->fileD->get('{$this->data->fieldName}'); }";

        if ($this->data->isMultiple) {
            $r[] = "public function get{$this->data->methodName}InternalPath(): array { return \$this->fileD->getInternalPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}PublicPath(): array { return \$this->fileD->getPublicPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Name(int \$index = 0): string { return \$this->fileD->getFileName('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Extension(int \$index = 0): string { return \$this->fileD->getFileExtension('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Content(int \$index = 0): string { return \$this->fileD->getFileContent('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}LastModified(int \$index = 0): int|false { return \$this->fileD->getFileLastModified('{$this->data->fieldName}', \$index); }";
            $r[] = "public function get{$this->data->methodName}Size(int \$index = 0): int|false { return \$this->fileD->getFileSize('{$this->data->fieldName}', \$index); }";

        } else {
            $r[] = "public function get{$this->data->methodName}InternalPath(): string { return \$this->fileD->getInternalPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}PublicPath(): string { return \$this->fileD->getPublicPath('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Name(): string { return \$this->fileD->getFileName('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Extension(): string { return \$this->fileD->getFileExtension('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Content(): string { return \$this->fileD->getFileContent('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}LastModified(): int|false { return \$this->fileD->getFileLastModified('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Size(): int|false { return \$this->fileD->getFileSize('{$this->data->fieldName}'); }";

        }

        $r[] = "public function get{$this->data->methodName}FieldConfig() { return \$this->getSchema()->getFileField('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->fileD->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { \$this->fileD->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}InternalPath(string \${$this->data->fieldName}):static { \$this->fileD->setInternalPath('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}WithHttpFile(array \$value = null):static { \$this->fileD->addUploadingFile('{$this->data->fieldName}', \$value); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->fileD->has('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
        ]);
    }

    public static function generateTraitsUsageCode(Field $field): array
    {
        return [
            ItemWithFileDataTrait::class
        ];
    }
}