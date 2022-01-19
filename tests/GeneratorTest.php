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
use PHPUnit\Framework\TestCase;
use stdClass;

class GeneratorTest extends TestCase
{
    public function testPointXdrHex(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointXdrBinary(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, [
            'hex' => false,
            'little_endian' => false,
        ]);
        $this->assertSame(pack('H*', '00000000013ff00000000000004000000000000000'), $generator->generate($point));
    }

    public function testPointNdr(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => true,
        ]);
        $this->assertSame('0101000000000000000000f03f0000000000000040', $generator->generate($point));
    }

    public function testPointEwkb(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointEwkbWith(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2),
            1000
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'emit_srid' => true,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0020000001000003e83ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointEwkbZ(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DZ,
            $this->coords(1, 2, 3)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00800000013ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointEwkbM(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DM,
            $this->coords(1, 2, null, 3)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00400000013ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointEwkbZM(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_4D,
            $this->coords(1, 2, 3, 4)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00c00000013ff0000000000000400000000000000040080000000000004010000000000000', $generator->generate($point));
    }

    public function testPointWkb12(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointWkb12Z(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DZ,
            $this->coords(1, 2, 3)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000003e93ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointWkb12M(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DM,
            $this->coords(1, 2, null, 3)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000007d13ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointWkb12ZM(): void
    {
        $point = new stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_4D,
            $this->coords(1, 2, 3, 4)
        );

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0000000bb93ff0000000000000400000000000000040080000000000004010000000000000', $generator->generate($point));
    }

    public function testLineString(): void
    {
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();
        $point3 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($lineString)
            ->willReturn(GeometryType::LINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($lineString)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2, $point3])
        ;

        $extractor
            ->expects($this->exactly(3))
            ->method('extractCoordinatesFromPoint')
            ->with($lineString)
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringEmpty(): void
    {
        $lineString = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($lineString)
            ->willReturn(GeometryType::LINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($lineString)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([])
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000200000000', $generator->generate($lineString));
    }

    public function testLineStringEwkbZ(): void
    {
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($lineString)
            ->willReturn(GeometryType::LINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($lineString)
            ->willReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractCoordinatesFromPoint')
            ->with($lineString)
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2, 3),
                $this->coords(4, 5, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0080000002000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringEwkbZWithSrid(): void
    {
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractSrid')
            ->with($lineString)
            ->willReturn(1000)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($lineString)
            ->willReturn(GeometryType::LINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($lineString)
            ->willReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractCoordinatesFromPoint')
            ->with($lineString)
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2, 3),
                $this->coords(4, 5, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'emit_srid' => true,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00a0000002000003e8000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringWkb12M(): void
    {
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($lineString)
            ->willReturn(GeometryType::LINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($lineString)
            ->willReturn(Dimension::DIMENSION_3DM)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractCoordinatesFromPoint')
            ->with($lineString)
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2, null, 3),
                $this->coords(4, 5, null, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000007d2000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testPolygon(): void
    {
        $polygon = new stdClass();
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();
        $point3 = new stdClass();
        $point4 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($polygon)
            ->willReturn(GeometryType::POLYGON)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($polygon)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractLineStringsFromPolygon')
            ->with($polygon)
            ->willReturn([$lineString])
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2, $point3, $point4])
        ;

        $extractor
            ->expects($this->exactly(4))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(6, 5),
                $this->coords(1, 2),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000300000001000000043ff0000000000000400000000000000040080000000000004010000000000000401800000000000040140000000000003ff00000000000004000000000000000', $generator->generate($polygon));
    }

    public function testPolygonEmpty(): void
    {
        $polygon = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($polygon)
            ->willReturn(GeometryType::POLYGON)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($polygon)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractLineStringsFromPolygon')
            ->with($polygon)
            ->willReturn([])
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000300000000', $generator->generate($polygon));
    }

    public function testMultiPoint(): void
    {
        $multiPoint = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiPoint)
            ->willReturn(GeometryType::MULTIPOINT)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiPoint)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromMultiPoint')
            ->with($multiPoint)
            ->willReturn([$point1, $point2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2),
                $this->coords(3, 4),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00000000040000000200000000013ff00000000000004000000000000000000000000140080000000000004010000000000000', $generator->generate($multiPoint));
    }

    public function testMultiPointEwkbZ(): void
    {
        $multiPoint = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiPoint)
            ->willReturn(GeometryType::MULTIPOINT)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiPoint)
            ->willReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromMultiPoint')
            ->with($multiPoint)
            ->willReturn([$point1, $point2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2, 5),
                $this->coords(3, 4, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('00800000040000000200800000013ff0000000000000400000000000000040140000000000000080000001400800000000000040100000000000004018000000000000', $generator->generate($multiPoint));
    }

    public function testMultiPointEmpty(): void
    {
        $multiPoint = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiPoint)
            ->willReturn(GeometryType::MULTIPOINT)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiPoint)
            ->willReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromMultiPoint')
            ->with($multiPoint)
            ->willReturn([])
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000400000000', $generator->generate($multiPoint));
    }

    public function testMultiLineString(): void
    {
        $multiLineString = new stdClass();
        $lineString1 = new stdClass();
        $lineString2 = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();
        $point3 = new stdClass();
        $point4 = new stdClass();
        $point5 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiLineString)
            ->willReturn(GeometryType::MULTILINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiLineString)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractLineStringsFromMultiLineString')
            ->with($multiLineString)
            ->willReturn([$lineString1, $lineString2])
        ;

        $extractor
            ->expects($this->exactly(2))
            ->method('extractPointsFromLineString')
            ->withConsecutive([$lineString1], [$lineString2])
            ->willReturnOnConsecutiveCalls(
                [$point1, $point2, $point3],
                [$point4, $point5],
            )
        ;

        $extractor
            ->expects($this->exactly(5))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6),

                $this->coords(-1, -2),
                $this->coords(-3, -4),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0000000005000000020000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000000000000200000002bff0000000000000c000000000000000c008000000000000c010000000000000', $generator->generate($multiLineString));
    }

    public function testMultiLineStringEmpty(): void
    {
        $multiLineString = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiLineString)
            ->willReturn(GeometryType::MULTILINESTRING)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiLineString)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractLineStringsFromMultiLineString')
            ->with($multiLineString)
            ->willReturn([])
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000500000000', $generator->generate($multiLineString));
    }

    public function testMultiPolygon(): void
    {
        $multiPolygon = new stdClass();
        $polygon1 = new stdClass();
        $polygon2 = new stdClass();
        $polygon3 = new stdClass();
        $lineString1 = new stdClass();
        $lineString2 = new stdClass();
        $lineString3 = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();
        $point3 = new stdClass();
        $point4 = new stdClass();
        $point5 = new stdClass();
        $point6 = new stdClass();
        $point7 = new stdClass();
        $point8 = new stdClass();
        $point9 = new stdClass();
        $point10 = new stdClass();
        $point11 = new stdClass();
        $point12 = new stdClass();
        $point13 = new stdClass();
        $point14 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($multiPolygon)
            ->willReturn(GeometryType::MULTIPOLYGON)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($multiPolygon)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPolygonsFromMultiPolygon')
            ->with($multiPolygon)
            ->willReturn([$polygon1, $polygon2, $polygon3])
        ;

        $extractor
            ->expects($this->exactly(3))
            ->method('extractLineStringsFromPolygon')
            ->withConsecutive([$polygon1], [$polygon2], [$polygon3])
            ->willReturnOnConsecutiveCalls(
                [$lineString1, $lineString2],
                [],
                [$lineString3]
            )
        ;

        $extractor
            ->expects($this->exactly(3))
            ->method('extractPointsFromLineString')
            ->withConsecutive([$lineString1], [$lineString2], [$lineString3])
            ->willReturnOnConsecutiveCalls(
                [$point1, $point2, $point3, $point4, $point5],
                [$point6, $point7, $point8, $point9],
                [$point10, $point11, $point12, $point13, $point14],
            )
        ;

        $extractor
            ->expects($this->exactly(14))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(0, 0),
                $this->coords(10, 0),
                $this->coords(10, 10),
                $this->coords(0, 10),
                $this->coords(0, 0),

                $this->coords(1, 1),
                $this->coords(2, 2),
                $this->coords(3, 1),
                $this->coords(1, 1),

                $this->coords(20, 20),
                $this->coords(30, 20),
                $this->coords(30, 30),
                $this->coords(20, 30),
                $this->coords(20, 20),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000600000003000000000300000002000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000000000043ff00000000000003ff00000000000004000000000000000400000000000000040080000000000003ff00000000000003ff00000000000003ff00000000000000000000003000000000000000003000000010000000540340000000000004034000000000000403e0000000000004034000000000000403e000000000000403e0000000000004034000000000000403e00000000000040340000000000004034000000000000', $generator->generate($multiPolygon));
    }

    public function testGeometryCollection(): void
    {
        $geometryCollection = new stdClass();
        $point = new stdClass();
        $lineString = new stdClass();
        $point1 = new stdClass();
        $point2 = new stdClass();
        $point3 = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->exactly(3))
            ->method('extractType')
            ->withConsecutive(
                [$geometryCollection],
                [$point],
                [$lineString],
            )
            ->willReturnOnConsecutiveCalls(
                GeometryType::GEOMETRYCOLLECTION,
                GeometryType::POINT,
                GeometryType::LINESTRING,
            )
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($geometryCollection)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractGeometriesFromGeometryCollection')
            ->with($geometryCollection)
            ->willReturn([$point, $lineString])
        ;

        $extractor
            ->expects($this->once())
            ->method('extractPointsFromLineString')
            ->with($lineString)
            ->willReturn([$point1, $point2, $point3])
        ;

        $extractor
            ->expects($this->exactly(4))
            ->method('extractCoordinatesFromPoint')
            ->willReturnOnConsecutiveCalls(
                $this->coords(-1, -2),

                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6),
            )
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('0000000007000000020000000001bff0000000000000c0000000000000000000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($geometryCollection));
    }

    public function testGeometryCollectonEmpty(): void
    {
        $geometryCollection = new stdClass();

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($geometryCollection)
            ->willReturn(GeometryType::GEOMETRYCOLLECTION)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($geometryCollection)
            ->willReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractGeometriesFromGeometryCollection')
            ->with($geometryCollection)
            ->willReturn([])
        ;

        $generator = new Generator($extractor, [
            'hex' => true,
            'little_endian' => false,
        ]);
        $this->assertSame('000000000700000000', $generator->generate($geometryCollection));
    }

    public function testConstructorShouldThrowExceptionForInvalidFormatOption(): void
    {
        $this->expectException(InvalidOptionException::class);

        new Generator($this->createMock(Extractor::class), [
            'format' => 'foo',
        ]);
    }

    public function testGenerateShouldCatchExtractorExceptions(): void
    {
        $this->expectException(GeneratorException::class);

        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->willThrowException(new Exception())
        ;

        $generator = new Generator($extractor);
        $generator->generate(new stdClass());
    }

    private function coords(
        float $x,
        float $y,
        ?float $z = null,
        ?float $m = null,
    ): Coordinates {
        return new Coordinates(
            x: $x,
            y: $y,
            z: $z,
            m: $m,
        );
    }

    private function getPointExtractorMock(
        object $point,
        Dimension $dimension,
        Coordinates $coords,
        int $srid = null
    ): Extractor {
        $extractor = $this->createMock(Extractor::class);

        $extractor
            ->expects($this->once())
            ->method('extractType')
            ->with($point)
            ->willReturn(GeometryType::POINT)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractDimension')
            ->with($point)
            ->willReturn($dimension)
        ;

        $extractor
            ->expects($this->once())
            ->method('extractCoordinatesFromPoint')
            ->with($point)
            ->willReturn($coords)
        ;

        if ($srid) {
            $extractor
                ->expects($this->once())
                ->method('extractSrid')
                ->with($point)
                ->willReturn($srid)
            ;
        }

        return $extractor;
    }
}
