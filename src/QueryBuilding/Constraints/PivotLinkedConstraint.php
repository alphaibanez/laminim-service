<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Connectors\Interfaces\DatabaseConnector;
use Lkt\Connectors\MariaDBConnector;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class PivotLinkedConstraint extends AbstractConstraint implements QueryConstraint
{
    protected string $mode = 'any';
    protected string $originComponent = '';

    public function __toString(): string
    {
        if (count($this->value) === 0) {
            return '';
        }
        $values = array_map(function($v){ return addslashes(stripslashes((int)$v));}, $this->value);
        $value = "('".implode("','", $values)."')";
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} IN {$value}";
    }

    public function toString(DatabaseConnector $connector = null): string
    {
        if (count($this->value) === 0) {
            return '';
        }

        $schema = Schema::get($this->originComponent);
        $field = $schema->getPivotField($this->column);

        $pivotSchema = $field->getPivotSchema();

        /** @var IntegerField $pivotSchemaSameOriginField */
        $pivotSchemaSameOriginField = $pivotSchema->getOneFieldPointingToComponent($schema->getComponent());
        $pivotSchemaTargetField = $pivotSchemaSameOriginField->isLeftPivot()
            ? $pivotSchema->getPivotRightIdField()
            : $pivotSchema->getPivotLeftIdField();

        $pivotSchemaTargetFieldName = $pivotSchemaTargetField->getColumn();

        $query = $pivotSchema->getQueryBuilder();

        if ($connector instanceof MariaDBConnector) {

            $values = array_map(function($v){ return addslashes(stripslashes((int)$v));}, $this->value);
            $value = "('".implode("','", $values)."')";
            $prepend = $query->getTable();
            if ($prepend) $prepend = "{$prepend}.";
            $where = "{$prepend}{$pivotSchemaTargetFieldName} IN {$value}";

            $originPrepend = $this->getTablePrepend();
            $idColumn = $schema->getIdentifiersNames()[0];
            $query->andRaw("{$originPrepend}{$idColumn} = {$prepend}{$pivotSchemaSameOriginField->getColumn()}");

            $query->andRaw($where);

            return "0 < ({$query->getCountQuery($pivotSchemaTargetFieldName)})";
        }

        return '';
    }

    public static function any(string $field, string $component, array $values): static
    {
        $ins = new static($field, $values);
        $ins->originComponent = $component;
        $ins->mode = 'any';
        return $ins;

    }
}