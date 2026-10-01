<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Enums\ClientFrontLanguage;

final class ClientFrontMaker
{
    protected static self|null $instance = null;

    public static function getInstance(): self
    {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    protected ClientFrontLanguage $clientFrontLanguage = ClientFrontLanguage::TypeScript;
    protected bool $makeHttpConfig = true;
    protected array $makeEnums = [];
    protected string $storePath = '';

    public function setStorePath(string $storePath): self
    {
        $this->storePath = $storePath;
        return $this;
    }

    public function addEnum(string $enumClass): self
    {
        if (!in_array($enumClass, $this->makeEnums)) {
            $this->makeEnums[] = $enumClass;
        }
        return $this;
    }

    public function generate()
    {
        if (!$this->storePath) throw new \Exception("ClientFrontMaker: storePath required");

        $enumPath = "{$this->storePath}/enums";
        if (!is_dir($enumPath)) {
            mkdir($enumPath, 0755, true);
        }

        foreach ($this->makeEnums as $enum) {
            $reflectionEnum = new \ReflectionEnum($enum);
            $code = $this->getEnumCode($reflectionEnum);
            if ($code === '') continue;

            file_put_contents("{$enumPath}/{$reflectionEnum->getShortName()}.ts", $code);
        }
    }

    private function getEnumCode(\ReflectionEnum $reflectionEnum): string
    {
        $backingType = null;
        if ($reflectionEnum->isBacked()) {
            $backingType = $reflectionEnum->getBackingType()?->getName();
        }

        $cases = $reflectionEnum->getCases();

        $arrayEnum = [];
        foreach ($cases as $case) {
            $key = $case->getName();
            $val = $case->getValue()->value;
            if ($backingType === 'string') {
                $arrayEnum[] = "    {$key} = \"{$val}\"";

            } elseif ($backingType === 'int') {
                $val = (int)$val;
                $arrayEnum[] = "    {$key} = {$val}";

            } elseif (!$backingType) {
                $arrayEnum[] = "    {$key}";
            }
        }

        $stringEnum = implode(",\n", $arrayEnum);
        if ($stringEnum === '') {
            return $stringEnum;
        }

        return "enum {$reflectionEnum->getShortName()} { \n{$stringEnum}\n }";
    }
}