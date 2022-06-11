<?php


if (!function_exists('phone_validate_regex')) {
    /**
     * Get phone validation regex
     * @return string
     */
    function phone_validate_regex()
    {
        // 0,3,5 -> STC | 4,6 -> Mobily | 8,9 -> Zain | 1 -> Bravo
        // 0570,0571,0572 -> Virgin | 0576,0577,0578 -> Lebara
        return '/^(009665|9665|\+9665|05)(0|3|5|6|4|9|8|7|1)([0-9]{7})$/';
    }
}

if (!function_exists('key_id')) {
    /**
     * helper function
     * @param int $id
     * @param int $max
     * @return int
     */
    function key_id($id, $max)
    {
        while ($id >= $max)
            if ($id >= $max)
                $id -= $max;
        return $id;
    }
}

if (!function_exists('hex_encode')) {
    function hex_encode($decimal)
    {
        return hex2bin(sprintf('%02s', dechex($decimal)));
    }
}

if (!function_exists('get_tax_qr_base64')) {
    /**
     * @param $sellerName
     * @param $vatNumber
     * @param $createdAt
     * @param $total
     * @param $tax
     * @return string
     */
    function get_tax_qr_base64($sellerName, $vatNumber, $createdAt, $total, $tax): string
    {
        return base64_encode(implode('', [
            hex_encode(1),
            hex_encode(strlen($sellerName)),
            $sellerName,

            hex_encode(2),
            hex_encode(strlen($vatNumber)),
            $vatNumber,

            hex_encode(3),
            hex_encode(strlen($createdAt)),
            $createdAt,

            hex_encode(4),
            hex_encode(strlen($total)),
            $total,

            hex_encode(5),
            hex_encode(strlen($tax)),
            $tax,
        ]));
    }
}
