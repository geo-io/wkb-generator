<?php

declare(strict_types=1);

namespace GeoIO\WKB\Generator;

use Exception;
use GeoIO\Coordinates;
use GeoIO\Dimension;
use GeoIO\Extractor;
use GeoIO\GeometryType;
use GeoIO\WKB\Generator\Exception\GeneratorException;
use GeoIO\WKB\Generator\Exception\InvalidOptionException;
use function count;

final class Generator
{
    /**
     * SFS 1.1 WKB (i.e. no Z or M values).
     */
    public const FORMAT_WKB11 = 'wkb11';

    /**
     * SFS 1.2 WKB with Z and M presence flagged by adding 1000 and/or 2000 to
     * the type code.
     */
    public const FORMAT_WKB12 = 'wkb12';

    /**
     * PostGIS EWKB extension with Z and M presence flagged by two high bits of
     * the type code, and support for embedded SRID.
     */
    public const FORMAT_EWKB = 'ewkb';

    private Extractor $extractor;

    private Packer $packer;

    private array $options = [
        'format' => self::FORMAT_WKB11,
        'emit_srid' => false, // Available only if format is FORMAT_EWKB
        'hex' => false,
        'little_endian' => false,
    ];

    public function __construct(
        Extractor $extractor,
        array $options = [],
    ) {
        $this->extractor = $extractor;

        if (isset($options['format'])) {
            $this->options['format'] = match ($options['format']) {
                self::FORMAT_WKB11, self::FORMAT_WKB12, self::FORMAT_EWKB => $options['format'],
                default => throw InvalidOptionException::create(
                    'format',
                    $options['format'],
                    [
                        self::FORMAT_WKB11,
                        self::FORMAT_WKB12,
                        self::FORMAT_EWKB,
                    ]
                ),
            };
        }

        if (isset($options['emit_srid']) && self::FORMAT_EWKB === $this->options['format']) {
            $this->options['emit_srid'] = (bool) $options['emit_srid'];
        }

        if (isset($options['hex'])) {
            $this->options['hex'] = (bool) $options['hex'];
        }

        if (isset($options['little_endian'])) {
            $this->options['little_endian'] = $options['little_endian'];
        }

        $this->packer = new Packer((bool) $this->options['little_endian']);
    }

    public function generate(mixed $geometry): string
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

            return (string) $unpacked[1];
        } catch (Exception $e) {
            throw new GeneratorException('Generation failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function generateGeometry(
        Dimension $dimension,
        mixed $geometry,
    ): string {
        return $this->generateGeometryData(
            $this->extractor->extractType($geometry),
            $dimension,
            $geometry,
            true
        );
    }

    private function generateGeometryData(
        GeometryType $type,
        Dimension $dimension,
        mixed $geometry,
        bool $topLevel = false,
    ): string {
        $typeCode = match ($type) {
            GeometryType::POINT => 1,
            GeometryType::LINESTRING => 2,
            GeometryType::POLYGON => 3,
            GeometryType::MULTIPOINT => 4,
            GeometryType::MULTILINESTRING => 5,
            GeometryType::MULTIPOLYGON => 6,
            GeometryType::GEOMETRYCOLLECTION => 7,
        };

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
            case GeometryType::POINT:
                $data .= $this->generatePoint($geometry, $dimension);
                break;
            case GeometryType::LINESTRING:
                $data .= $this->generateLineString($geometry, $dimension);
                break;
            case GeometryType::POLYGON:
                $data .= $this->generatePolygon($geometry, $dimension);
                break;
            case GeometryType::MULTIPOINT:
                $subGeometries = $this->extractor->extractPointsFromMultiPoint($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, GeometryType::POINT);
                break;
            case GeometryType::MULTILINESTRING:
                $subGeometries = $this->extractor->extractLineStringsFromMultiLineString($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, GeometryType::LINESTRING);
                break;
            case GeometryType::MULTIPOLYGON:
                $subGeometries = $this->extractor->extractPolygonsFromMultiPolygon($geometry);
                $data .= $this->generateMulti($subGeometries, $dimension, GeometryType::POLYGON);
                break;
            default:
                $data .= $this->generateGeometryCollection($geometry, $dimension);
                break;
        }

        return $data;
    }

    private function generateCoordinates(
        ?Coordinates $coordinates,
        Dimension $dimension,
    ): string {
        $data = $this->packer->double($coordinates->x ?? 0.0);
        $data .= $this->packer->double($coordinates->y ?? 0.0);

        if (self::FORMAT_WKB11 !== $this->options['format'] &&
            (Dimension::DIMENSION_4D === $dimension ||
             Dimension::DIMENSION_3DZ === $dimension)) {
            $data .= $this->packer->double($coordinates->z ?? 0.0);
        }

        if (self::FORMAT_WKB11 !== $this->options['format'] &&
            (Dimension::DIMENSION_4D === $dimension ||
             Dimension::DIMENSION_3DM === $dimension)) {
            $data .= $this->packer->double($coordinates->m ?? 0.0);
        }

        return $data;
    }

    private function generatePoint(
        mixed $point,
        Dimension $dimension,
    ): string {
        $coordinates = $this->extractor->extractCoordinatesFromPoint($point);

        return $this->generateCoordinates($coordinates, $dimension);
    }

    private function generateLineString(
        mixed $lineString,
        Dimension $dimension,
    ): string {
        $points = $this->extractor->extractPointsFromLineString($lineString);

        $parts = [];

        /** @var mixed $point */
        foreach ($points as $point) {
            $coordinates = $this->extractor->extractCoordinatesFromPoint($point);
            $parts[] = $this->generateCoordinates($coordinates, $dimension);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generatePolygon(
        mixed $polygon,
        Dimension $dimension,
    ): string {
        $lineStrings = $this->extractor->extractLineStringsFromPolygon($polygon);

        $parts = [];

        /** @var mixed $lineString */
        foreach ($lineStrings as $lineString) {
            $parts[] = $this->generateLineString($lineString, $dimension);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generateMulti(
        iterable $subGeometries,
        Dimension $dimension,
        GeometryType $expectedType,
    ): string {
        $parts = [];

        /** @var mixed $geometry */
        foreach ($subGeometries as $geometry) {
            $parts[] = $this->generateGeometryData($expectedType, $dimension, $geometry);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }

    private function generateGeometryCollection(
        mixed $geometryCollection,
        Dimension $dimension,
    ): string {
        $geometries = $this->extractor->extractGeometriesFromGeometryCollection($geometryCollection);

        $parts = [];

        /** @var mixed $geometry */
        foreach ($geometries as $geometry) {
            $parts[] = $this->generateGeometry($dimension, $geometry);
        }

        $data = $this->packer->integer(count($parts));
        $data .= implode('', $parts);

        return $data;
    }
}
