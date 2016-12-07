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

class MaskWriteRegisterPacket
{
    /**
     * Packet builder FC22 - MASK WRITE register
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $andMask
     * @param  int $orMask
     * @return string
     */
    public static function build($unitId, $reference, $andMask, $orMask)
    {
        $dataLen = 0;
        // build data section
        $buffer1 = '';
        // build body
        $buffer2 = '';
        $buffer2 .= IecType::iecBYTE(22);             // FC 22 = 22(0x16)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($andMask);        // AND mask
        $buffer2 .= IecType::iecINT($orMask);          // OR mask
        $dataLen += 7;
        // build header
        $buffer3 = '';
        $buffer3 .= IecType::iecINT(mt_rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        return $buffer3 . $buffer2 . $buffer1;
    }


    /**
     * FC22 response parser
     *
     * @param  string $packet
     * @return bool
     * @throws \Exception
     */
    public static function parse($packet)
    {
        return true;
    }

}