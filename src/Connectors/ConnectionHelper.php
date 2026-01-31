<?php

namespace Lkt\Connectors;

class ConnectionHelper
{
//    private static int $PREPARED_PARAM_INDEX = 0;

    /**
     * @param string $query
     * @return string
     */
    public static function sanitizeQuery(string $query = ''): string
    {
//        $q = \explode("\n", $query);
//        $t = [];
//        foreach ($q as $line) {
//            $l = \trim($line);
//            if (\strpos($l, '--') !== 0) {
//                $t[] = $l;
//            }
//        }
//
//        $query = \implode(' ', $t);

//        $query = \str_replace("\n", ' ', $query);
//        $query = \str_replace("\t", ' ', $query);
//        $query = \preg_replace('!\s+!', ' ', $query);
//        $query = \str_replace(", ", ',', $query);
        $query = \str_replace("( ", '(', $query);
        $query = \str_replace(" )", ')', $query);
        return \trim($query);
    }

//    /**
//     * @param string $sql
//     * @param array $params
//     * @return string
//     */
//    public static function prepareParams(string $sql = '', array $params = []): string
//    {
//        // Check if not is an associate array
//        if (array_values($params) === $params && count($params) > 0) {
//            self::normalize($sql, $params);
//        }
//
//        // Replace placeholders in the query
//        \preg_match_all("/\:param_[0-9_]+/", $sql, $matches);
//
//        // Scape them
//        $escaped = [];
//        foreach ($matches[0] as $key) {
//            $escaped[] = $params[\str_replace(':', '', $key)];
//        }
//        $queryParts = \preg_split("/\:param_[0-9_]+/", $sql);
//        $parts = \count($queryParts);
//        $sql = '';
//        for ($i = 0; $i <= $parts; $i++) {
//            if (!isset ($queryParts[$i])) {
//                $queryParts[$i] = '';
//            }
//
//            if (!isset ($escaped[$i])) {
//                $escaped[$i] = '';
//            } else {
//                $escaped[$i] = \addslashes(\stripslashes($escaped[$i]));
//            }
//
//            $sql .= $queryParts[$i] . $escaped[$i];
//        }
//        return \trim($sql);
//    }
//
//    /**
//     * @param string $query
//     * @param array $params
//     * @return void
//     */
//    public static function normalize(string &$query, array &$params = []): void
//    {
//        static::$PREPARED_PARAM_INDEX = 0;
//        $query = \preg_replace_callback('/\?/', ['self', 'normalizeParams'], $query);
//
//        $newParams = [];
//        foreach ($params as $key => $param) {
//            $newParams['param_' . $key] = $param;
//        }
//
//        $params = $newParams;
//    }
//
//    /**
//     * @return string
//     */
//    private static function normalizeParams(): string
//    {
//        return ':param_' . static::$PREPARED_PARAM_INDEX++;
//    }
}