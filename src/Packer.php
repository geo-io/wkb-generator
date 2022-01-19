<?php

declare(strict_types=1);

namespace GeoIO\WKB\Generator;

final class Packer
{
    private bool $littleEndian;

    public function __construct(bool $littleEndian)
    {
        $this->littleEndian = $littleEndian;
    }

    public function endian(): string
    {
        return $this->byte($this->littleEndian ? 1 : 0);
    }

    public function byte(int $value): string
    {
        return pack('c', $value);
    }

    public function integer(int $value): string
    {
        return pack($this->littleEndian ? 'V' : 'N', $value);
    }

    public function double(float $value): string
    {
        $data = pack('d', $value);

        if (!$this->littleEndian) {
            $data = strrev($data);
        }

        return $data;
    }
}
