<?php

namespace GeoIO\WKB\Generator;

use GeoIO\Dimension;
use GeoIO\Extractor;
use Mockery;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testPointXdrHex()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointXdrBinary()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, array(
            'hex' => false,
            'little_endian' => false
        ));
        $this->assertSame(pack('H*', '00000000013ff00000000000004000000000000000'), $generator->generate($point));
    }

    public function testPointNdr()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => true
        ));
        $this->assertSame('0101000000000000000000f03f0000000000000040', $generator->generate($point));
    }

    public function testPointEwkb()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointEwkbWith()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2),
            1000
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'emit_srid' => true,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0020000001000003e83ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointEwkbZ()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DZ,
            $this->coords(1, 2, 3)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00800000013ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointEwkbM()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DM,
            $this->coords(1, 2, null, 3)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00400000013ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointEwkbZM()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_4D,
            $this->coords(1, 2, 3, 4)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00c00000013ff0000000000000400000000000000040080000000000004010000000000000', $generator->generate($point));
    }

    public function testPointWkb12()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_2D,
            $this->coords(1, 2)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000000013ff00000000000004000000000000000', $generator->generate($point));
    }

    public function testPointWkb12Z()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DZ,
            $this->coords(1, 2, 3)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000003e93ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointWkb12M()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_3DM,
            $this->coords(1, 2, null, 3)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000007d13ff000000000000040000000000000004008000000000000', $generator->generate($point));
    }

    public function testPointWkb12ZM()
    {
        $point = new \stdClass();

        $extractor = $this->getPointExtractorMock(
            $point,
            Dimension::DIMENSION_4D,
            $this->coords(1, 2, 3, 4)
        );

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0000000bb93ff0000000000000400000000000000040080000000000004010000000000000', $generator->generate($point));
    }

    public function testLineString()
    {
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();
        $point3 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($lineString)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2, $point3))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(3)
            ->andReturn(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringEmpty()
    {
        $lineString = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($lineString)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array())
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000200000000', $generator->generate($lineString));
    }

    public function testLineStringEwkbZ()
    {
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($lineString)
            ->andReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(2)
            ->andReturn(
                $this->coords(1, 2, 3),
                $this->coords(4, 5, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0080000002000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringEwkbZWithSrid()
    {
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractSrid')
            ->once()
            ->with($lineString)
            ->andReturn(1000)
        ;

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($lineString)
            ->andReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(2)
            ->andReturn(
                $this->coords(1, 2, 3),
                $this->coords(4, 5, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'emit_srid' => true,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00a0000002000003e8000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testLineStringWkb12M()
    {
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($lineString)
            ->andReturn(Dimension::DIMENSION_3DM)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(2)
            ->andReturn(
                $this->coords(1, 2, null, 3),
                $this->coords(4, 5, null, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_WKB12,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000007d2000000023ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($lineString));
    }

    public function testPolygon()
    {
        $polygon = new \stdClass();
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();
        $point3 = new \stdClass();
        $point4 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($polygon)
            ->andReturn(Extractor::TYPE_POLYGON)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($polygon)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromPolygon')
            ->once()
            ->with($polygon)
            ->andReturn(array($lineString))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2, $point3, $point4))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(4)
            ->andReturn(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(6, 5),
                $this->coords(1, 2)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000300000001000000043ff0000000000000400000000000000040080000000000004010000000000000401800000000000040140000000000003ff00000000000004000000000000000', $generator->generate($polygon));
    }

    public function testPolygonEmpty()
    {
        $polygon = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($polygon)
            ->andReturn(Extractor::TYPE_POLYGON)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($polygon)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromPolygon')
            ->once()
            ->with($polygon)
            ->andReturn(array())
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000300000000', $generator->generate($polygon));
    }

    public function testMultiPoint()
    {
        $multiPoint = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiPoint)
            ->andReturn(Extractor::TYPE_MULTIPOINT)
        ;

        $extractor
            ->shouldReceive('extractType')
            ->atLeast(1)
            ->with(Mockery::any())
            ->andReturn(Extractor::TYPE_POINT)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($multiPoint)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->atLeast(1)
            ->with(Mockery::any())
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractPointsFromMultiPoint')
            ->once()
            ->with($multiPoint)
            ->andReturn(array($point1, $point2))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(2)
            ->andReturn(
                $this->coords(1, 2),
                $this->coords(3, 4)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00000000040000000200000000013ff00000000000004000000000000000000000000140080000000000004010000000000000', $generator->generate($multiPoint));
    }

    public function testMultiPointEwkbZ()
    {
        $multiPoint = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiPoint)
            ->andReturn(Extractor::TYPE_MULTIPOINT)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($multiPoint)
            ->andReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->shouldReceive('extractType')
            ->atLeast(1)
            ->with(Mockery::any())
            ->andReturn(Extractor::TYPE_POINT)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->atLeast(1)
            ->with(Mockery::any())
            ->andReturn(Dimension::DIMENSION_3DZ)
        ;

        $extractor
            ->shouldReceive('extractPointsFromMultiPoint')
            ->once()
            ->with($multiPoint)
            ->andReturn(array($point1, $point2))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(2)
            ->andReturn(
                $this->coords(1, 2, 5),
                $this->coords(3, 4, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'format' => Generator::FORMAT_EWKB,
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('00800000040000000200800000013ff0000000000000400000000000000040140000000000000080000001400800000000000040100000000000004018000000000000', $generator->generate($multiPoint));
    }

    public function testMultiPointEmpty()
    {
        $multiPoint = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiPoint)
            ->andReturn(Extractor::TYPE_MULTIPOINT)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($multiPoint)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractPointsFromMultiPoint')
            ->once()
            ->with($multiPoint)
            ->andReturn(array())
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000400000000', $generator->generate($multiPoint));
    }

    public function testMultiLineString()
    {
        $multiLineString = new \stdClass();
        $lineString1 = new \stdClass();
        $lineString2 = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();
        $point3 = new \stdClass();
        $point4 = new \stdClass();
        $point5 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiLineString)
            ->andReturn(Extractor::TYPE_MULTILINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->atLeast(1)
            ->with(Mockery::any())
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromMultiLineString')
            ->once()
            ->with($multiLineString)
            ->andReturn(array($lineString1, $lineString2))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString1)
            ->andReturn(array($point1, $point2, $point3))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString2)
            ->andReturn(array($point4, $point5))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(5)
            ->andReturn(
                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6),

                $this->coords(-1, -2),
                $this->coords(-3, -4)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0000000005000000020000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000000000000200000002bff0000000000000c000000000000000c008000000000000c010000000000000', $generator->generate($multiLineString));
    }

    public function testMultiLineStringEmpty()
    {
        $multiLineString = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiLineString)
            ->andReturn(Extractor::TYPE_MULTILINESTRING)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($multiLineString)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromMultiLineString')
            ->once()
            ->with($multiLineString)
            ->andReturn(array())
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000500000000', $generator->generate($multiLineString));
    }

    public function testMultiPolygon()
    {
        $multiPolygon = new \stdClass();
        $polygon1 = new \stdClass();
        $polygon2 = new \stdClass();
        $polygon3 = new \stdClass();
        $lineString1 = new \stdClass();
        $lineString2 = new \stdClass();
        $lineString3 = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();
        $point3 = new \stdClass();
        $point4 = new \stdClass();
        $point5 = new \stdClass();
        $point6 = new \stdClass();
        $point7 = new \stdClass();
        $point8 = new \stdClass();
        $point9 = new \stdClass();
        $point10 = new \stdClass();
        $point11 = new \stdClass();
        $point12 = new \stdClass();
        $point13 = new \stdClass();
        $point14 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($multiPolygon)
            ->andReturn(Extractor::TYPE_MULTIPOLYGON)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($multiPolygon)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractPolygonsFromMultiPolygon')
            ->once()
            ->with($multiPolygon)
            ->andReturn(array($polygon1, $polygon2, $polygon3))
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromPolygon')
            ->once()
            ->with($polygon1)
            ->andReturn(array($lineString1, $lineString2))
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromPolygon')
            ->once()
            ->with($polygon2)
            ->andReturn(array())
        ;

        $extractor
            ->shouldReceive('extractLineStringsFromPolygon')
            ->once()
            ->with($polygon3)
            ->andReturn(array($lineString3))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString1)
            ->andReturn(array($point1, $point2, $point3, $point4, $point5))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString2)
            ->andReturn(array($point6, $point7, $point8, $point9))
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString3)
            ->andReturn(array($point10, $point11, $point12, $point13, $point14))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(14)
            ->andReturn(
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
                $this->coords(20, 20)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000600000003000000000300000002000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000000000043ff00000000000003ff00000000000004000000000000000400000000000000040080000000000003ff00000000000003ff00000000000003ff00000000000000000000003000000000000000003000000010000000540340000000000004034000000000000403e0000000000004034000000000000403e000000000000403e0000000000004034000000000000403e00000000000040340000000000004034000000000000', $generator->generate($multiPolygon));
    }

    public function testGeometryCollecton()
    {
        $geometryCollection = new \stdClass();
        $point = new \stdClass();
        $lineString = new \stdClass();
        $point1 = new \stdClass();
        $point2 = new \stdClass();
        $point3 = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($geometryCollection)
            ->andReturn(Extractor::TYPE_GEOMETRYCOLLECTION)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($geometryCollection)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractGeometriesFromGeometryCollection')
            ->once()
            ->with($geometryCollection)
            ->andReturn(array($point, $lineString))
        ;

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($point)
            ->andReturn(Extractor::TYPE_POINT)
        ;

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($lineString)
            ->andReturn(Extractor::TYPE_LINESTRING)
        ;

        $extractor
            ->shouldReceive('extractPointsFromLineString')
            ->once()
            ->with($lineString)
            ->andReturn(array($point1, $point2, $point3))
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->times(4)
            ->andReturn(
                $this->coords(-1, -2),

                $this->coords(1, 2),
                $this->coords(3, 4),
                $this->coords(5, 6)
            )
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('0000000007000000020000000001bff0000000000000c0000000000000000000000002000000033ff000000000000040000000000000004008000000000000401000000000000040140000000000004018000000000000', $generator->generate($geometryCollection));
    }

    public function testGeometryCollectonEmpty()
    {
        $geometryCollection = new \stdClass();

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($geometryCollection)
            ->andReturn(Extractor::TYPE_GEOMETRYCOLLECTION)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($geometryCollection)
            ->andReturn(Dimension::DIMENSION_2D)
        ;

        $extractor
            ->shouldReceive('extractGeometriesFromGeometryCollection')
            ->once()
            ->with($geometryCollection)
            ->andReturn(array())
        ;

        $generator = new Generator($extractor, array(
            'hex' => true,
            'little_endian' => false
        ));
        $this->assertSame('000000000700000000', $generator->generate($geometryCollection));
    }

    public function testConstructorShouldThrowExceptionForInvalidFormatOption()
    {
        $this->setExpectedException('GeoIO\\WKB\Generator\\Exception\\InvalidOptionException');

        new Generator($extractor = Mockery::mock('GeoIO\\Extractor'), array(
            'format' => 'foo'
        ));
    }

    public function testGenerateShouldCatchExtractorExceptions()
    {
        $this->setExpectedException('GeoIO\\WKB\Generator\\Exception\\GeneratorException');

        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor->shouldIgnoreMissing();

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->andThrow(new \Exception())
        ;

        $generator = new Generator($extractor);
        $generator->generate('foo');
    }

    protected function coords($x, $y, $z = null, $m = null)
    {
        return array(
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'm' => $m
        );
    }

    protected function getPointExtractorMock($point, $dimension, $coords, $srid = null)
    {
        $extractor = Mockery::mock('GeoIO\\Extractor');

        $extractor
            ->shouldReceive('extractType')
            ->once()
            ->with($point)
            ->andReturn(Extractor::TYPE_POINT)
        ;

        $extractor
            ->shouldReceive('extractDimension')
            ->once()
            ->with($point)
            ->andReturn($dimension)
        ;

        $extractor
            ->shouldReceive('extractCoordinatesFromPoint')
            ->once()
            ->with($point)
            ->andReturn($coords)
        ;

        if ($srid) {
            $extractor
                ->shouldReceive('extractSrid')
                ->once()
                ->with($point)
                ->andReturn($srid)
            ;
        }

        return $extractor;
    }
}
