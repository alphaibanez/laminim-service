<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Enums\ClientFrontLanguage;
use Lkt\Debug\VarDumper;
use Lkt\Http\Enums\ParamType;
use Lkt\Http\Router;
use Lkt\Http\Routes\AbstractRoute;
use Lkt\Http\Routes\DeleteRoute;
use Lkt\Http\Routes\GetRoute;
use Lkt\Http\Routes\PatchRoute;
use Lkt\Http\Routes\PostRoute;
use Lkt\Http\Routes\PutRoute;

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

        if (count($this->makeEnums) > 0) {
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

        $routes = Router::getRoutes();
        if (count($routes) > 0) {

            $httpPath = "{$this->storePath}/config";
            if (!is_dir($httpPath)) {
                mkdir($httpPath, 0755, true);
            }

            $usesGET = false;
            $usesPOST = false;
            $usesPUT = false;
            $usesPATCH = false;
            $usesDELETE = false;

            $payload = [];

            foreach ($routes as $route) {
                $code = $this->getRouteCode($route);

                if ($code) {

                    if ($route instanceof GetRoute) {
                        $usesGET = true;
                        $code = "createHTTPGetResource({$code});";
                    } elseif ($route instanceof PostRoute) {
                        $usesPOST = true;
                        $code = "createHTTPPostResource({$code});";
                    } elseif ($route instanceof PutRoute) {
                        $usesPUT = true;
                        $code = "createHTTPPutResource({$code});";
                    } elseif ($route instanceof PatchRoute) {
                        $usesPATCH = true;
                        $code = "createHTTPPatchResource({$code});";
                    } elseif ($route instanceof DeleteRoute) {
                        $usesDELETE = true;
                        $code = "createHTTPDeleteResource({$code});";
                    }

                    $payload[] = $code;
                }
            }

            $imports = [];
            if ($usesGET) $imports[] = 'createHTTPGetResource';
            if ($usesPOST) $imports[] = 'createHTTPPostResource';
            if ($usesPUT) $imports[] = 'createHTTPPutResource';
            if ($usesPATCH) $imports[] = 'createHTTPPatchResource';
            if ($usesDELETE) $imports[] = 'createHTTPDeleteResource';

            $importsStr = '';
            if (count($imports) > 0) {
                $importsStr = 'import {' . implode(',', $imports) .'} from "laminim-client"';
            }

            $code = 'export const setupHttpConfig = () => {' . "\n\n". implode("\n\n", $payload) . "\n". '};';

            $finalCode = $importsStr . "\n" . $code;

            file_put_contents("{$httpPath}/http-config.ts", $finalCode);

            VarDumper::die($code);
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

    private function getRouteCode(AbstractRoute $route): string
    {
        $name = $route->getName();
        if (!$name) return '';

        $url = $route->getRoute();

        // Replaces url regex like ":\d+"
        // Search between ":" and "}" and removes everything except for "}"
        $url = preg_replace('/:[^}]+(?=})/', '', $url);

        $payload = [
            'name' => $name,
            'url' => $url,
        ];

        $assignParams = [];
        foreach ($route->getParams() as $paramName => $param) {
            /** @var ParamType $type */
            $type = $param[0];

            /** @var mixed $defaultValue */
            $defaultValue = $param[1];

            /** @var array $config */
            $config = $param[3];

            $t = [];
            if ($defaultValue) {
                $t['default'] = "{$defaultValue}";
            }

            if ($type && $type !== ParamType::NotDefined) {
                $t['type'] = "{$type->value}";
            }
            if (count($config) > 0) {
                foreach ($config as $prop => $val) {
                    $t[$prop] = $val;
                }
            }

            $assignParams[$paramName] = $t;
        }

        if (count($assignParams) > 0) {
            $payload['params'] = $assignParams;
        }

        if ($route->allowsAnonymousParams()) {
            $payload['allowAnonymousParams'] = true;
        }

        $digToData = $route->getExpectedResponseDataProperty();
        if ($digToData) {
            $payload['digToData'] = $digToData;
        }

        $digToPerms = $route->getExpectedResponsePermsProperty();
        if ($digToPerms) {
            $payload['digToPerms'] = $digToPerms;
        }

        $digToAutoReloadId = $route->getExpectedResponseIdProperty();
        if ($digToAutoReloadId) {
            $payload['digToAutoReloadId'] = $digToAutoReloadId;
        }

        return json_encode($payload, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_SLASHES ^ JSON_FORCE_OBJECT);
    }
}