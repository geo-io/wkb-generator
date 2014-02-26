<?php

namespace GeoIO\WKB\Generator;

use GeoIO\Dimension;
use GeoIO\Extractor;
use GeoIO\WKB\Generator\Exception\GeneratorException;
use GeoIO\WKB\Generator\Exception\InvalidOptionException;

class Generator
{
    /**
     * SFS 1.1 WKB (i.e. no Z or M values).
     */
    const FORMAT_WKB11 = 'wkb11';

    /**
     * SFS 1.2 WKB with Z and M presence flagged by adding 1000 and/or 2000 to
     * the type code.
     */
    const FORMAT_WKB12 = 'wkb12';

    /**
     * PostGIS EWKB extension with Z and M presence flagged by two high bits of
     * the type code, and support for embedded SRID.
     */
    const FORMAT_EWKB = 'ewkb';

    private static $typeCodes = array(
        Extractor::TYPE_POINT => 1,
        Extractor::TYPE_LINESTRING => 2,
        Extractor::TYPE_POLYGON => 3,
        Extractor::TYPE_MULTIPOINT => 4,
        Extractor::TYPE_MULTILINESTRING => 5,
        Extractor::TYPE_MULTIPOLYGON => 6,
        Extractor::TYPE_GEOMETRYCOLLECTION => 7,
    );

    private $extractor;

    private $packer;

    private $options = array(
        'format' => self::FORMAT_WKB11,
        'emit_srid' => false, // Available only if format is FORMAT_EWKB
        'hex' => false,
        'little_endian' => false,
    );

    public function __construct(Extractor $extractor, array $options = array())
    {
        $this->extractor = $extractor;

        if (isset($options['format'])) {
            switch ($options['format']) {
                case self::FORMAT_WKB11:
                case self::FORMAT_WKB12:
                case self::FORMAT_EWKB:
                    $this->options['format'] = $options['format'];
                    break;
                default:
                    throw InvalidOptionException::create(
                        'format',
                        $options['format'],
                        array(
                            self::FORMAT_WKB11,
                            self::FORMAT_WKB12,
                            self::FORMAT_EWKB
                        )
                    );
            }
        }

        if (isset($options['emit_srid']) && self::FORMAT_EWKB === $this->options['format']) {
            $this->options['emit_srid'] = (bool) $options['emit_srid'];
        }

        if (isset($options['hex'])) {
            $this->options['hex'] = (bool) $options['hex'];
        }

        if (isset($options['little_endian'])) {
            $this->options['little_endian'] = (bool) $options['little_endian'];
        }

        $this->packer = new Packer($this->options['little_endian']);
    }

