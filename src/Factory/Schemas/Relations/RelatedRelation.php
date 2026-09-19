<?php

namespace Lkt\Factory\Schemas\Relations;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\QueryBuilding\Query;

/**
 * @deprecated
 * used for related keys merge field
 */
class RelatedRelation
{
    protected string $component;
    protected string $fieldPointingMe;
    protected array $additionalColumns = [];
    protected $queryConfigurator = null;

    /**
     * @throws InvalidComponentException
     */
    public function __construct(string $component, string $fieldPointingMe, array $additionalColumns = [], ?callable $queryConfigurator = null)
    {
        $this->component = $component;
        $this->fieldPointingMe = $fieldPointingMe;
        $this->additionalColumns = $additionalColumns;
        $this->queryConfigurator = $queryConfigurator;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * @deprecated unused
     */
    public function getPointerField(): string
    {
        return $this->fieldPointingMe;
    }

    /**
     * @deprecated used for related keys merge field
     */
    public function getAdditionalColumns(): array
    {
        return $this->additionalColumns;
    }

    /**
     * @deprecated used for related keys merge field
     */
    public function hasAdditionalColumns(): bool
    {
        return count($this->additionalColumns) > 0;
    }

    public function applyQueryConfigurator(Query $query): void
    {
        if ($this->queryConfigurator) {

            if (is_array($this->queryConfigurator)) {
                call_user_func_array($this->queryConfigurator, [$query]);
            } else {
                call_user_func($this->queryConfigurator, $query);
            }
        }
    }
}