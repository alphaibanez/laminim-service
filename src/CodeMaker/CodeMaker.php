<?php

namespace Lkt\CodeMaker;

use Lkt\CodeMaker\Helpers\FieldsCodeHelper;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;
use function Lkt\Tools\Strings\removeDuplicatedWhiteSpaces;

class CodeMaker
{
    public static function generate(): void
    {
        $stack = Schema::getStack();
        echo "Generating classes...\n";
        echo "\n";
        echo "\n";

        foreach ($stack as $schema) {

            if ($schema->isLib()) continue;

            $instanceSettings = $schema->getInstanceSettings();
            $filePath = $instanceSettings?->getGeneratedClassFullPath();
            if (str_contains(realpath($filePath), '/vendor')) continue;

            $component = $schema->getComponent();
            echo "Generating code for: {$component}...\n";

            $className = $instanceSettings?->getAppClass();
            $returnSelf = '\\' . $className;

            $extends = $instanceSettings?->hasLegalExtendClass()
                ? $instanceSettings?->getClassToBeExtended()
                : '';

            if ($extends !== '') $extends = "extends \\{$extends}";

            $implements = $instanceSettings?->getImplementedInterfacesAsString();
            if ($implements !== ''){
                $implements = "implements {$implements};";
            }

            $traits = [];
            $instanceTraits = $instanceSettings?->getUsedTraitsAsString();
            if ($instanceTraits !== ''){
                $traits[] = $instanceTraits;
            }

            $namespace = $instanceSettings?->getNamespaceForGeneratedClass();


            $methodsData = FieldsCodeHelper::makeFieldsCode($schema);
            $methods = $methodsData['methods'];
            $methodsTraits = $methodsData['traits'];
            if ($methodsTraits !== ''){
                $traits[] = $methodsTraits;
            }

            $traitsStr = 'use ' . implode(',', $traits) . ';';

            $relatedQueryCaller = $schema->getInstanceSettings()?->getQueryCallerFQDN();

            $templateData['relatedQueryCaller'] = '\Lkt\QueryCaller\QueryCaller';

            if (!$relatedQueryCaller) {
                $relatedQueryCaller = 'Lkt\QueryCaller\QueryCaller';
            }
            $relatedQueryCaller = '\\' . $relatedQueryCaller;

            $code = Template::file(__DIR__ . '/../../assets/phtml/class-template.phtml')->setData([
                'component' => $component,
                'className' => $instanceSettings?->getClassNameForGeneratedClass(),
                'extends' => $extends,
                'implements' => $implements,
                'traits' => $traitsStr,
                'namespace' => $namespace,
                'methods' => $methods,
                'returnSelf' => $returnSelf,
                'queryCaller' => $relatedQueryCaller,
                'hasTable' => $schema->getTable() !== '_'
            ])->parse();
            $code = str_replace("\n", ' ', $code);
            $code = removeDuplicatedWhiteSpaces($code);
            $code = '<?php ' .$code;

            $filePath = $instanceSettings?->getGeneratedClassFullPath();
            $status = $filePath ? file_put_contents($filePath, $code) : false;
            if ($status === false) {
                echo "Could't store {$filePath}\n";
                echo "Maybe an invalid path or not enough permissions\n";
            } else {
                echo "Successful storage at {$filePath}\n";
            }

            echo "\n";
        }
    }
}