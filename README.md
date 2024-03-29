Geo I/O WKB Generator
=====================

[![Build Status](https://github.com/geo-io/wkb-generator/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/geo-io/wkb-generator/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/geo-io/wkb-generator/badge.svg?branch=main)](https://coveralls.io/github/geo-io/wkb-generator?branch=main)

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
$generator = new GeoIO\WKB\Generator\Generator($extractor, array(
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

```bash
composer require geo-io/wkb-generator
```

License
-------

Copyright (c) 2014-2022 Jan Sorgalla. Released under the [MIT License](LICENSE).
