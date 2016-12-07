<?php
/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @copyright Copyright (c) 2004, 2013 Jan Krakora
 * @license   PhpModbus license
 * @category  Phpmodbus
 * @tutorial  Phpmodbus.pkg
 * @package   Phpmodbus
 * @version   $id$
 */

namespace PHPModbus\Packet;

use PHPModbus\IecType;

class ReadCoilsPacket
{
    /**
     * FC1 packet builder - read coils
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return string
     */
    public static function build($unitId, $reference, $quantity)
    {
        $dataLen = 0;
        // build data section
        $buffer1 = '';
        // build body
        $buffer2 = '';
        $buffer2 .= IecType::iecBYTE(1);              // FC 1 = 1(0x01)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        $dataLen += 5;
        // build header
        $buffer3 = '';
        $buffer3 .= IecType::iecINT(mt_rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // length
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        return $buffer3 . $buffer2 . $buffer1;
    }


    /**
     * FC 1 response parser
     *
     * @param  string $packet
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public static function parse($packet, $quantity)
    {
        $data = array();
        for ($i = 0, $len = ord($packet[8]); $i < $len; $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        // get bool values to array
        $data_boolean_array = array();
        $di = 0;
        foreach ($data as $value) {
            for ($i = 0; $i < 8; $i++) {
                if ($di == $quantity) {
                    continue;
                }
                // get boolean value
                $v = ($value >> $i) & 0x01;
                // build boolean array
                if ($v == 0) {
                    $data_boolean_array[] = false;
                } else {
                    $data_boolean_array[] = true;
                }
                $di++;
            }
        }
        return $data_boolean_array;
    }

}