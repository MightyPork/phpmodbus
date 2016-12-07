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

class ReadWriteRegistersPacket
{
    /**
     * Packet FC23 builder - READ WRITE registers
     *
     * @param  int $unitId
     * @param  int $referenceRead
     * @param  int $quantity
     * @param  int $referenceWrite
     * @param  array $data
     * @param  array $dataTypes
     * @param  int $endianness (0 = little endian = 0, 1 = big endian)
     * @return string
     */
    public static function build(
        $unitId,
        $referenceRead,
        $quantity,
        $referenceWrite,
        array $data,
        array $dataTypes,
        $endianness
    )
    {

        $dataLen = 0;
        // build data section
        $buffer1 = '';
        foreach ($data as $key => $dataitem) {
            if ($dataTypes[$key] === 'INT') {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            } elseif ($dataTypes[$key] === 'DINT') {
                $buffer1 .= IecType::iecDINT($dataitem, $endianness);   // register values x
                $dataLen += 4;
            } elseif ($dataTypes[$key] === 'REAL') {
                $buffer1 .= IecType::iecREAL($dataitem, $endianness);   // register values x
                $dataLen += 4;
            } else {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            }
        }
        // build body
        $buffer2 = '';
        $buffer2 .= IecType::iecBYTE(23);             // FC 23 = 23(0x17)
        // build body - read section
        $buffer2 .= IecType::iecINT($referenceRead);  // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        // build body - write section
        $buffer2 .= IecType::iecINT($referenceWrite); // refnumber = 12288
        $buffer2 .= IecType::iecINT($dataLen / 2);      // word count
        $buffer2 .= IecType::iecBYTE($dataLen);       // byte count
        $dataLen += 10;
        // build header
        $buffer3 = '';
        $buffer3 .= IecType::iecINT(mt_rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // length
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * FC23 response parser
     *
     * @param  string $packet
     * @return array|false
     * @throws \Exception
     */
    public static function parse($packet)
    {
        $data = array();
        // get data
        for ($i = 0, $len = ord($packet[8]); $i < $len; $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        return $data;
    }

}