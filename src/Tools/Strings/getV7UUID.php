<?php

namespace Lkt\Tools\Strings;
/**
 * According to RFC 9562 compliant UUIDs
 * @see https://www.rfc-editor.org/info/rfc9562/
 *
 * Combines:
 * - 48-bit Unix timestamp in milliseconds
 * - 74-bit random timestamp
 *
 * Advantages:
 * - Time ordering.
 * - Good privacy.
 * - Excellent for databases.
 * - Compatible with distributed systems.
 *
 * @return string
 */
function getV7UUID(): string
{
    // 48 bits of timestamp in ms
    $ms = (int) floor(microtime(true) * 1000);

    // 10 random bytes = 80 bits
    $rand = random_bytes(10);

    // First 6 bytes: timestamp big-endian
    $bytes = '';
    $bytes .= chr(($ms >> 40) & 0xff);
    $bytes .= chr(($ms >> 32) & 0xff);
    $bytes .= chr(($ms >> 24) & 0xff);
    $bytes .= chr(($ms >> 16) & 0xff);
    $bytes .= chr(($ms >> 8) & 0xff);
    $bytes .= chr($ms & 0xff);

    // Byte 6: version 7 + 4 random bits
    $bytes .= chr((0x70) | (ord($rand[0]) & 0x0f));

    // Byte 7: 8 random bits
    $bytes .= $rand[1];

    // Byte 8: RFC 4122 variant (10xxxxxx)
    $bytes .= chr((ord($rand[2]) & 0x3f) | 0x80);

    // Bytes 9-15: random
    $bytes .= substr($rand, 3, 7);

    $hex = bin2hex($bytes);

    return sprintf( '%s-%s-%s-%s-%s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20, 12) );
}