<?php

namespace Lkt\WebPages\functions;

function addWebElement(array $data, int $id, int $before = 0, int $after = 0): array
{
    if ($before > 0) {
        $index = array_search($before, $data);
        if ($index === false) {
            $data[] = $id;
        } else {
            array_splice($data, $index, 0, $id);
        }
    }
    elseif ($after > 0) {
        $index = array_search($after, $data);
        if ($index === false) {
            $data[] = $id;
        } else {
            array_splice($data, $index + 1, 0, $id);
        }
    } else {

        $data[] = $id;
    }

    return $data;
}