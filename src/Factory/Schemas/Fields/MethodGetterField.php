<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Item;
use Lkt\Factory\Schemas\ComputedFields\AbstractComputedField;
use function Lkt\Tools\Reflection\runCallable;

class MethodGetterField extends AbstractComputedField implements Field
{
    protected string $type = 'instance';
    protected mixed $callable = null;

    public static function call(string $name, callable $callable): static
    {
        $r = new static($name);
        $r->type = 'callable';
        $r->callable = $callable;
        return $r;
    }

    public function execute(Item|\Lkt\Factory\Instance\Interfaces\Item $item, array $args)
    {
        if ($this->type === 'callable') {
            return runCallable($this->callable, [
                'item' => $item,
                ...$args,
            ]);
        }

        return runCallable([$item, $this->getName()], [
            'item' => $item,
            ...$args,
        ]);
    }
}