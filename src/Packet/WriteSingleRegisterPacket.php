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

class WriteSingleRegisterPacket
{

    /**
     * Packet builder FC6 - WRITE single register
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return string
     */
    public static function build($unitId, $reference, array $data)
    {
        $dataLen = 0;
        // build data section
        $buffer1 = '';
        foreach ($data as $key => $dataitem) {
            $buffer1 .= IecType::iecINT($dataitem);   // register values x
            $dataLen += 2;
            break;
        }
        // build body
        $buffer2 = '';
        $buffer2 .= IecType::iecBYTE(6);             // FC6 = 6(0x06)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $dataLen += 3;
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
     * FC6 response parser
     *
     * @return bool
     * @throws \Exception
     */
    public static function parse()
    {
        return true;
    }

}