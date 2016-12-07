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

class WriteMultipleRegisterPacket
{

    /**
     * Packet builder FC16 - WRITE multiple register
     *     e.g.: 4dd90000000d0010300000030603e807d00bb8
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @param  array $dataTypes
     * @param  int $endianness (0 = little endian = 0, 1 = big endian)
     * @return string
     */
    public static function build($unitId, $reference, array $data, array $dataTypes, $endianness)
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
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
        $buffer2 .= IecType::iecBYTE(16);             // FC 16 = 16(0x10)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($dataLen / 2);        // word count
        $buffer2 .= IecType::iecBYTE($dataLen);     // byte count
        $dataLen += 6;
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
     * FC16 response parser
     *
     * @return bool
     * @throws \Exception
     */
    public static function parse()
    {
        return true;
    }

}