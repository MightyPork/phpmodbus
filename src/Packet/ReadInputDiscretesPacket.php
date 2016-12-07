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

class ReadInputDiscretesPacket
{
    /**
     * FC2 packet builder - read coils
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
        $buffer2 .= IecType::iecBYTE(2);              // FC 2 = 2(0x02)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        $dataLen += 5;
        // build header
        $buffer3 = '';
        $buffer3 .= IecType::iecINT(mt_rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * FC 2 response parser, alias to FC 1 parser i.e. readCoilsParser.
     *
     * @param  string $packet
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public static function parse($packet, $quantity)
    {
        return ReadCoilsPacket::parse($packet, $quantity);
    }

}