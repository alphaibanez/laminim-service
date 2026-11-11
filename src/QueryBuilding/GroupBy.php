<?php

namespace Lkt\QueryBuilding;

use Lkt\Connectors\Interfaces\DatabaseConnector;
use Lkt\Connectors\MariaDBConnector;
use Lkt\QueryBuilding\Enums\DateGroupByInterval;

class GroupBy
{
    protected array $data = [];

    protected function __construct(string $field)
    {
        $this->data[] = $field;
    }

    public function toString(DatabaseConnector|null $connector = null): string
    {
        $r = [];
        foreach ($this->data as $datum) {
            if (str_starts_with($datum, 'lktDateInterval:')) {
                $parts = explode(':', $datum);
                $interval = DateGroupByInterval::tryFrom($parts[1]);
                if (!$interval) continue;

                $f = $parts[2];

                if ($connector instanceof MariaDBConnector) {
                    switch ($interval) {
                        case DateGroupByInterval::FifteenMinutes:
                            $r[] = "CONCAT(DATE_FORMAT({$f}, \"%Y-%m-%d %H:\"),IF(\"15\">MINUTE({$f}), \"00\", IF(\"30\">MINUTE({$f}), \"15\", IF(\"45\">MINUTE({$f}), \"30\", \"45\"))))";
                            break;
                        case DateGroupByInterval::ThirtyMinutes:
                            $r[] = "CONCAT(DATE_FORMAT({$f}, \"%Y-%m-%d %H:\"),IF(\"30\">MINUTE({$f}), \"00\", \"30\"))";
                            break;
                        case DateGroupByInterval::OneHour:
                            $r[] = "CONCAT(EXTRACT( YEAR_MONTH FROM `{$f}` ), \"-\" ,DAY({$f}), \" \", HOUR({$f}))";
                            break;
                        case DateGroupByInterval::OneDay:
                            $r[] = "CONCAT(EXTRACT( YEAR_MONTH FROM `{$f}` ), \"-\" ,DAY({$f}))";
                            break;
                        case DateGroupByInterval::OneWeek:
                            $r[] = "CONCAT(EXTRACT( YEAR_MONTH FROM `{$f}` ), \"-\" ,WEEK({$f}))";
                            break;
                        case DateGroupByInterval::OneMonth:
                            $r[] = "EXTRACT( YEAR_MONTH FROM `{$f}`)";
                            break;
                    }
                }

            } else {
                $r[] = $datum;
            }
        }

        return implode(', ', $r);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function andGroup(string $field): static
    {
        $this->data[] = $field;
        return $this;
    }

    public static function define(string $field): static
    {
        return new static($field);
    }

    public static function dateBy(string $field, DateGroupByInterval $interval)
    {
        return new static("lktDateInterval:{$interval->value}:{$field}");
    }
}