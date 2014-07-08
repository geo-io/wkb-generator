Geo I/O WKB Generator
=====================

[![Build Status](https://travis-ci.org/geo-io/wkb-generator.png?branch=master)](https://travis-ci.org/geo-io/wkb-generator)
[![Coverage Status](https://img.shields.io/coveralls/geo-io/wkb-generator.svg)](https://coveralls.io/r/geo-io/wkb-generator)

Generates [Well-known binary (WKB)](http://en.wikipedia.org/wiki/Well-known_text#Well-known_binary)
representations from geometric objects.

```php
class MyExtractor implements GeoIO\Extractor
{
    public function extractType($geometry)
    {
        if ($geometry instanceof MyPoint) {
            return self::TYPE_POINT;
        }

        // ...
    }

    public function extractCoordinatesFromPoint($point)
    {
        return array(
            'x' => $point->getX(),
            'y' => $point->getY(),
            'z' => null,
            'm' => null,
        );
    }

    // ...
}

$extractor = MyExtractor();
$generator = new GeoIO\WKB\Generator($extractor, array(
    'hex' => true
));

echo $generator->generate(new MyPoint(1, 2));
// Outputs:
// 0101000000000000000000f03f0000000000000040
```

Installation
------------

Install [through composer](http://getcomposer.org). Check the
[packagist page](https://packagist.org/packages/geo-io/wkb-generator) for all
available versions.

```json
{
    "require": {
        "geo-io/wkb-generator": "0.1.*@dev"
    }
}
```

License
-------

Geo I/O WKB Generator is released under the [MIT License](LICENSE).
