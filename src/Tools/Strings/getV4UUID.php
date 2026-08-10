<?php

namespace Lkt\Tools\Strings;
/**
 * According to RFC 4122 compliant UUIDs
 * @see https://www.rfc-editor.org/info/rfc4122/
 * @see https://www.rfc-editor.org/rfc/rfc4122.txt
 *
 * Ramdom
 *
 *
 * Generates 122 effective random bits.
 * It is most commonly used for:
 * - User IDs
 * - Internal tokens
 * - APIs
 * - Microservices
 *
 * Advantages:
 * - Does not reveal information.
 * - Very simple.
 * - Collisions are virtually impossible.
 *
 * Disadvantages:
 * - Not temporally ordered.
 * - In some databases, it can fragment indexes.
 *
 * @return string
 */
function getV4UUID(): string
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for the time_low
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        // 16 bits for the time_mid
        mt_rand(0, 0xffff),
        // 16 bits for the time_hi,
        mt_rand(0, 0x0fff) | 0x4000,

        // 8 bits and 16 bits for the clk_seq_hi_res,
        // 8 bits for the clk_seq_low,
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for the node
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}