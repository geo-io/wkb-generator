<?php

namespace GeoIO\WKB\Generator;

class Packer
{
    private $littleEndian;

    public function __construct($littleEndian)
    {
        $this->littleEndian = (bool) $littleEndian;
    }

    public function endian()
    {
        return $this->byte($this->littleEndian ? 1 : 0);
    }

    public function byte($value)
    {
        return pack('c', $value);
    }

    public function integer($value)
    {
        return pack($this->littleEndian ? 'V' : 'N', $value);
    }

    public function double($value)
    {
        $value = pack('d', $value);

        if (!$this->littleEndian) {
            $value = strrev($value);
        }

        return $value;
    }
}
