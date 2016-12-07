<?php

namespace PHPModbus;

/**
 * Phpmodbus Copyright (c) 2004, 2012 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @copyright Copyright (c) 2004, 2012 Jan Krakora
 * @license   PhpModbus license
 * @category  Phpmodbus
 * @tutorial  Phpmodbus.pkg
 * @package   Phpmodbus
 * @version   $id$
 */

/**
 * ModbusMasterTcp
 *
 * This class deals with the MODBUS master using TCP. Extends ModbusMaster class.
 *
 * @author    Jan Krakora
 * @copyright Copyright (c) 2004, 2012 Jan Krakora
 * @package   Phpmodbus
 */
class ModbusMasterTcp extends ModbusMaster
{
    /**
     * ModbusMasterTcp
     *
     * This is the constructor that defines {@link $host} IP address of the object.
     *
     * @param String $host An IP address of a Modbus TCP device. E.g. "192.168.1.1".
     */
    public function __construct($host)
    {
        parent::__construct($host, "TCP");
    }
}
