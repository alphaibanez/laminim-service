<?php

namespace Lkt\Tools\Arrays;

/**
 * @param array $initialArray
 * @param array $finishArray
 * @return array|array[]
 */
function compareArrays(array $initialArray, array $finishArray): array
{
    $r = ['added' => [], 'deleted' => []];

    $arr1 = array_unique($initialArray, SORT_REGULAR);
    $arr2 = array_unique($finishArray, SORT_REGULAR);

    foreach ($arr1 as $value) {
        if (!in_array($value, $arr2, true)) {
            $r['deleted'][] = $value;
        }
    }

    foreach ($arr2 as $value) {
        if (!in_array($value, $arr1, true)) {
            $r['added'][] = $value;
        }
    }

    return $r;
}