    public function generate($geometry)
    {
        try {
            $data = $this->generateGeometry(
                $this->extractor->extractDimension($geometry),
                $geometry
            );

            if (!$this->options['hex']) {
                return $data;
            }

            $unpacked = unpack('H*', $data);

            return $unpacked[1];
        } catch (\Exception $e) {
            throw new GeneratorException('Generation failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function generateGeometry($dimension, $geometry)
    {
        return $this->generateGeometryData(
            $this->extractor->extractType($geometry),
            $dimension,
            $geometry,
            true
        );
    }

    private function generateGeometryData($type, $dimension, $geometry, $topLevel = false)
    {
        $typeCode = self::$typeCodes[$type];

        $emitSrid = false;

        if (self::FORMAT_EWKB === $this->options['format']) {
            if (Dimension::DIMENSION_4D === $dimension ||
                Dimension::DIMENSION_3DZ === $dimension) {
                $typeCode |= 0x80000000;
            }

            if (Dimension::DIMENSION_4D === $dimension ||
                Dimension::DIMENSION_3DM === $dimension) {
                $typeCode |= 0x40000000;
            }

            if ($topLevel && $this->options['emit_srid']) {
                $typeCode |= 0x20000000;
                $emitSrid = true;
            }
        }

        if (self::FORMAT_WKB12 === $this->options['format']) {
            if (Dimension::DIMENSION_4D === $dimension ||
                Dimension::DIMENSION_3DZ === $dimension) {
                $typeCode += 1000;
            }

            if (Dimension::DIMENSION_4D === $dimension ||
                Dimension::DIMENSION_3DM === $dimension) {
                $typeCode += 2000;
            }
        }

        $data = $this->packer->endian();
        $data .= $this->packer->integer($typeCode);

        if ($emitSrid && null !== ($srid = $this->extractor->extractSrid($geometry))) {
            $data .= $this->packer->integer($srid);
        }

        switch ($type) {
            case Extractor::TYPE_POINT:
                $data .= $this->generatePoint($geometry, $dimension);
                break;
            case Extractor::TYPE_LINESTRING:
                $data .= $this->generateLineString($geometry, $dimension);
                break;
            case Extractor::TYPE_POLYGON:
                $data .= $this->generatePolygon($geometry, $dimension);
                break;
            case Extractor::TYPE_MULTIPOINT:
                $subGeometries = $this->extractor->extractPointsFromMultiPoint($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, Extractor::TYPE_POINT);
                break;
            case Extractor::TYPE_MULTILINESTRING:
                $subGeometries = $this->extractor->extractLineStringsFromMultiLineString($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, Extractor::TYPE_LINESTRING);
                break;
            case Extractor::TYPE_MULTIPOLYGON:
                $subGeometries = $this->extractor->extractPolygonsFromMultiPolygon($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, Extractor::TYPE_POLYGON);
                break;
            default:
                $data .= $this->generateGeometryCollection($geometry, $dimension);
                break;
        }

        return $data;
    }

    private function generateCoordinates(array $coordinates, $dimension)
    {
        $data = $this->packer->double(isset($coordinates['x']) ? $coordinates['x'] : 0);
        $data .= $this->packer->double(isset($coordinates['y']) ? $coordinates['y'] : 0);

        if (self::FORMAT_WKB11 !== $this->options['format'] &&
            (Dimension::DIMENSION_4D === $dimension ||
             Dimension::DIMENSION_3DZ === $dimension)) {
            $data .= $this->packer->double(isset($coordinates['z']) ? $coordinates['z'] : 0);
        }

        if (self::FORMAT_WKB11 !== $this->options['format'] &&
            (Dimension::DIMENSION_4D === $dimension ||
             Dimension::DIMENSION_3DM === $dimension)) {
            $data .= $this->packer->double(isset($coordinates['m']) ? $coordinates['m'] : 0);
        }

        return $data;
    }

    private function generatePoint($point, $dimension)
    {
        $coordinates = $this->extractor->extractCoordinatesFromPoint($point);

        return $this->generateCoordinates($coordinates, $dimension);
    }

    private function generateLineString($lineString, $dimension)
    {
        $points = $this->extractor->extractPointsFromLineString($lineString);

        $parts = array();
        foreach ($points as $point) {
            $coordinates = $this->extractor->extractCoordinatesFromPoint($point);
            $parts[] = $this->generateCoordinates($coordinates, $dimension);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generatePolygon($polygon, $dimension)
    {
        $lineStrings = $this->extractor->extractLineStringsFromPolygon($polygon);

        $parts = array();
        foreach ($lineStrings as $lineString) {
            $parts[] = $this->generateLineString($lineString, $dimension);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generateMulti($subGeometries, $dimension, $expectedType)
    {
        $parts = array();
        foreach ($subGeometries as $geometry) {
            $parts[] = $this->generateGeometryData($expectedType, $dimension, $geometry);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generateGeometryCollection($geometryCollection, $dimension)
    {
        $geometries = $this->extractor->extractGeometriesFromGeometryCollection($geometryCollection);

        $parts = array();
        foreach ($geometries as $geometry) {
            $parts[] = $this->generateGeometry($dimension, $geometry);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }
}
