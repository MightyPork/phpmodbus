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

/**
 * Builds/parses packet in Modbus TCP/IP format (http://www.simplymodbus.ca/TCP.htm)
 */
class WriteMultipleCoilsPacket
{
    /**
     * Packet builder FC15 - Write multiple coils
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return string
     */
    public static function build($unitId, $reference, array $data)
    {
        $dataLen = 0;

        list($pduData, $wordCount) = self::getDataConvertedToIecWords($data);
        $dataLen += $wordCount;

        // build body
        $pduHeader = '';
        $pduHeader .= IecType::iecBYTE(15);             // FC 15 = 15(0x0f)
        $pduHeader .= IecType::iecINT($reference);      // refnumber = 12288
        $pduHeader .= IecType::iecINT(count($data));      // bit count
        $pduHeader .= IecType::iecBYTE((count($data) + 7) / 8);       // byte count
        $dataLen += 6;

        $mbapHeader = '';
        $mbapHeader .= IecType::iecINT(mt_rand(0, 65000));   // transaction ID
        $mbapHeader .= IecType::iecINT(0);               // protocol ID
        $mbapHeader .= IecType::iecINT($dataLen + 1);    // length
        $mbapHeader .= IecType::iecBYTE($unitId);        // unit ID

        return $mbapHeader . $pduHeader . $pduData;
    }

    /**
     * FC15 response parser
     *
     * @return bool
     * @throws \Exception
     */
    public static function parse()
    {
        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private static function getDataConvertedToIecWords(array $data)
    {
        $data_word_stream = array();
        $data_word = 0;
        $shift = 0;
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            if ((($i % 8) === 0) && ($i > 0)) {
                //shift to next word
                $data_word_stream[] = $data_word;
                $shift = 0;
                $data_word = 0;
            }
            $data_word |= (0x01 && $data[$i]) << $shift;
            $shift++;
        }
        $data_word_stream[] = $data_word;

        $pduData = '';
        foreach ($data_word_stream as $key => $dataitem) {
            $pduData .= IecType::iecBYTE($dataitem);
        }
        return array($pduData, count($data_word_stream));
    }

}