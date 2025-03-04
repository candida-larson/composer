<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test\Json;

use Composer\Json\JsonManipulator;
use Composer\Test\TestCase;

class JsonManipulatorTest extends TestCase
{
    /**
     * @dataProvider linkProvider
     * @param string $json
     * @param string $type
     * @param string $package
     * @param string $constraint
     * @param string $expected
     */
    public function testAddLink($json, $type, $package, $constraint, $expected)
    {
        $manipulator = new JsonManipulator($json);
        $this->assertTrue($manipulator->addLink($type, $package, $constraint));
        $this->assertEquals($expected, $manipulator->getContents());
    }

    public function linkProvider()
    {
        return array(
            array(
                '{}',
                'require',
                'vendor/baz',
                'qux',
                "{\n".
"    \"require\": {\n".
"        \"vendor/baz\": \"qux\"\n".
"    }\n".
"}\n",
            ),
            array(
                '{
    "foo": "bar"
}',
                'require',
                'vendor/baz',
                'qux',
                '{
    "foo": "bar",
    "require": {
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require": {
    }
}',
                'require',
                'vendor/baz',
                'qux',
                '{
    "require": {
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "empty": "",
    "require": {
        "foo": "bar"
    }
}',
                'require',
                'vendor/baz',
                'qux',
                '{
    "empty": "",
    "require": {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "baz"
    }
}',
                'require',
                'vendor/baz',
                'qux',
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),

            array(
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "baz"
    }
}',
                'require',
                'vEnDoR/bAz',
                'qux',
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require":
    {
        "foo": "bar",
        "vendor\/baz": "baz"
    }
}',
                'require',
                'vendor/baz',
                'qux',
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require":
    {
        "foo": "bar",
        "vendor\/baz": "baz"
    }
}',
                'require',
                'vEnDoR/bAz',
                'qux',
                '{
    "require":
    {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require": {
        "foo": "bar"
    },
    "repositories": [{
        "type": "package",
        "package": {
            "require": {
                "foo": "bar"
            }
        }
    }]
}',
                'require',
                'foo',
                'qux',
                '{
    "require": {
        "foo": "qux"
    },
    "repositories": [{
        "type": "package",
        "package": {
            "require": {
                "foo": "bar"
            }
        }
    }]
}
',
            ),
            array(
                '{
    "repositories": [{
        "type": "package",
        "package": {
            "require": {
                "foo": "bar"
            }
        }
    }]
}',
                'require',
                'foo',
                'qux',
                '{
    "repositories": [{
        "type": "package",
        "package": {
            "require": {
                "foo": "bar"
            }
        }
    }],
    "require": {
        "foo": "qux"
    }
}
',
            ),
            array(
                '{
    "require": {
        "php": "5.*"
    }
}',
                'require-dev',
                'foo',
                'qux',
                '{
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "qux"
    }
}
',
            ),
            array(
                '{
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "bar"
    }
}',
                'require-dev',
                'foo',
                'qux',
                '{
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "qux"
    }
}
',
            ),
            array(
                '{
    "repositories": [{
        "type": "package",
        "package": {
            "bar": "ba[z",
            "dist": {
                "url": "http...",
                "type": "zip"
            },
            "autoload": {
                "classmap": [ "foo/bar" ]
            }
        }
    }],
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "bar"
    }
}',
                'require-dev',
                'foo',
                'qux',
                '{
    "repositories": [{
        "type": "package",
        "package": {
            "bar": "ba[z",
            "dist": {
                "url": "http...",
                "type": "zip"
            },
            "autoload": {
                "classmap": [ "foo/bar" ]
            }
        }
    }],
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "qux"
    }
}
',
            ),
            array(
                '{
    "config": {
        "cache-files-ttl": 0,
        "discard-changes": true
    },
    "minimum-stability": "stable",
    "prefer-stable": false,
    "provide": {
        "heroku-sys/cedar": "14.2016.03.22"
    },
    "repositories": [
        {
            "packagist.org": false
        },
        {
            "type": "package",
            "package": [
                {
                    "type": "metapackage",
                    "name": "anthonymartin/geo-location",
                    "version": "v1.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "aws/aws-sdk-php",
                    "version": "3.9.4",
                    "require": {
                        "heroku-sys/php": ">=5.5"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "cloudinary/cloudinary_php",
                    "version": "dev-master",
                    "require": {
                        "heroku-sys/ext-curl": "*",
                        "heroku-sys/ext-json": "*",
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/annotations",
                    "version": "v1.2.7",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/cache",
                    "version": "v1.6.0",
                    "require": {
                        "heroku-sys/php": "~5.5|~7.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/collections",
                    "version": "v1.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/common",
                    "version": "v2.6.1",
                    "require": {
                        "heroku-sys/php": "~5.5|~7.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/inflector",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/lexer",
                    "version": "v1.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "geoip/geoip",
                    "version": "v1.16",
                    "require": [],
                    "replace": [],
                    "provide": [],
                    "conflict": {
                        "heroku-sys/ext-geoip": "*"
                    }
                },
                {
                    "type": "metapackage",
                    "name": "giggsey/libphonenumber-for-php",
                    "version": "7.2.5",
                    "require": {
                        "heroku-sys/ext-mbstring": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/guzzle",
                    "version": "5.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/promises",
                    "version": "1.0.3",
                    "require": {
                        "heroku-sys/php": ">=5.5.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/psr7",
                    "version": "1.2.3",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/ringphp",
                    "version": "1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/streams",
                    "version": "3.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "hipchat/hipchat-php",
                    "version": "v1.4",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "kriswallsmith/buzz",
                    "version": "v0.15",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "league/csv",
                    "version": "8.0.0",
                    "require": {
                        "heroku-sys/ext-mbstring": "*",
                        "heroku-sys/php": ">=5.5.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "league/fractal",
                    "version": "0.13.0",
                    "require": {
                        "heroku-sys/php": ">=5.4"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "mashape/unirest-php",
                    "version": "1.2.1",
                    "require": {
                        "heroku-sys/ext-curl": "*",
                        "heroku-sys/ext-json": "*",
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "mtdowling/jmespath.php",
                    "version": "2.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "palex/phpstructureddata",
                    "version": "v2.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "psr/http-message",
                    "version": "1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "react/promise",
                    "version": "v2.2.1",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "rollbar/rollbar",
                    "version": "v0.15.0",
                    "require": {
                        "heroku-sys/ext-curl": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "ronanguilloux/isocodes",
                    "version": "1.2.0",
                    "require": {
                        "heroku-sys/ext-bcmath": "*",
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "sendgrid/sendgrid",
                    "version": "2.1.1",
                    "require": {
                        "heroku-sys/php": ">=5.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "sendgrid/smtpapi",
                    "version": "0.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/css-selector",
                    "version": "v2.8.2",
                    "require": {
                        "heroku-sys/php": ">=5.3.9"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/http-foundation",
                    "version": "v2.8.2",
                    "require": {
                        "heroku-sys/php": ">=5.3.9"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/polyfill-php54",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/polyfill-php55",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "thepixeldeveloper/sitemap",
                    "version": "3.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "tijsverkoyen/css-to-inline-styles",
                    "version": "1.5.5",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "yiisoft/yii",
                    "version": "1.1.17",
                    "require": {
                        "heroku-sys/php": ">=5.1.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "composer.json/composer.lock",
                    "version": "dev-597511d6d51b96e4a8afeba2c79982e5",
                    "require": {
                        "heroku-sys/php": "~5.6.0",
                        "heroku-sys/ext-newrelic": "*",
                        "heroku-sys/ext-gd": "*",
                        "heroku-sys/ext-redis": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                }
            ]
        }
    ],
    "require": {
        "composer.json/composer.lock": "dev-597511d6d51b96e4a8afeba2c79982e5",
        "anthonymartin/geo-location": "v1.0.0",
        "aws/aws-sdk-php": "3.9.4",
        "cloudinary/cloudinary_php": "dev-master",
        "doctrine/annotations": "v1.2.7",
        "doctrine/cache": "v1.6.0",
        "doctrine/collections": "v1.3.0",
        "doctrine/common": "v2.6.1",
        "doctrine/inflector": "v1.1.0",
        "doctrine/lexer": "v1.0.1",
        "geoip/geoip": "v1.16",
        "giggsey/libphonenumber-for-php": "7.2.5",
        "guzzlehttp/guzzle": "5.3.0",
        "guzzlehttp/promises": "1.0.3",
        "guzzlehttp/psr7": "1.2.3",
        "guzzlehttp/ringphp": "1.1.0",
        "guzzlehttp/streams": "3.0.0",
        "hipchat/hipchat-php": "v1.4",
        "kriswallsmith/buzz": "v0.15",
        "league/csv": "8.0.0",
        "league/fractal": "0.13.0",
        "mashape/unirest-php": "1.2.1",
        "mtdowling/jmespath.php": "2.3.0",
        "palex/phpstructureddata": "v2.0.1",
        "psr/http-message": "1.0",
        "react/promise": "v2.2.1",
        "rollbar/rollbar": "v0.15.0",
        "ronanguilloux/isocodes": "1.2.0",
        "sendgrid/sendgrid": "2.1.1",
        "sendgrid/smtpapi": "0.0.1",
        "symfony/css-selector": "v2.8.2",
        "symfony/http-foundation": "v2.8.2",
        "symfony/polyfill-php54": "v1.1.0",
        "symfony/polyfill-php55": "v1.1.0",
        "thepixeldeveloper/sitemap": "3.0.0",
        "tijsverkoyen/css-to-inline-styles": "1.5.5",
        "yiisoft/yii": "1.1.17",
        "heroku-sys/apache": "^2.4.10",
        "heroku-sys/nginx": "~1.8.0"
    }
}',
                'require',
                'foo',
                'qux',
                '{
    "config": {
        "cache-files-ttl": 0,
        "discard-changes": true
    },
    "minimum-stability": "stable",
    "prefer-stable": false,
    "provide": {
        "heroku-sys/cedar": "14.2016.03.22"
    },
    "repositories": [
        {
            "packagist.org": false
        },
        {
            "type": "package",
            "package": [
                {
                    "type": "metapackage",
                    "name": "anthonymartin/geo-location",
                    "version": "v1.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "aws/aws-sdk-php",
                    "version": "3.9.4",
                    "require": {
                        "heroku-sys/php": ">=5.5"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "cloudinary/cloudinary_php",
                    "version": "dev-master",
                    "require": {
                        "heroku-sys/ext-curl": "*",
                        "heroku-sys/ext-json": "*",
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/annotations",
                    "version": "v1.2.7",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/cache",
                    "version": "v1.6.0",
                    "require": {
                        "heroku-sys/php": "~5.5|~7.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/collections",
                    "version": "v1.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/common",
                    "version": "v2.6.1",
                    "require": {
                        "heroku-sys/php": "~5.5|~7.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/inflector",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "doctrine/lexer",
                    "version": "v1.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3.2"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "geoip/geoip",
                    "version": "v1.16",
                    "require": [],
                    "replace": [],
                    "provide": [],
                    "conflict": {
                        "heroku-sys/ext-geoip": "*"
                    }
                },
                {
                    "type": "metapackage",
                    "name": "giggsey/libphonenumber-for-php",
                    "version": "7.2.5",
                    "require": {
                        "heroku-sys/ext-mbstring": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/guzzle",
                    "version": "5.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/promises",
                    "version": "1.0.3",
                    "require": {
                        "heroku-sys/php": ">=5.5.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/psr7",
                    "version": "1.2.3",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/ringphp",
                    "version": "1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "guzzlehttp/streams",
                    "version": "3.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "hipchat/hipchat-php",
                    "version": "v1.4",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "kriswallsmith/buzz",
                    "version": "v0.15",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "league/csv",
                    "version": "8.0.0",
                    "require": {
                        "heroku-sys/ext-mbstring": "*",
                        "heroku-sys/php": ">=5.5.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "league/fractal",
                    "version": "0.13.0",
                    "require": {
                        "heroku-sys/php": ">=5.4"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "mashape/unirest-php",
                    "version": "1.2.1",
                    "require": {
                        "heroku-sys/ext-curl": "*",
                        "heroku-sys/ext-json": "*",
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "mtdowling/jmespath.php",
                    "version": "2.3.0",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "palex/phpstructureddata",
                    "version": "v2.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "psr/http-message",
                    "version": "1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "react/promise",
                    "version": "v2.2.1",
                    "require": {
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "rollbar/rollbar",
                    "version": "v0.15.0",
                    "require": {
                        "heroku-sys/ext-curl": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "ronanguilloux/isocodes",
                    "version": "1.2.0",
                    "require": {
                        "heroku-sys/ext-bcmath": "*",
                        "heroku-sys/php": ">=5.4.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "sendgrid/sendgrid",
                    "version": "2.1.1",
                    "require": {
                        "heroku-sys/php": ">=5.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "sendgrid/smtpapi",
                    "version": "0.0.1",
                    "require": {
                        "heroku-sys/php": ">=5.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/css-selector",
                    "version": "v2.8.2",
                    "require": {
                        "heroku-sys/php": ">=5.3.9"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/http-foundation",
                    "version": "v2.8.2",
                    "require": {
                        "heroku-sys/php": ">=5.3.9"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/polyfill-php54",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "symfony/polyfill-php55",
                    "version": "v1.1.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.3"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "thepixeldeveloper/sitemap",
                    "version": "3.0.0",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "tijsverkoyen/css-to-inline-styles",
                    "version": "1.5.5",
                    "require": {
                        "heroku-sys/php": ">=5.3.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "yiisoft/yii",
                    "version": "1.1.17",
                    "require": {
                        "heroku-sys/php": ">=5.1.0"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                },
                {
                    "type": "metapackage",
                    "name": "composer.json/composer.lock",
                    "version": "dev-597511d6d51b96e4a8afeba2c79982e5",
                    "require": {
                        "heroku-sys/php": "~5.6.0",
                        "heroku-sys/ext-newrelic": "*",
                        "heroku-sys/ext-gd": "*",
                        "heroku-sys/ext-redis": "*"
                    },
                    "replace": [],
                    "provide": [],
                    "conflict": []
                }
            ]
        }
    ],
    "require": {
        "composer.json/composer.lock": "dev-597511d6d51b96e4a8afeba2c79982e5",
        "anthonymartin/geo-location": "v1.0.0",
        "aws/aws-sdk-php": "3.9.4",
        "cloudinary/cloudinary_php": "dev-master",
        "doctrine/annotations": "v1.2.7",
        "doctrine/cache": "v1.6.0",
        "doctrine/collections": "v1.3.0",
        "doctrine/common": "v2.6.1",
        "doctrine/inflector": "v1.1.0",
        "doctrine/lexer": "v1.0.1",
        "geoip/geoip": "v1.16",
        "giggsey/libphonenumber-for-php": "7.2.5",
        "guzzlehttp/guzzle": "5.3.0",
        "guzzlehttp/promises": "1.0.3",
        "guzzlehttp/psr7": "1.2.3",
        "guzzlehttp/ringphp": "1.1.0",
        "guzzlehttp/streams": "3.0.0",
        "hipchat/hipchat-php": "v1.4",
        "kriswallsmith/buzz": "v0.15",
        "league/csv": "8.0.0",
        "league/fractal": "0.13.0",
        "mashape/unirest-php": "1.2.1",
        "mtdowling/jmespath.php": "2.3.0",
        "palex/phpstructureddata": "v2.0.1",
        "psr/http-message": "1.0",
        "react/promise": "v2.2.1",
        "rollbar/rollbar": "v0.15.0",
        "ronanguilloux/isocodes": "1.2.0",
        "sendgrid/sendgrid": "2.1.1",
        "sendgrid/smtpapi": "0.0.1",
        "symfony/css-selector": "v2.8.2",
        "symfony/http-foundation": "v2.8.2",
        "symfony/polyfill-php54": "v1.1.0",
        "symfony/polyfill-php55": "v1.1.0",
        "thepixeldeveloper/sitemap": "3.0.0",
        "tijsverkoyen/css-to-inline-styles": "1.5.5",
        "yiisoft/yii": "1.1.17",
        "heroku-sys/apache": "^2.4.10",
        "heroku-sys/nginx": "~1.8.0",
        "foo": "qux"
    }
}
',
            ),
        );
    }

    /**
     * @dataProvider providerAddLinkAndSortPackages
     * @param string $json
     * @param string $type
     * @param string $package
     * @param string $constraint
     * @param bool $sortPackages
     * @param string $expected
     */
    public function testAddLinkAndSortPackages($json, $type, $package, $constraint, $sortPackages, $expected)
    {
        $manipulator = new JsonManipulator($json);
        $this->assertTrue($manipulator->addLink($type, $package, $constraint, $sortPackages));
        $this->assertEquals($expected, $manipulator->getContents());
    }

    public function providerAddLinkAndSortPackages()
    {
        return array(
            array(
                '{
    "require": {
        "vendor/baz": "qux"
    }
}',
                'require',
                'foo',
                'bar',
                true,
                '{
    "require": {
        "foo": "bar",
        "vendor/baz": "qux"
    }
}
',
            ),
            array(
                '{
    "require": {
        "vendor/baz": "qux"
    }
}',
                'require',
                'foo',
                'bar',
                false,
                '{
    "require": {
        "vendor/baz": "qux",
        "foo": "bar"
    }
}
',
            ),
            array(
                '{
    "require": {
        "foo": "baz",
        "ext-10gd": "*",
        "ext-2mcrypt": "*",
        "lib-foo": "*",
        "hhvm": "*",
        "php": ">=5.5"
    }
}',
                'require',
                'igorw/retry',
                '*',
                true,
                '{
    "require": {
        "php": ">=5.5",
        "hhvm": "*",
        "ext-2mcrypt": "*",
        "ext-10gd": "*",
        "lib-foo": "*",
        "foo": "baz",
        "igorw/retry": "*"
    }
}
',
            ),
        );
    }

    /**
     * @dataProvider removeSubNodeProvider
     * @param string $json
     * @param string $name
     * @param string $expected
     * @param ?string $expectedContent
     */
    public function testRemoveSubNode($json, $name, $expected, $expectedContent = null)
    {
        $manipulator = new JsonManipulator($json);

        $this->assertEquals($expected, $manipulator->removeSubNode('repositories', $name));
        if (null !== $expectedContent) {
            $this->assertEquals($expectedContent, $manipulator->getContents());
        }
    }

    public function removeSubNodeProvider()
    {
        return array(
            'works on simple ones first' => array(
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        },
        "bar": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'foo',
                true,
                '{
    "repositories": {
        "bar": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}
',
            ),
            'works on simple ones last' => array(
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        },
        "bar": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'bar',
                true,
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}
',
            ),
            'works on simple ones unique' => array(
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'foo',
                true,
                '{
    "repositories": {
    }
}
',
            ),
            'works on simple ones escaped slash' => array(
                '{
    "repositories": {
        "foo\/bar": {
            "bar": "baz"
        }
    }
}',
                'foo/bar',
                true,
                '{
    "repositories": {
    }
}
',
            ),
            'works on simple ones middle' => array(
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        },
        "bar": {
            "foo": "bar",
            "bar": "baz"
        },
        "baz": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'bar',
                true,
                '{
    "repositories": {
        "foo": {
            "foo": "bar",
            "bar": "baz"
        },
        "baz": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}
',
            ),
            'works on undefined ones' => array(
                '{
    "repositories": {
        "main": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'removenotthere',
                true,
                '{
    "repositories": {
        "main": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}
',
            ),
            'works on child having unmatched name' => array(
                '{
    "repositories": {
        "baz": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'bar',
                true,
                '{
    "repositories": {
        "baz": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}
',
            ),
            'works on child having duplicate name' => array(
                '{
    "repositories": {
        "foo": {
            "baz": "qux"
        },
        "baz": {
            "foo": "bar",
            "bar": "baz"
        }
    }
}',
                'baz',
                true,
                '{
    "repositories": {
        "foo": {
            "baz": "qux"
        }
    }
}
',
            ),
            'works on empty repos' => array(
                '{
    "repositories": {
    }
}',
                'bar',
                true,
            ),
            'works on empty repos2' => array(
                '{
    "repositories": {}
}',
                'bar',
                true,
            ),
            'works on missing repos' => array(
                "{\n}",
                'bar',
                true,
            ),
            'works on deep repos' => array(
                '{
    "repositories": {
        "foo": {
            "package": { "bar": "baz" }
        }
    }
}',
                'foo',
                true,
                '{
    "repositories": {
    }
}
',
            ),
            'works on deep repos with borked texts' => array(
                '{
    "repositories": {
        "foo": {
            "package": { "bar": "ba{z" }
        }
    }
}',
                'bar',
                true,
                '{
    "repositories": {
        "foo": {
            "package": { "bar": "ba{z" }
        }
    }
}
',

                '{
}
',
            ),
            'works on deep repos with borked texts2' => array(
                '{
    "repositories": {
        "foo": {
            "package": { "bar": "ba}z" }
        }
    }
}',
                'bar',
                true,
                '{
    "repositories": {
        "foo": {
            "package": { "bar": "ba}z" }
        }
    }
}
',

                '{
}
',
            ),
            'fails on deep arrays with borked texts' => array(
                '{
    "repositories": [
        {
            "package": { "bar": "ba[z" }
        }
    ]
}',
                'bar',
                false,
            ),
            'fails on deep arrays with borked texts2' => array(
                '{
    "repositories": [
        {
            "package": { "bar": "ba]z" }
        }
    ]
}',
                'bar',
                false,
            ),
        );
    }

    public function testRemoveSubNodeFromRequire()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "package": {
                "require": {
                    "this/should-not-end-up-in-root-require": "~2.0"
                },
                "require-dev": {
                    "this/should-not-end-up-in-root-require-dev": "~2.0"
                }
            }
        }
    ],
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "require-dev": {
        "package/d": "*"
    }
}');

        $this->assertTrue($manipulator->removeSubNode('require', 'package/c'));
        $this->assertTrue($manipulator->removeSubNode('require-dev', 'package/d'));
        $this->assertEquals('{
    "repositories": [
        {
            "package": {
                "require": {
                    "this/should-not-end-up-in-root-require": "~2.0"
                },
                "require-dev": {
                    "this/should-not-end-up-in-root-require-dev": "~2.0"
                }
            }
        }
    ],
    "require": {
        "package/a": "*",
        "package/b": "*"
    },
    "require-dev": {
    }
}
', $manipulator->getContents());
    }

    public function testAddSubNodeInRequire()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "package": {
                "require": {
                    "this/should-not-end-up-in-root-require": "~2.0"
                },
                "require-dev": {
                    "this/should-not-end-up-in-root-require-dev": "~2.0"
                }
            }
        }
    ],
    "require": {
        "package/a": "*",
        "package/b": "*"
    },
    "require-dev": {
        "package/d": "*"
    }
}');

        $this->assertTrue($manipulator->addSubNode('require', 'package/c', '*'));
        $this->assertTrue($manipulator->addSubNode('require-dev', 'package/e', '*'));
        $this->assertEquals('{
    "repositories": [
        {
            "package": {
                "require": {
                    "this/should-not-end-up-in-root-require": "~2.0"
                },
                "require-dev": {
                    "this/should-not-end-up-in-root-require-dev": "~2.0"
                }
            }
        }
    ],
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "require-dev": {
        "package/d": "*",
        "package/e": "*"
    }
}
', $manipulator->getContents());
    }

    public function testAddExtraWithPackage()
    {
        //$this->markTestSkipped();
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "extra": {
        "auto-append-gitignore": true
    }
}');

        $this->assertTrue($manipulator->addProperty('extra.foo-bar', true));
        $this->assertEquals('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "extra": {
        "auto-append-gitignore": true,
        "foo-bar": true
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigWithPackage()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "config": {
        "platform": {
            "php": "5.3.9"
        }
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('preferred-install.my-organization/stable-package', 'dist'));
        $this->assertEquals('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "config": {
        "platform": {
            "php": "5.3.9"
        },
        "preferred-install": {
            "my-organization/stable-package": "dist"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddSuggestWithPackage()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "suggest": {
        "package": "Description"
    }
}');

        $this->assertTrue($manipulator->addProperty('suggest.new-package', 'new-description'));
        $this->assertEquals('{
    "repositories": [
        {
            "type": "package",
            "package": {
                "authors": [],
                "extra": {
                    "package-xml": "package.xml"
                }
            }
        }
    ],
    "suggest": {
        "package": "Description",
        "new-package": "new-description"
    }
}
', $manipulator->getContents());
    }

    public function testAddRepositoryCanInitializeEmptyRepositories()
    {
        $manipulator = new JsonManipulator('{
  "repositories": {
  }
}');

        $this->assertTrue($manipulator->addRepository('bar', array('type' => 'composer')));
        $this->assertEquals('{
  "repositories": {
    "bar": {
      "type": "composer"
    }
  }
}
', $manipulator->getContents());
    }

    public function testAddRepositoryCanInitializeFromScratch()
    {
        $manipulator = new JsonManipulator("{
\t\"a\": \"b\"
}");

        $this->assertTrue($manipulator->addRepository('bar2', array('type' => 'composer')));
        $this->assertEquals("{
\t\"a\": \"b\",
\t\"repositories\": {
\t\t\"bar2\": {
\t\t\t\"type\": \"composer\"
\t\t}
\t}
}
", $manipulator->getContents());
    }

    public function testAddRepositoryCanAppend()
    {
        $manipulator = new JsonManipulator('{
    "repositories": {
        "foo": {
            "type": "vcs",
            "url": "lala"
        }
    }
}');

        $this->assertTrue($manipulator->addRepository('bar', array('type' => 'composer'), true));
        $this->assertEquals('{
    "repositories": {
        "foo": {
            "type": "vcs",
            "url": "lala"
        },
        "bar": {
            "type": "composer"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddRepositoryCanPrepend()
    {
        $manipulator = new JsonManipulator('{
    "repositories": {
        "foo": {
            "type": "vcs",
            "url": "lala"
        }
    }
}');

        $this->assertTrue($manipulator->addRepository('bar', array('type' => 'composer'), false));
        $this->assertEquals('{
    "repositories": {
        "bar": {
            "type": "composer"
        },
        "foo": {
            "type": "vcs",
            "url": "lala"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddRepositoryCanOverrideDeepRepos()
    {
        $manipulator = new JsonManipulator('{
    "repositories": {
        "baz": {
            "type": "package",
            "package": {}
        }
    }
}');

        $this->assertTrue($manipulator->addRepository('baz', array('type' => 'composer')));
        $this->assertEquals('{
    "repositories": {
        "baz": {
            "type": "composer"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingEscapes()
    {
        $manipulator = new JsonManipulator('{
    "config": {
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('test', 'a\b'));
        $this->assertTrue($manipulator->addConfigSetting('test2', "a\nb\fa"));
        $this->assertEquals('{
    "config": {
        "test": "a\\\\b",
        "test2": "a\nb\fa"
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingWorksFromScratch()
    {
        $manipulator = new JsonManipulator('{
}');

        $this->assertTrue($manipulator->addConfigSetting('foo.bar', 'baz'));
        $this->assertEquals('{
    "config": {
        "foo": {
            "bar": "baz"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanAdd()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "foo": "bar"
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('bar', 'baz'));
        $this->assertEquals('{
    "config": {
        "foo": "bar",
        "bar": "baz"
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanOverwrite()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "foo": "bar",
        "bar": "baz"
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('foo', 'zomg'));
        $this->assertEquals('{
    "config": {
        "foo": "zomg",
        "bar": "baz"
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanOverwriteNumbers()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "foo": 500
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('foo', 50));
        $this->assertEquals('{
    "config": {
        "foo": 50
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanOverwriteArrays()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "github-oauth": {
            "github.com": "foo"
        },
        "github-protocols": ["https"]
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('github-protocols', array('https', 'http')));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "github.com": "foo"
        },
        "github-protocols": ["https", "http"]
    }
}
', $manipulator->getContents());

        $this->assertTrue($manipulator->addConfigSetting('github-oauth', array('github.com' => 'bar', 'alt.example.org' => 'baz')));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "github.com": "bar",
            "alt.example.org": "baz"
        },
        "github-protocols": ["https", "http"]
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanAddSubKeyInEmptyConfig()
    {
        $manipulator = new JsonManipulator('{
    "config": {
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('github-oauth.bar', 'baz'));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "bar": "baz"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanAddSubKeyInEmptyVal()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "github-oauth": {},
        "github-oauth2": {
        }
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('github-oauth.bar', 'baz'));
        $this->assertTrue($manipulator->addConfigSetting('github-oauth2.a.bar', 'baz2'));
        $this->assertTrue($manipulator->addConfigSetting('github-oauth3.b', 'c'));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "bar": "baz"
        },
        "github-oauth2": {
            "a.bar": "baz2"
        },
        "github-oauth3": {
            "b": "c"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddConfigSettingCanAddSubKeyInHash()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "github-oauth": {
            "github.com": "foo"
        }
    }
}');

        $this->assertTrue($manipulator->addConfigSetting('github-oauth.bar', 'baz'));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "github.com": "foo",
            "bar": "baz"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddRootSettingDoesNotBreakDots()
    {
        $manipulator = new JsonManipulator('{
    "github-oauth": {
        "github.com": "foo"
    }
}');

        $this->assertTrue($manipulator->addSubNode('github-oauth', 'bar', 'baz'));
        $this->assertEquals('{
    "github-oauth": {
        "github.com": "foo",
        "bar": "baz"
    }
}
', $manipulator->getContents());
    }

    public function testRemoveConfigSettingCanRemoveSubKeyInHash()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "github-oauth": {
            "github.com": "foo",
            "bar": "baz"
        }
    }
}');

        $this->assertTrue($manipulator->removeConfigSetting('github-oauth.bar'));
        $this->assertEquals('{
    "config": {
        "github-oauth": {
            "github.com": "foo"
        }
    }
}
', $manipulator->getContents());
    }

    public function testRemoveConfigSettingCanRemoveSubKeyInHashWithSiblings()
    {
        $manipulator = new JsonManipulator('{
    "config": {
        "foo": "bar",
        "github-oauth": {
            "github.com": "foo",
            "bar": "baz"
        }
    }
}');

        $this->assertTrue($manipulator->removeConfigSetting('github-oauth.bar'));
        $this->assertEquals('{
    "config": {
        "foo": "bar",
        "github-oauth": {
            "github.com": "foo"
        }
    }
}
', $manipulator->getContents());
    }

    public function testAddMainKey()
    {
        $manipulator = new JsonManipulator('{
    "foo": "bar"
}');

        $this->assertTrue($manipulator->addMainKey('bar', 'baz'));
        $this->assertEquals('{
    "foo": "bar",
    "bar": "baz"
}
', $manipulator->getContents());
    }

    public function testAddMainKeyWithContentHavingDollarSignFollowedByDigit()
    {
        $manipulator = new JsonManipulator('{
    "foo": "bar"
}');

        $this->assertTrue($manipulator->addMainKey('bar', '$1baz'));
        $this->assertEquals('{
    "foo": "bar",
    "bar": "$1baz"
}
', $manipulator->getContents());
    }

    public function testAddMainKeyWithContentHavingDollarSignFollowedByDigit2()
    {
        $manipulator = new JsonManipulator('{}');

        $this->assertTrue($manipulator->addMainKey('foo', '$1bar'));
        $this->assertEquals('{
    "foo": "$1bar"
}
', $manipulator->getContents());
    }

    public function testUpdateMainKey()
    {
        $manipulator = new JsonManipulator('{
    "foo": "bar"
}');

        $this->assertTrue($manipulator->addMainKey('foo', 'baz'));
        $this->assertEquals('{
    "foo": "baz"
}
', $manipulator->getContents());
    }

    public function testUpdateMainKey2()
    {
        $manipulator = new JsonManipulator('{
    "a": {
        "foo": "bar",
        "baz": "qux"
    },
    "foo": "bar",
    "baz": "bar"
}');

        $this->assertTrue($manipulator->addMainKey('foo', 'baz'));
        $this->assertTrue($manipulator->addMainKey('baz', 'quux'));
        $this->assertEquals('{
    "a": {
        "foo": "bar",
        "baz": "qux"
    },
    "foo": "baz",
    "baz": "quux"
}
', $manipulator->getContents());
    }

    public function testUpdateMainKey3()
    {
        $manipulator = new JsonManipulator('{
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "bar"
    }
}');

        $this->assertTrue($manipulator->addMainKey('require-dev', array('foo' => 'qux')));
        $this->assertEquals('{
    "require": {
        "php": "5.*"
    },
    "require-dev": {
        "foo": "qux"
    }
}
', $manipulator->getContents());
    }

    public function testUpdateMainKeyWithContentHavingDollarSignFollowedByDigit()
    {
        $manipulator = new JsonManipulator('{
    "foo": "bar"
}');

        $this->assertTrue($manipulator->addMainKey('foo', '$1bar'));
        $this->assertEquals('{
    "foo": "$1bar"
}
', $manipulator->getContents());
    }

    public function testRemoveMainKey()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
        {
            "package": {
                "require": {
                    "this/should-not-end-up-in-root-require": "~2.0"
                },
                "require-dev": {
                    "this/should-not-end-up-in-root-require-dev": "~2.0"
                }
            }
        }
    ],
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "foo": "bar",
    "require-dev": {
        "package/d": "*"
    }
}');

        $this->assertTrue($manipulator->removeMainKey('repositories'));
        $this->assertEquals('{
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "foo": "bar",
    "require-dev": {
        "package/d": "*"
    }
}
', $manipulator->getContents());

        $this->assertTrue($manipulator->removeMainKey('foo'));
        $this->assertEquals('{
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "require-dev": {
        "package/d": "*"
    }
}
', $manipulator->getContents());

        $this->assertTrue($manipulator->removeMainKey('require'));
        $this->assertTrue($manipulator->removeMainKey('require-dev'));
        $this->assertEquals('{
}
', $manipulator->getContents());
    }

    public function testRemoveMainKeyIfEmpty()
    {
        $manipulator = new JsonManipulator('{
    "repositories": [
    ],
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "foo": "bar",
    "require-dev": {
    }
}');

        $this->assertTrue($manipulator->removeMainKeyIfEmpty('repositories'));
        $this->assertEquals('{
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "foo": "bar",
    "require-dev": {
    }
}
', $manipulator->getContents());

        $this->assertTrue($manipulator->removeMainKeyIfEmpty('foo'));
        $this->assertTrue($manipulator->removeMainKeyIfEmpty('require'));
        $this->assertTrue($manipulator->removeMainKeyIfEmpty('require-dev'));
        $this->assertEquals('{
    "require": {
        "package/a": "*",
        "package/b": "*",
        "package/c": "*"
    },
    "foo": "bar"
}
', $manipulator->getContents());
    }

    public function testRemoveMainKeyRemovesKeyWhereValueIsNull()
    {
        $manipulator = new JsonManipulator(json_encode(array(
            'foo' => 9000,
            'bar' => null,
        )));

        $manipulator->removeMainKey('bar');

        $expected = json_encode(array(
            'foo' => 9000,
        ));

        $this->assertJsonStringEqualsJsonString($expected, $manipulator->getContents());
    }

    public function testIndentDetection()
    {
        $manipulator = new JsonManipulator('{

  "require": {
    "php": "5.*"
  }
}');

        $this->assertTrue($manipulator->addMainKey('require-dev', array('foo' => 'qux')));
        $this->assertEquals('{

  "require": {
    "php": "5.*"
  },
  "require-dev": {
    "foo": "qux"
  }
}
', $manipulator->getContents());
    }

    public function testRemoveMainKeyAtEndOfFile()
    {
        $manipulator = new JsonManipulator('{
    "require": {
        "package/a": "*"
    }
}
');
        $this->assertTrue($manipulator->addMainKey('homepage', 'http...'));
        $this->assertTrue($manipulator->addMainKey('license', 'mit'));
        $this->assertEquals('{
    "require": {
        "package/a": "*"
    },
    "homepage": "http...",
    "license": "mit"
}
', $manipulator->getContents());

        $this->assertTrue($manipulator->removeMainKey('homepage'));
        $this->assertTrue($manipulator->removeMainKey('license'));
        $this->assertEquals('{
    "require": {
        "package/a": "*"
    }
}
', $manipulator->getContents());
    }

    public function testEscapedUnicodeDoesNotCauseBacktrackLimitErrorGithubIssue8131()
    {
        $manipulator = new JsonManipulator('{
  "description": "Some U\u00F1icode",
  "require": {
    "foo/bar": "^1.0"
  }
}');

        $this->assertTrue($manipulator->addLink('require', 'foo/baz', '^1.0'));
        $this->assertEquals('{
  "description": "Some U\u00F1icode",
  "require": {
    "foo/bar": "^1.0",
    "foo/baz": "^1.0"
  }
}
', $manipulator->getContents());
    }

    public function testLargeFileDoesNotCauseBacktrackLimitErrorGithubIssue9595()
    {
        $manipulator = new JsonManipulator('{
    "name": "leoloso/pop",
    "require": {
        "php": "^7.4|^8.0",
        "ext-mbstring": "*",
        "brain/cortex": "~1.0.0",
        "composer/installers": "~1.0",
        "composer/semver": "^1.5",
        "erusev/parsedown": "^1.7",
        "guzzlehttp/guzzle": "~6.3",
        "jrfnl/php-cast-to-type": "^2.0",
        "league/pipeline": "^1.0",
        "lkwdwrd/wp-muplugin-loader": "dev-feature-composer-v2",
        "obsidian/polyfill-hrtime": "^0.1",
        "psr/cache": "^1.0",
        "symfony/cache": "^5.1",
        "symfony/config": "^5.1",
        "symfony/dependency-injection": "^5.1",
        "symfony/dotenv": "^5.1",
        "symfony/expression-language": "^5.1",
        "symfony/polyfill-php72": "^1.18",
        "symfony/polyfill-php73": "^1.18",
        "symfony/polyfill-php74": "^1.18",
        "symfony/polyfill-php80": "^1.18",
        "symfony/property-access": "^5.1",
        "symfony/yaml": "^5.1"
    },
    "require-dev": {
        "johnpbloch/wordpress": ">=5.5",
        "phpstan/phpstan": "^0.12",
        "phpunit/phpunit": ">=9.3",
        "rector/rector": "^0.9",
        "squizlabs/php_codesniffer": "^3.0",
        "symfony/var-dumper": "^5.1",
        "symplify/monorepo-builder": "^9.0",
        "szepeviktor/phpstan-wordpress": "^0.6.2"
    },
    "autoload": {
        "psr-4": {
            "GraphQLAPI\\\\ConvertCaseDirectives\\\\": "layers/GraphQLAPIForWP/plugins/convert-case-directives/src",
            "GraphQLAPI\\\\GraphQLAPI\\\\": "layers/GraphQLAPIForWP/plugins/graphql-api-for-wp/src",
            "GraphQLAPI\\\\SchemaFeedback\\\\": "layers/GraphQLAPIForWP/plugins/schema-feedback/src",
            "GraphQLByPoP\\\\GraphQLClientsForWP\\\\": "layers/GraphQLByPoP/packages/graphql-clients-for-wp/src",
            "GraphQLByPoP\\\\GraphQLEndpointForWP\\\\": "layers/GraphQLByPoP/packages/graphql-endpoint-for-wp/src",
            "GraphQLByPoP\\\\GraphQLParser\\\\": "layers/GraphQLByPoP/packages/graphql-parser/src",
            "GraphQLByPoP\\\\GraphQLQuery\\\\": "layers/GraphQLByPoP/packages/graphql-query/src",
            "GraphQLByPoP\\\\GraphQLRequest\\\\": "layers/GraphQLByPoP/packages/graphql-request/src",
            "GraphQLByPoP\\\\GraphQLServer\\\\": "layers/GraphQLByPoP/packages/graphql-server/src",
            "Leoloso\\\\ExamplesForPoP\\\\": "layers/Misc/packages/examples-for-pop/src",
            "PoPSchema\\\\BasicDirectives\\\\": "layers/Schema/packages/basic-directives/src",
            "PoPSchema\\\\BlockMetadataWP\\\\": "layers/Schema/packages/block-metadata-for-wp/src",
            "PoPSchema\\\\CDNDirective\\\\": "layers/Schema/packages/cdn-directive/src",
            "PoPSchema\\\\CategoriesWP\\\\": "layers/Schema/packages/categories-wp/src",
            "PoPSchema\\\\Categories\\\\": "layers/Schema/packages/categories/src",
            "PoPSchema\\\\CommentMetaWP\\\\": "layers/Schema/packages/commentmeta-wp/src",
            "PoPSchema\\\\CommentMeta\\\\": "layers/Schema/packages/commentmeta/src",
            "PoPSchema\\\\CommentMutationsWP\\\\": "layers/Schema/packages/comment-mutations-wp/src",
            "PoPSchema\\\\CommentMutations\\\\": "layers/Schema/packages/comment-mutations/src",
            "PoPSchema\\\\CommentsWP\\\\": "layers/Schema/packages/comments-wp/src",
            "PoPSchema\\\\Comments\\\\": "layers/Schema/packages/comments/src",
            "PoPSchema\\\\ConvertCaseDirectives\\\\": "layers/Schema/packages/convert-case-directives/src",
            "PoPSchema\\\\CustomPostMediaMutationsWP\\\\": "layers/Schema/packages/custompostmedia-mutations-wp/src",
            "PoPSchema\\\\CustomPostMediaMutations\\\\": "layers/Schema/packages/custompostmedia-mutations/src",
            "PoPSchema\\\\CustomPostMediaWP\\\\": "layers/Schema/packages/custompostmedia-wp/src",
            "PoPSchema\\\\CustomPostMedia\\\\": "layers/Schema/packages/custompostmedia/src",
            "PoPSchema\\\\CustomPostMetaWP\\\\": "layers/Schema/packages/custompostmeta-wp/src",
            "PoPSchema\\\\CustomPostMeta\\\\": "layers/Schema/packages/custompostmeta/src",
            "PoPSchema\\\\CustomPostMutationsWP\\\\": "layers/Schema/packages/custompost-mutations-wp/src",
            "PoPSchema\\\\CustomPostMutations\\\\": "layers/Schema/packages/custompost-mutations/src",
            "PoPSchema\\\\CustomPostsWP\\\\": "layers/Schema/packages/customposts-wp/src",
            "PoPSchema\\\\CustomPosts\\\\": "layers/Schema/packages/customposts/src",
            "PoPSchema\\\\EventMutationsWPEM\\\\": "layers/Schema/packages/event-mutations-wp-em/src",
            "PoPSchema\\\\EventMutations\\\\": "layers/Schema/packages/event-mutations/src",
            "PoPSchema\\\\EventsWPEM\\\\": "layers/Schema/packages/events-wp-em/src",
            "PoPSchema\\\\Events\\\\": "layers/Schema/packages/events/src",
            "PoPSchema\\\\EverythingElseWP\\\\": "layers/Schema/packages/everythingelse-wp/src",
            "PoPSchema\\\\EverythingElse\\\\": "layers/Schema/packages/everythingelse/src",
            "PoPSchema\\\\GenericCustomPosts\\\\": "layers/Schema/packages/generic-customposts/src",
            "PoPSchema\\\\GoogleTranslateDirectiveForCustomPosts\\\\": "layers/Schema/packages/google-translate-directive-for-customposts/src",
            "PoPSchema\\\\GoogleTranslateDirective\\\\": "layers/Schema/packages/google-translate-directive/src",
            "PoPSchema\\\\HighlightsWP\\\\": "layers/Schema/packages/highlights-wp/src",
            "PoPSchema\\\\Highlights\\\\": "layers/Schema/packages/highlights/src",
            "PoPSchema\\\\LocationPostsWP\\\\": "layers/Schema/packages/locationposts-wp/src",
            "PoPSchema\\\\LocationPosts\\\\": "layers/Schema/packages/locationposts/src",
            "PoPSchema\\\\LocationsWPEM\\\\": "layers/Schema/packages/locations-wp-em/src",
            "PoPSchema\\\\Locations\\\\": "layers/Schema/packages/locations/src",
            "PoPSchema\\\\MediaWP\\\\": "layers/Schema/packages/media-wp/src",
            "PoPSchema\\\\Media\\\\": "layers/Schema/packages/media/src",
            "PoPSchema\\\\MenusWP\\\\": "layers/Schema/packages/menus-wp/src",
            "PoPSchema\\\\Menus\\\\": "layers/Schema/packages/menus/src",
            "PoPSchema\\\\MetaQueryWP\\\\": "layers/Schema/packages/metaquery-wp/src",
            "PoPSchema\\\\MetaQuery\\\\": "layers/Schema/packages/metaquery/src",
            "PoPSchema\\\\Meta\\\\": "layers/Schema/packages/meta/src",
            "PoPSchema\\\\NotificationsWP\\\\": "layers/Schema/packages/notifications-wp/src",
            "PoPSchema\\\\Notifications\\\\": "layers/Schema/packages/notifications/src",
            "PoPSchema\\\\PagesWP\\\\": "layers/Schema/packages/pages-wp/src",
            "PoPSchema\\\\Pages\\\\": "layers/Schema/packages/pages/src",
            "PoPSchema\\\\PostMutations\\\\": "layers/Schema/packages/post-mutations/src",
            "PoPSchema\\\\PostTagsWP\\\\": "layers/Schema/packages/post-tags-wp/src",
            "PoPSchema\\\\PostTags\\\\": "layers/Schema/packages/post-tags/src",
            "PoPSchema\\\\PostsWP\\\\": "layers/Schema/packages/posts-wp/src",
            "PoPSchema\\\\Posts\\\\": "layers/Schema/packages/posts/src",
            "PoPSchema\\\\QueriedObjectWP\\\\": "layers/Schema/packages/queriedobject-wp/src",
            "PoPSchema\\\\QueriedObject\\\\": "layers/Schema/packages/queriedobject/src",
            "PoPSchema\\\\SchemaCommons\\\\": "layers/Schema/packages/schema-commons/src",
            "PoPSchema\\\\StancesWP\\\\": "layers/Schema/packages/stances-wp/src",
            "PoPSchema\\\\Stances\\\\": "layers/Schema/packages/stances/src",
            "PoPSchema\\\\TagsWP\\\\": "layers/Schema/packages/tags-wp/src",
            "PoPSchema\\\\Tags\\\\": "layers/Schema/packages/tags/src",
            "PoPSchema\\\\TaxonomiesWP\\\\": "layers/Schema/packages/taxonomies-wp/src",
            "PoPSchema\\\\Taxonomies\\\\": "layers/Schema/packages/taxonomies/src",
            "PoPSchema\\\\TaxonomyMetaWP\\\\": "layers/Schema/packages/taxonomymeta-wp/src",
            "PoPSchema\\\\TaxonomyMeta\\\\": "layers/Schema/packages/taxonomymeta/src",
            "PoPSchema\\\\TaxonomyQueryWP\\\\": "layers/Schema/packages/taxonomyquery-wp/src",
            "PoPSchema\\\\TaxonomyQuery\\\\": "layers/Schema/packages/taxonomyquery/src",
            "PoPSchema\\\\TranslateDirectiveACL\\\\": "layers/Schema/packages/translate-directive-acl/src",
            "PoPSchema\\\\TranslateDirective\\\\": "layers/Schema/packages/translate-directive/src",
            "PoPSchema\\\\UserMetaWP\\\\": "layers/Schema/packages/usermeta-wp/src",
            "PoPSchema\\\\UserMeta\\\\": "layers/Schema/packages/usermeta/src",
            "PoPSchema\\\\UserRolesACL\\\\": "layers/Schema/packages/user-roles-acl/src",
            "PoPSchema\\\\UserRolesAccessControl\\\\": "layers/Schema/packages/user-roles-access-control/src",
            "PoPSchema\\\\UserRolesWP\\\\": "layers/Schema/packages/user-roles-wp/src",
            "PoPSchema\\\\UserRoles\\\\": "layers/Schema/packages/user-roles/src",
            "PoPSchema\\\\UserStateAccessControl\\\\": "layers/Schema/packages/user-state-access-control/src",
            "PoPSchema\\\\UserStateMutationsWP\\\\": "layers/Schema/packages/user-state-mutations-wp/src",
            "PoPSchema\\\\UserStateMutations\\\\": "layers/Schema/packages/user-state-mutations/src",
            "PoPSchema\\\\UserStateWP\\\\": "layers/Schema/packages/user-state-wp/src",
            "PoPSchema\\\\UserState\\\\": "layers/Schema/packages/user-state/src",
            "PoPSchema\\\\UsersWP\\\\": "layers/Schema/packages/users-wp/src",
            "PoPSchema\\\\Users\\\\": "layers/Schema/packages/users/src",
            "PoPSitesWassup\\\\CommentMutations\\\\": "layers/Wassup/packages/comment-mutations/src",
            "PoPSitesWassup\\\\ContactUsMutations\\\\": "layers/Wassup/packages/contactus-mutations/src",
            "PoPSitesWassup\\\\ContactUserMutations\\\\": "layers/Wassup/packages/contactuser-mutations/src",
            "PoPSitesWassup\\\\CustomPostLinkMutations\\\\": "layers/Wassup/packages/custompostlink-mutations/src",
            "PoPSitesWassup\\\\CustomPostMutations\\\\": "layers/Wassup/packages/custompost-mutations/src",
            "PoPSitesWassup\\\\EventLinkMutations\\\\": "layers/Wassup/packages/eventlink-mutations/src",
            "PoPSitesWassup\\\\EventMutations\\\\": "layers/Wassup/packages/event-mutations/src",
            "PoPSitesWassup\\\\EverythingElseMutations\\\\": "layers/Wassup/packages/everythingelse-mutations/src",
            "PoPSitesWassup\\\\FlagMutations\\\\": "layers/Wassup/packages/flag-mutations/src",
            "PoPSitesWassup\\\\FormMutations\\\\": "layers/Wassup/packages/form-mutations/src",
            "PoPSitesWassup\\\\GravityFormsMutations\\\\": "layers/Wassup/packages/gravityforms-mutations/src",
            "PoPSitesWassup\\\\HighlightMutations\\\\": "layers/Wassup/packages/highlight-mutations/src",
            "PoPSitesWassup\\\\LocationMutations\\\\": "layers/Wassup/packages/location-mutations/src",
            "PoPSitesWassup\\\\LocationPostLinkMutations\\\\": "layers/Wassup/packages/locationpostlink-mutations/src",
            "PoPSitesWassup\\\\LocationPostMutations\\\\": "layers/Wassup/packages/locationpost-mutations/src",
            "PoPSitesWassup\\\\NewsletterMutations\\\\": "layers/Wassup/packages/newsletter-mutations/src",
            "PoPSitesWassup\\\\NotificationMutations\\\\": "layers/Wassup/packages/notification-mutations/src",
            "PoPSitesWassup\\\\PostLinkMutations\\\\": "layers/Wassup/packages/postlink-mutations/src",
            "PoPSitesWassup\\\\PostMutations\\\\": "layers/Wassup/packages/post-mutations/src",
            "PoPSitesWassup\\\\ShareMutations\\\\": "layers/Wassup/packages/share-mutations/src",
            "PoPSitesWassup\\\\SocialNetworkMutations\\\\": "layers/Wassup/packages/socialnetwork-mutations/src",
            "PoPSitesWassup\\\\StanceMutations\\\\": "layers/Wassup/packages/stance-mutations/src",
            "PoPSitesWassup\\\\SystemMutations\\\\": "layers/Wassup/packages/system-mutations/src",
            "PoPSitesWassup\\\\UserStateMutations\\\\": "layers/Wassup/packages/user-state-mutations/src",
            "PoPSitesWassup\\\\VolunteerMutations\\\\": "layers/Wassup/packages/volunteer-mutations/src",
            "PoPSitesWassup\\\\Wassup\\\\": "layers/Wassup/packages/wassup/src",
            "PoP\\\\APIClients\\\\": "layers/API/packages/api-clients/src",
            "PoP\\\\APIEndpointsForWP\\\\": "layers/API/packages/api-endpoints-for-wp/src",
            "PoP\\\\APIEndpoints\\\\": "layers/API/packages/api-endpoints/src",
            "PoP\\\\APIMirrorQuery\\\\": "layers/API/packages/api-mirrorquery/src",
            "PoP\\\\API\\\\": "layers/API/packages/api/src",
            "PoP\\\\AccessControl\\\\": "layers/Engine/packages/access-control/src",
            "PoP\\\\ApplicationWP\\\\": "layers/SiteBuilder/packages/application-wp/src",
            "PoP\\\\Application\\\\": "layers/SiteBuilder/packages/application/src",
            "PoP\\\\Base36Definitions\\\\": "layers/SiteBuilder/packages/definitions-base36/src",
            "PoP\\\\CacheControl\\\\": "layers/Engine/packages/cache-control/src",
            "PoP\\\\ComponentModel\\\\": "layers/Engine/packages/component-model/src",
            "PoP\\\\ConfigurableSchemaFeedback\\\\": "layers/Engine/packages/configurable-schema-feedback/src",
            "PoP\\\\ConfigurationComponentModel\\\\": "layers/SiteBuilder/packages/component-model-configuration/src",
            "PoP\\\\DefinitionPersistence\\\\": "layers/SiteBuilder/packages/definitionpersistence/src",
            "PoP\\\\Definitions\\\\": "layers/Engine/packages/definitions/src",
            "PoP\\\\EmojiDefinitions\\\\": "layers/SiteBuilder/packages/definitions-emoji/src",
            "PoP\\\\EngineWP\\\\": "layers/Engine/packages/engine-wp/src",
            "PoP\\\\Engine\\\\": "layers/Engine/packages/engine/src",
            "PoP\\\\FieldQuery\\\\": "layers/Engine/packages/field-query/src",
            "PoP\\\\FileStore\\\\": "layers/Engine/packages/filestore/src",
            "PoP\\\\FunctionFields\\\\": "layers/Engine/packages/function-fields/src",
            "PoP\\\\GraphQLAPI\\\\": "layers/API/packages/api-graphql/src",
            "PoP\\\\GuzzleHelpers\\\\": "layers/Engine/packages/guzzle-helpers/src",
            "PoP\\\\HooksWP\\\\": "layers/Engine/packages/hooks-wp/src",
            "PoP\\\\Hooks\\\\": "layers/Engine/packages/hooks/src",
            "PoP\\\\LooseContracts\\\\": "layers/Engine/packages/loosecontracts/src",
            "PoP\\\\MandatoryDirectivesByConfiguration\\\\": "layers/Engine/packages/mandatory-directives-by-configuration/src",
            "PoP\\\\ModuleRouting\\\\": "layers/Engine/packages/modulerouting/src",
            "PoP\\\\Multisite\\\\": "layers/SiteBuilder/packages/multisite/src",
            "PoP\\\\PoP\\\\": "src",
            "PoP\\\\QueryParsing\\\\": "layers/Engine/packages/query-parsing/src",
            "PoP\\\\RESTAPI\\\\": "layers/API/packages/api-rest/src",
            "PoP\\\\ResourceLoader\\\\": "layers/SiteBuilder/packages/resourceloader/src",
            "PoP\\\\Resources\\\\": "layers/SiteBuilder/packages/resources/src",
            "PoP\\\\Root\\\\": "layers/Engine/packages/root/src",
            "PoP\\\\RoutingWP\\\\": "layers/Engine/packages/routing-wp/src",
            "PoP\\\\Routing\\\\": "layers/Engine/packages/routing/src",
            "PoP\\\\SPA\\\\": "layers/SiteBuilder/packages/spa/src",
            "PoP\\\\SSG\\\\": "layers/SiteBuilder/packages/static-site-generator/src",
            "PoP\\\\SiteWP\\\\": "layers/SiteBuilder/packages/site-wp/src",
            "PoP\\\\Site\\\\": "layers/SiteBuilder/packages/site/src",
            "PoP\\\\TraceTools\\\\": "layers/Engine/packages/trace-tools/src",
            "PoP\\\\TranslationWP\\\\": "layers/Engine/packages/translation-wp/src",
            "PoP\\\\Translation\\\\": "layers/Engine/packages/translation/src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "GraphQLAPI\\\\ConvertCaseDirectives\\\\": "layers/GraphQLAPIForWP/plugins/convert-case-directives/tests",
            "GraphQLAPI\\\\GraphQLAPI\\\\": "layers/GraphQLAPIForWP/plugins/graphql-api-for-wp/tests",
            "GraphQLAPI\\\\SchemaFeedback\\\\": "layers/GraphQLAPIForWP/plugins/schema-feedback/tests",
            "GraphQLByPoP\\\\GraphQLClientsForWP\\\\": "layers/GraphQLByPoP/packages/graphql-clients-for-wp/tests",
            "GraphQLByPoP\\\\GraphQLEndpointForWP\\\\": "layers/GraphQLByPoP/packages/graphql-endpoint-for-wp/tests",
            "GraphQLByPoP\\\\GraphQLParser\\\\": "layers/GraphQLByPoP/packages/graphql-parser/tests",
            "GraphQLByPoP\\\\GraphQLQuery\\\\": "layers/GraphQLByPoP/packages/graphql-query/tests",
            "GraphQLByPoP\\\\GraphQLRequest\\\\": "layers/GraphQLByPoP/packages/graphql-request/tests",
            "GraphQLByPoP\\\\GraphQLServer\\\\": "layers/GraphQLByPoP/packages/graphql-server/tests",
            "Leoloso\\\\ExamplesForPoP\\\\": "layers/Misc/packages/examples-for-pop/tests",
            "PoPSchema\\\\BasicDirectives\\\\": "layers/Schema/packages/basic-directives/tests",
            "PoPSchema\\\\BlockMetadataWP\\\\": "layers/Schema/packages/block-metadata-for-wp/tests",
            "PoPSchema\\\\CDNDirective\\\\": "layers/Schema/packages/cdn-directive/tests",
            "PoPSchema\\\\CategoriesWP\\\\": "layers/Schema/packages/categories-wp/tests",
            "PoPSchema\\\\Categories\\\\": "layers/Schema/packages/categories/tests",
            "PoPSchema\\\\CommentMetaWP\\\\": "layers/Schema/packages/commentmeta-wp/tests",
            "PoPSchema\\\\CommentMeta\\\\": "layers/Schema/packages/commentmeta/tests",
            "PoPSchema\\\\CommentMutationsWP\\\\": "layers/Schema/packages/comment-mutations-wp/tests",
            "PoPSchema\\\\CommentMutations\\\\": "layers/Schema/packages/comment-mutations/tests",
            "PoPSchema\\\\CommentsWP\\\\": "layers/Schema/packages/comments-wp/tests",
            "PoPSchema\\\\Comments\\\\": "layers/Schema/packages/comments/tests",
            "PoPSchema\\\\ConvertCaseDirectives\\\\": "layers/Schema/packages/convert-case-directives/tests",
            "PoPSchema\\\\CustomPostMediaMutationsWP\\\\": "layers/Schema/packages/custompostmedia-mutations-wp/tests",
            "PoPSchema\\\\CustomPostMediaMutations\\\\": "layers/Schema/packages/custompostmedia-mutations/tests",
            "PoPSchema\\\\CustomPostMediaWP\\\\": "layers/Schema/packages/custompostmedia-wp/tests",
            "PoPSchema\\\\CustomPostMedia\\\\": "layers/Schema/packages/custompostmedia/tests",
            "PoPSchema\\\\CustomPostMetaWP\\\\": "layers/Schema/packages/custompostmeta-wp/tests",
            "PoPSchema\\\\CustomPostMeta\\\\": "layers/Schema/packages/custompostmeta/tests",
            "PoPSchema\\\\CustomPostMutationsWP\\\\": "layers/Schema/packages/custompost-mutations-wp/tests",
            "PoPSchema\\\\CustomPostMutations\\\\": "layers/Schema/packages/custompost-mutations/tests",
            "PoPSchema\\\\CustomPostsWP\\\\": "layers/Schema/packages/customposts-wp/tests",
            "PoPSchema\\\\CustomPosts\\\\": "layers/Schema/packages/customposts/tests",
            "PoPSchema\\\\EventMutationsWPEM\\\\": "layers/Schema/packages/event-mutations-wp-em/tests",
            "PoPSchema\\\\EventMutations\\\\": "layers/Schema/packages/event-mutations/tests",
            "PoPSchema\\\\EventsWPEM\\\\": "layers/Schema/packages/events-wp-em/tests",
            "PoPSchema\\\\Events\\\\": "layers/Schema/packages/events/tests",
            "PoPSchema\\\\EverythingElseWP\\\\": "layers/Schema/packages/everythingelse-wp/tests",
            "PoPSchema\\\\EverythingElse\\\\": "layers/Schema/packages/everythingelse/tests",
            "PoPSchema\\\\GenericCustomPosts\\\\": "layers/Schema/packages/generic-customposts/tests",
            "PoPSchema\\\\GoogleTranslateDirectiveForCustomPosts\\\\": "layers/Schema/packages/google-translate-directive-for-customposts/tests",
            "PoPSchema\\\\GoogleTranslateDirective\\\\": "layers/Schema/packages/google-translate-directive/tests",
            "PoPSchema\\\\HighlightsWP\\\\": "layers/Schema/packages/highlights-wp/tests",
            "PoPSchema\\\\Highlights\\\\": "layers/Schema/packages/highlights/tests",
            "PoPSchema\\\\LocationPostsWP\\\\": "layers/Schema/packages/locationposts-wp/tests",
            "PoPSchema\\\\LocationPosts\\\\": "layers/Schema/packages/locationposts/tests",
            "PoPSchema\\\\LocationsWPEM\\\\": "layers/Schema/packages/locations-wp-em/tests",
            "PoPSchema\\\\Locations\\\\": "layers/Schema/packages/locations/tests",
            "PoPSchema\\\\MediaWP\\\\": "layers/Schema/packages/media-wp/tests",
            "PoPSchema\\\\Media\\\\": "layers/Schema/packages/media/tests",
            "PoPSchema\\\\MenusWP\\\\": "layers/Schema/packages/menus-wp/tests",
            "PoPSchema\\\\Menus\\\\": "layers/Schema/packages/menus/tests",
            "PoPSchema\\\\MetaQueryWP\\\\": "layers/Schema/packages/metaquery-wp/tests",
            "PoPSchema\\\\MetaQuery\\\\": "layers/Schema/packages/metaquery/tests",
            "PoPSchema\\\\Meta\\\\": "layers/Schema/packages/meta/tests",
            "PoPSchema\\\\NotificationsWP\\\\": "layers/Schema/packages/notifications-wp/tests",
            "PoPSchema\\\\Notifications\\\\": "layers/Schema/packages/notifications/tests",
            "PoPSchema\\\\PagesWP\\\\": "layers/Schema/packages/pages-wp/tests",
            "PoPSchema\\\\Pages\\\\": "layers/Schema/packages/pages/tests",
            "PoPSchema\\\\PostMutations\\\\": "layers/Schema/packages/post-mutations/tests",
            "PoPSchema\\\\PostTagsWP\\\\": "layers/Schema/packages/post-tags-wp/tests",
            "PoPSchema\\\\PostTags\\\\": "layers/Schema/packages/post-tags/tests",
            "PoPSchema\\\\PostsWP\\\\": "layers/Schema/packages/posts-wp/tests",
            "PoPSchema\\\\Posts\\\\": "layers/Schema/packages/posts/tests",
            "PoPSchema\\\\QueriedObjectWP\\\\": "layers/Schema/packages/queriedobject-wp/tests",
            "PoPSchema\\\\QueriedObject\\\\": "layers/Schema/packages/queriedobject/tests",
            "PoPSchema\\\\SchemaCommons\\\\": "layers/Schema/packages/schema-commons/tests",
            "PoPSchema\\\\StancesWP\\\\": "layers/Schema/packages/stances-wp/tests",
            "PoPSchema\\\\Stances\\\\": "layers/Schema/packages/stances/tests",
            "PoPSchema\\\\TagsWP\\\\": "layers/Schema/packages/tags-wp/tests",
            "PoPSchema\\\\Tags\\\\": "layers/Schema/packages/tags/tests",
            "PoPSchema\\\\TaxonomiesWP\\\\": "layers/Schema/packages/taxonomies-wp/tests",
            "PoPSchema\\\\Taxonomies\\\\": "layers/Schema/packages/taxonomies/tests",
            "PoPSchema\\\\TaxonomyMetaWP\\\\": "layers/Schema/packages/taxonomymeta-wp/tests",
            "PoPSchema\\\\TaxonomyMeta\\\\": "layers/Schema/packages/taxonomymeta/tests",
            "PoPSchema\\\\TaxonomyQueryWP\\\\": "layers/Schema/packages/taxonomyquery-wp/tests",
            "PoPSchema\\\\TaxonomyQuery\\\\": "layers/Schema/packages/taxonomyquery/tests",
            "PoPSchema\\\\TranslateDirectiveACL\\\\": "layers/Schema/packages/translate-directive-acl/tests",
            "PoPSchema\\\\TranslateDirective\\\\": "layers/Schema/packages/translate-directive/tests",
            "PoPSchema\\\\UserMetaWP\\\\": "layers/Schema/packages/usermeta-wp/tests",
            "PoPSchema\\\\UserMeta\\\\": "layers/Schema/packages/usermeta/tests",
            "PoPSchema\\\\UserRolesACL\\\\": "layers/Schema/packages/user-roles-acl/tests",
            "PoPSchema\\\\UserRolesAccessControl\\\\": "layers/Schema/packages/user-roles-access-control/tests",
            "PoPSchema\\\\UserRolesWP\\\\": "layers/Schema/packages/user-roles-wp/tests",
            "PoPSchema\\\\UserRoles\\\\": "layers/Schema/packages/user-roles/tests",
            "PoPSchema\\\\UserStateAccessControl\\\\": "layers/Schema/packages/user-state-access-control/tests",
            "PoPSchema\\\\UserStateMutationsWP\\\\": "layers/Schema/packages/user-state-mutations-wp/tests",
            "PoPSchema\\\\UserStateMutations\\\\": "layers/Schema/packages/user-state-mutations/tests",
            "PoPSchema\\\\UserStateWP\\\\": "layers/Schema/packages/user-state-wp/tests",
            "PoPSchema\\\\UserState\\\\": "layers/Schema/packages/user-state/tests",
            "PoPSchema\\\\UsersWP\\\\": "layers/Schema/packages/users-wp/tests",
            "PoPSchema\\\\Users\\\\": "layers/Schema/packages/users/tests",
            "PoPSitesWassup\\\\CommentMutations\\\\": "layers/Wassup/packages/comment-mutations/tests",
            "PoPSitesWassup\\\\ContactUsMutations\\\\": "layers/Wassup/packages/contactus-mutations/tests",
            "PoPSitesWassup\\\\ContactUserMutations\\\\": "layers/Wassup/packages/contactuser-mutations/tests",
            "PoPSitesWassup\\\\CustomPostLinkMutations\\\\": "layers/Wassup/packages/custompostlink-mutations/tests",
            "PoPSitesWassup\\\\CustomPostMutations\\\\": "layers/Wassup/packages/custompost-mutations/tests",
            "PoPSitesWassup\\\\EventLinkMutations\\\\": "layers/Wassup/packages/eventlink-mutations/tests",
            "PoPSitesWassup\\\\EventMutations\\\\": "layers/Wassup/packages/event-mutations/tests",
            "PoPSitesWassup\\\\EverythingElseMutations\\\\": "layers/Wassup/packages/everythingelse-mutations/tests",
            "PoPSitesWassup\\\\FlagMutations\\\\": "layers/Wassup/packages/flag-mutations/tests",
            "PoPSitesWassup\\\\FormMutations\\\\": "layers/Wassup/packages/form-mutations/tests",
            "PoPSitesWassup\\\\GravityFormsMutations\\\\": "layers/Wassup/packages/gravityforms-mutations/tests",
            "PoPSitesWassup\\\\HighlightMutations\\\\": "layers/Wassup/packages/highlight-mutations/tests",
            "PoPSitesWassup\\\\LocationMutations\\\\": "layers/Wassup/packages/location-mutations/tests",
            "PoPSitesWassup\\\\LocationPostLinkMutations\\\\": "layers/Wassup/packages/locationpostlink-mutations/tests",
            "PoPSitesWassup\\\\LocationPostMutations\\\\": "layers/Wassup/packages/locationpost-mutations/tests",
            "PoPSitesWassup\\\\NewsletterMutations\\\\": "layers/Wassup/packages/newsletter-mutations/tests",
            "PoPSitesWassup\\\\NotificationMutations\\\\": "layers/Wassup/packages/notification-mutations/tests",
            "PoPSitesWassup\\\\PostLinkMutations\\\\": "layers/Wassup/packages/postlink-mutations/tests",
            "PoPSitesWassup\\\\PostMutations\\\\": "layers/Wassup/packages/post-mutations/tests",
            "PoPSitesWassup\\\\ShareMutations\\\\": "layers/Wassup/packages/share-mutations/tests",
            "PoPSitesWassup\\\\SocialNetworkMutations\\\\": "layers/Wassup/packages/socialnetwork-mutations/tests",
            "PoPSitesWassup\\\\StanceMutations\\\\": "layers/Wassup/packages/stance-mutations/tests",
            "PoPSitesWassup\\\\SystemMutations\\\\": "layers/Wassup/packages/system-mutations/tests",
            "PoPSitesWassup\\\\UserStateMutations\\\\": "layers/Wassup/packages/user-state-mutations/tests",
            "PoPSitesWassup\\\\VolunteerMutations\\\\": "layers/Wassup/packages/volunteer-mutations/tests",
            "PoPSitesWassup\\\\Wassup\\\\": "layers/Wassup/packages/wassup/tests",
            "PoP\\\\APIClients\\\\": "layers/API/packages/api-clients/tests",
            "PoP\\\\APIEndpointsForWP\\\\": "layers/API/packages/api-endpoints-for-wp/tests",
            "PoP\\\\APIEndpoints\\\\": "layers/API/packages/api-endpoints/tests",
            "PoP\\\\APIMirrorQuery\\\\": "layers/API/packages/api-mirrorquery/tests",
            "PoP\\\\API\\\\": "layers/API/packages/api/tests",
            "PoP\\\\AccessControl\\\\": "layers/Engine/packages/access-control/tests",
            "PoP\\\\ApplicationWP\\\\": "layers/SiteBuilder/packages/application-wp/tests",
            "PoP\\\\Application\\\\": "layers/SiteBuilder/packages/application/tests",
            "PoP\\\\Base36Definitions\\\\": "layers/SiteBuilder/packages/definitions-base36/tests",
            "PoP\\\\CacheControl\\\\": "layers/Engine/packages/cache-control/tests",
            "PoP\\\\ComponentModel\\\\": "layers/Engine/packages/component-model/tests",
            "PoP\\\\ConfigurableSchemaFeedback\\\\": "layers/Engine/packages/configurable-schema-feedback/tests",
            "PoP\\\\ConfigurationComponentModel\\\\": "layers/SiteBuilder/packages/component-model-configuration/tests",
            "PoP\\\\DefinitionPersistence\\\\": "layers/SiteBuilder/packages/definitionpersistence/tests",
            "PoP\\\\Definitions\\\\": "layers/Engine/packages/definitions/tests",
            "PoP\\\\EmojiDefinitions\\\\": "layers/SiteBuilder/packages/definitions-emoji/tests",
            "PoP\\\\EngineWP\\\\": "layers/Engine/packages/engine-wp/tests",
            "PoP\\\\Engine\\\\": "layers/Engine/packages/engine/tests",
            "PoP\\\\FieldQuery\\\\": "layers/Engine/packages/field-query/tests",
            "PoP\\\\FileStore\\\\": "layers/Engine/packages/filestore/tests",
            "PoP\\\\FunctionFields\\\\": "layers/Engine/packages/function-fields/tests",
            "PoP\\\\GraphQLAPI\\\\": "layers/API/packages/api-graphql/tests",
            "PoP\\\\GuzzleHelpers\\\\": "layers/Engine/packages/guzzle-helpers/tests",
            "PoP\\\\HooksWP\\\\": "layers/Engine/packages/hooks-wp/tests",
            "PoP\\\\Hooks\\\\": "layers/Engine/packages/hooks/tests",
            "PoP\\\\LooseContracts\\\\": "layers/Engine/packages/loosecontracts/tests",
            "PoP\\\\MandatoryDirectivesByConfiguration\\\\": "layers/Engine/packages/mandatory-directives-by-configuration/tests",
            "PoP\\\\ModuleRouting\\\\": "layers/Engine/packages/modulerouting/tests",
            "PoP\\\\Multisite\\\\": "layers/SiteBuilder/packages/multisite/tests",
            "PoP\\\\QueryParsing\\\\": "layers/Engine/packages/query-parsing/tests",
            "PoP\\\\RESTAPI\\\\": "layers/API/packages/api-rest/tests",
            "PoP\\\\ResourceLoader\\\\": "layers/SiteBuilder/packages/resourceloader/tests",
            "PoP\\\\Resources\\\\": "layers/SiteBuilder/packages/resources/tests",
            "PoP\\\\Root\\\\": "layers/Engine/packages/root/tests",
            "PoP\\\\RoutingWP\\\\": "layers/Engine/packages/routing-wp/tests",
            "PoP\\\\Routing\\\\": "layers/Engine/packages/routing/tests",
            "PoP\\\\SPA\\\\": "layers/SiteBuilder/packages/spa/tests",
            "PoP\\\\SSG\\\\": "layers/SiteBuilder/packages/static-site-generator/tests",
            "PoP\\\\SiteWP\\\\": "layers/SiteBuilder/packages/site-wp/tests",
            "PoP\\\\Site\\\\": "layers/SiteBuilder/packages/site/tests",
            "PoP\\\\TraceTools\\\\": "layers/Engine/packages/trace-tools/tests",
            "PoP\\\\TranslationWP\\\\": "layers/Engine/packages/translation-wp/tests",
            "PoP\\\\Translation\\\\": "layers/Engine/packages/translation/tests"
        }
    },
    "extra": {
        "wordpress-install-dir": "vendor/wordpress/wordpress",
        "merge-plugin": {
            "include": [
                "composer.local.json"
            ],
            "recurse": true,
            "replace": false,
            "ignore-duplicates": false,
            "merge-dev": true,
            "merge-extra": false,
            "merge-extra-deep": false,
            "merge-scripts": false
        }
    },
    "replace": {
        "getpop/access-control": "self.version",
        "getpop/api": "self.version",
        "getpop/api-clients": "self.version",
        "getpop/api-endpoints": "self.version",
        "getpop/api-endpoints-for-wp": "self.version",
        "getpop/api-graphql": "self.version",
        "getpop/api-mirrorquery": "self.version",
        "getpop/api-rest": "self.version",
        "getpop/application": "self.version",
        "getpop/application-wp": "self.version",
        "getpop/cache-control": "self.version",
        "getpop/component-model": "self.version",
        "getpop/component-model-configuration": "self.version",
        "getpop/configurable-schema-feedback": "self.version",
        "getpop/definitionpersistence": "self.version",
        "getpop/definitions": "self.version",
        "getpop/definitions-base36": "self.version",
        "getpop/definitions-emoji": "self.version",
        "getpop/engine": "self.version",
        "getpop/engine-wp": "self.version",
        "getpop/engine-wp-bootloader": "self.version",
        "getpop/field-query": "self.version",
        "getpop/filestore": "self.version",
        "getpop/function-fields": "self.version",
        "getpop/guzzle-helpers": "self.version",
        "getpop/hooks": "self.version",
        "getpop/hooks-wp": "self.version",
        "getpop/loosecontracts": "self.version",
        "getpop/mandatory-directives-by-configuration": "self.version",
        "getpop/migrate-api": "self.version",
        "getpop/migrate-api-graphql": "self.version",
        "getpop/migrate-component-model": "self.version",
        "getpop/migrate-component-model-configuration": "self.version",
        "getpop/migrate-engine": "self.version",
        "getpop/migrate-engine-wp": "self.version",
        "getpop/migrate-static-site-generator": "self.version",
        "getpop/modulerouting": "self.version",
        "getpop/multisite": "self.version",
        "getpop/query-parsing": "self.version",
        "getpop/resourceloader": "self.version",
        "getpop/resources": "self.version",
        "getpop/root": "self.version",
        "getpop/routing": "self.version",
        "getpop/routing-wp": "self.version",
        "getpop/site": "self.version",
        "getpop/site-wp": "self.version",
        "getpop/spa": "self.version",
        "getpop/static-site-generator": "self.version",
        "getpop/trace-tools": "self.version",
        "getpop/translation": "self.version",
        "getpop/translation-wp": "self.version",
        "graphql-api/convert-case-directives": "self.version",
        "graphql-api/graphql-api-for-wp": "self.version",
        "graphql-api/schema-feedback": "self.version",
        "graphql-by-pop/graphql-clients-for-wp": "self.version",
        "graphql-by-pop/graphql-endpoint-for-wp": "self.version",
        "graphql-by-pop/graphql-parser": "self.version",
        "graphql-by-pop/graphql-query": "self.version",
        "graphql-by-pop/graphql-request": "self.version",
        "graphql-by-pop/graphql-server": "self.version",
        "leoloso/examples-for-pop": "self.version",
        "pop-migrate-everythingelse/cssconverter": "self.version",
        "pop-migrate-everythingelse/ssr": "self.version",
        "pop-schema/basic-directives": "self.version",
        "pop-schema/block-metadata-for-wp": "self.version",
        "pop-schema/categories": "self.version",
        "pop-schema/categories-wp": "self.version",
        "pop-schema/cdn-directive": "self.version",
        "pop-schema/comment-mutations": "self.version",
        "pop-schema/comment-mutations-wp": "self.version",
        "pop-schema/commentmeta": "self.version",
        "pop-schema/commentmeta-wp": "self.version",
        "pop-schema/comments": "self.version",
        "pop-schema/comments-wp": "self.version",
        "pop-schema/convert-case-directives": "self.version",
        "pop-schema/custompost-mutations": "self.version",
        "pop-schema/custompost-mutations-wp": "self.version",
        "pop-schema/custompostmedia": "self.version",
        "pop-schema/custompostmedia-mutations": "self.version",
        "pop-schema/custompostmedia-mutations-wp": "self.version",
        "pop-schema/custompostmedia-wp": "self.version",
        "pop-schema/custompostmeta": "self.version",
        "pop-schema/custompostmeta-wp": "self.version",
        "pop-schema/customposts": "self.version",
        "pop-schema/customposts-wp": "self.version",
        "pop-schema/event-mutations": "self.version",
        "pop-schema/event-mutations-wp-em": "self.version",
        "pop-schema/events": "self.version",
        "pop-schema/events-wp-em": "self.version",
        "pop-schema/everythingelse": "self.version",
        "pop-schema/everythingelse-wp": "self.version",
        "pop-schema/generic-customposts": "self.version",
        "pop-schema/google-translate-directive": "self.version",
        "pop-schema/google-translate-directive-for-customposts": "self.version",
        "pop-schema/highlights": "self.version",
        "pop-schema/highlights-wp": "self.version",
        "pop-schema/locationposts": "self.version",
        "pop-schema/locationposts-wp": "self.version",
        "pop-schema/locations": "self.version",
        "pop-schema/locations-wp-em": "self.version",
        "pop-schema/media": "self.version",
        "pop-schema/media-wp": "self.version",
        "pop-schema/menus": "self.version",
        "pop-schema/menus-wp": "self.version",
        "pop-schema/meta": "self.version",
        "pop-schema/metaquery": "self.version",
        "pop-schema/metaquery-wp": "self.version",
        "pop-schema/migrate-categories": "self.version",
        "pop-schema/migrate-categories-wp": "self.version",
        "pop-schema/migrate-commentmeta": "self.version",
        "pop-schema/migrate-commentmeta-wp": "self.version",
        "pop-schema/migrate-comments": "self.version",
        "pop-schema/migrate-comments-wp": "self.version",
        "pop-schema/migrate-custompostmedia": "self.version",
        "pop-schema/migrate-custompostmedia-wp": "self.version",
        "pop-schema/migrate-custompostmeta": "self.version",
        "pop-schema/migrate-custompostmeta-wp": "self.version",
        "pop-schema/migrate-customposts": "self.version",
        "pop-schema/migrate-customposts-wp": "self.version",
        "pop-schema/migrate-events": "self.version",
        "pop-schema/migrate-events-wp-em": "self.version",
        "pop-schema/migrate-everythingelse": "self.version",
        "pop-schema/migrate-locations": "self.version",
        "pop-schema/migrate-locations-wp-em": "self.version",
        "pop-schema/migrate-media": "self.version",
        "pop-schema/migrate-media-wp": "self.version",
        "pop-schema/migrate-meta": "self.version",
        "pop-schema/migrate-metaquery": "self.version",
        "pop-schema/migrate-metaquery-wp": "self.version",
        "pop-schema/migrate-pages": "self.version",
        "pop-schema/migrate-pages-wp": "self.version",
        "pop-schema/migrate-post-tags": "self.version",
        "pop-schema/migrate-post-tags-wp": "self.version",
        "pop-schema/migrate-posts": "self.version",
        "pop-schema/migrate-posts-wp": "self.version",
        "pop-schema/migrate-queriedobject": "self.version",
        "pop-schema/migrate-queriedobject-wp": "self.version",
        "pop-schema/migrate-tags": "self.version",
        "pop-schema/migrate-tags-wp": "self.version",
        "pop-schema/migrate-taxonomies": "self.version",
        "pop-schema/migrate-taxonomies-wp": "self.version",
        "pop-schema/migrate-taxonomymeta": "self.version",
        "pop-schema/migrate-taxonomymeta-wp": "self.version",
        "pop-schema/migrate-taxonomyquery": "self.version",
        "pop-schema/migrate-taxonomyquery-wp": "self.version",
        "pop-schema/migrate-usermeta": "self.version",
        "pop-schema/migrate-usermeta-wp": "self.version",
        "pop-schema/migrate-users": "self.version",
        "pop-schema/migrate-users-wp": "self.version",
        "pop-schema/notifications": "self.version",
        "pop-schema/notifications-wp": "self.version",
        "pop-schema/pages": "self.version",
        "pop-schema/pages-wp": "self.version",
        "pop-schema/post-mutations": "self.version",
        "pop-schema/post-tags": "self.version",
        "pop-schema/post-tags-wp": "self.version",
        "pop-schema/posts": "self.version",
        "pop-schema/posts-wp": "self.version",
        "pop-schema/queriedobject": "self.version",
        "pop-schema/queriedobject-wp": "self.version",
        "pop-schema/schema-commons": "self.version",
        "pop-schema/stances": "self.version",
        "pop-schema/stances-wp": "self.version",
        "pop-schema/tags": "self.version",
        "pop-schema/tags-wp": "self.version",
        "pop-schema/taxonomies": "self.version",
        "pop-schema/taxonomies-wp": "self.version",
        "pop-schema/taxonomymeta": "self.version",
        "pop-schema/taxonomymeta-wp": "self.version",
        "pop-schema/taxonomyquery": "self.version",
        "pop-schema/taxonomyquery-wp": "self.version",
        "pop-schema/translate-directive": "self.version",
        "pop-schema/translate-directive-acl": "self.version",
        "pop-schema/user-roles": "self.version",
        "pop-schema/user-roles-access-control": "self.version",
        "pop-schema/user-roles-acl": "self.version",
        "pop-schema/user-roles-wp": "self.version",
        "pop-schema/user-state": "self.version",
        "pop-schema/user-state-access-control": "self.version",
        "pop-schema/user-state-mutations": "self.version",
        "pop-schema/user-state-mutations-wp": "self.version",
        "pop-schema/user-state-wp": "self.version",
        "pop-schema/usermeta": "self.version",
        "pop-schema/usermeta-wp": "self.version",
        "pop-schema/users": "self.version",
        "pop-schema/users-wp": "self.version",
        "pop-sites-wassup/comment-mutations": "self.version",
        "pop-sites-wassup/contactus-mutations": "self.version",
        "pop-sites-wassup/contactuser-mutations": "self.version",
        "pop-sites-wassup/custompost-mutations": "self.version",
        "pop-sites-wassup/custompostlink-mutations": "self.version",
        "pop-sites-wassup/event-mutations": "self.version",
        "pop-sites-wassup/eventlink-mutations": "self.version",
        "pop-sites-wassup/everythingelse-mutations": "self.version",
        "pop-sites-wassup/flag-mutations": "self.version",
        "pop-sites-wassup/form-mutations": "self.version",
        "pop-sites-wassup/gravityforms-mutations": "self.version",
        "pop-sites-wassup/highlight-mutations": "self.version",
        "pop-sites-wassup/location-mutations": "self.version",
        "pop-sites-wassup/locationpost-mutations": "self.version",
        "pop-sites-wassup/locationpostlink-mutations": "self.version",
        "pop-sites-wassup/newsletter-mutations": "self.version",
        "pop-sites-wassup/notification-mutations": "self.version",
        "pop-sites-wassup/post-mutations": "self.version",
        "pop-sites-wassup/postlink-mutations": "self.version",
        "pop-sites-wassup/share-mutations": "self.version",
        "pop-sites-wassup/socialnetwork-mutations": "self.version",
        "pop-sites-wassup/stance-mutations": "self.version",
        "pop-sites-wassup/system-mutations": "self.version",
        "pop-sites-wassup/user-state-mutations": "self.version",
        "pop-sites-wassup/volunteer-mutations": "self.version",
        "pop-sites-wassup/wassup": "self.version"
    },
    "authors": [
        {
            "name": "Leonardo Losoviz",
            "email": "leo@getpop.org",
            "homepage": "https://getpop.org"
        }
    ],
    "description": "Monorepo for all the PoP packages",
    "license": "GPL-2.0-or-later",
    "config": {
        "sort-packages": true
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://wpackagist.org"
        },
        {
            "type": "vcs",
            "url": "https://github.com/leoloso/wp-muplugin-loader.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/mcaskill/composer-merge-plugin.git"
        }
    ],
    "scripts": {
        "test": "phpunit",
        "check-style": "phpcs -n src $(monorepo-builder source-packages --subfolder=src --subfolder=tests)",
        "fix-style": "phpcbf -n src $(monorepo-builder source-packages --subfolder=src --subfolder=tests)",
        "analyse": "ci/phpstan.sh \\". $(monorepo-builder source-packages --skip-unmigrated)\\"",
        "preview-src-downgrade": "rector process $(monorepo-builder source-packages --subfolder=src) --config=rector-downgrade-code.php --ansi --dry-run || true",
        "preview-vendor-downgrade": "layers/Engine/packages/root/ci/downgrade_code.sh 7.1 rector-downgrade-code.php --dry-run || true",
        "preview-code-downgrade": [
            "@preview-src-downgrade",
            "@preview-vendor-downgrade"
        ],
        "build-server": [
            "lando init --source remote --remote-url https://wordpress.org/latest.tar.gz --recipe wordpress --webroot wordpress --name graphql-api-dev",
            "@start-server"
        ],
        "start-server": [
            "cd layers/GraphQLAPIForWP/plugins/graphql-api-for-wp && composer install",
            "lando start"
        ],
        "rebuild-server": "lando rebuild -y",
        "merge-monorepo": "monorepo-builder merge --ansi",
        "propagate-monorepo": "monorepo-builder propagate --ansi",
        "validate-monorepo": "monorepo-builder validate --ansi",
        "release": "monorepo-builder release patch --ansi"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}');

        $this->assertTrue($manipulator->addSubNode('config', 'platform-check', false));
        $this->assertEquals('{
    "name": "leoloso/pop",
    "require": {
        "php": "^7.4|^8.0",
        "ext-mbstring": "*",
        "brain/cortex": "~1.0.0",
        "composer/installers": "~1.0",
        "composer/semver": "^1.5",
        "erusev/parsedown": "^1.7",
        "guzzlehttp/guzzle": "~6.3",
        "jrfnl/php-cast-to-type": "^2.0",
        "league/pipeline": "^1.0",
        "lkwdwrd/wp-muplugin-loader": "dev-feature-composer-v2",
        "obsidian/polyfill-hrtime": "^0.1",
        "psr/cache": "^1.0",
        "symfony/cache": "^5.1",
        "symfony/config": "^5.1",
        "symfony/dependency-injection": "^5.1",
        "symfony/dotenv": "^5.1",
        "symfony/expression-language": "^5.1",
        "symfony/polyfill-php72": "^1.18",
        "symfony/polyfill-php73": "^1.18",
        "symfony/polyfill-php74": "^1.18",
        "symfony/polyfill-php80": "^1.18",
        "symfony/property-access": "^5.1",
        "symfony/yaml": "^5.1"
    },
    "require-dev": {
        "johnpbloch/wordpress": ">=5.5",
        "phpstan/phpstan": "^0.12",
        "phpunit/phpunit": ">=9.3",
        "rector/rector": "^0.9",
        "squizlabs/php_codesniffer": "^3.0",
        "symfony/var-dumper": "^5.1",
        "symplify/monorepo-builder": "^9.0",
        "szepeviktor/phpstan-wordpress": "^0.6.2"
    },
    "autoload": {
        "psr-4": {
            "GraphQLAPI\\\\ConvertCaseDirectives\\\\": "layers/GraphQLAPIForWP/plugins/convert-case-directives/src",
            "GraphQLAPI\\\\GraphQLAPI\\\\": "layers/GraphQLAPIForWP/plugins/graphql-api-for-wp/src",
            "GraphQLAPI\\\\SchemaFeedback\\\\": "layers/GraphQLAPIForWP/plugins/schema-feedback/src",
            "GraphQLByPoP\\\\GraphQLClientsForWP\\\\": "layers/GraphQLByPoP/packages/graphql-clients-for-wp/src",
            "GraphQLByPoP\\\\GraphQLEndpointForWP\\\\": "layers/GraphQLByPoP/packages/graphql-endpoint-for-wp/src",
            "GraphQLByPoP\\\\GraphQLParser\\\\": "layers/GraphQLByPoP/packages/graphql-parser/src",
            "GraphQLByPoP\\\\GraphQLQuery\\\\": "layers/GraphQLByPoP/packages/graphql-query/src",
            "GraphQLByPoP\\\\GraphQLRequest\\\\": "layers/GraphQLByPoP/packages/graphql-request/src",
            "GraphQLByPoP\\\\GraphQLServer\\\\": "layers/GraphQLByPoP/packages/graphql-server/src",
            "Leoloso\\\\ExamplesForPoP\\\\": "layers/Misc/packages/examples-for-pop/src",
            "PoPSchema\\\\BasicDirectives\\\\": "layers/Schema/packages/basic-directives/src",
            "PoPSchema\\\\BlockMetadataWP\\\\": "layers/Schema/packages/block-metadata-for-wp/src",
            "PoPSchema\\\\CDNDirective\\\\": "layers/Schema/packages/cdn-directive/src",
            "PoPSchema\\\\CategoriesWP\\\\": "layers/Schema/packages/categories-wp/src",
            "PoPSchema\\\\Categories\\\\": "layers/Schema/packages/categories/src",
            "PoPSchema\\\\CommentMetaWP\\\\": "layers/Schema/packages/commentmeta-wp/src",
            "PoPSchema\\\\CommentMeta\\\\": "layers/Schema/packages/commentmeta/src",
            "PoPSchema\\\\CommentMutationsWP\\\\": "layers/Schema/packages/comment-mutations-wp/src",
            "PoPSchema\\\\CommentMutations\\\\": "layers/Schema/packages/comment-mutations/src",
            "PoPSchema\\\\CommentsWP\\\\": "layers/Schema/packages/comments-wp/src",
            "PoPSchema\\\\Comments\\\\": "layers/Schema/packages/comments/src",
            "PoPSchema\\\\ConvertCaseDirectives\\\\": "layers/Schema/packages/convert-case-directives/src",
            "PoPSchema\\\\CustomPostMediaMutationsWP\\\\": "layers/Schema/packages/custompostmedia-mutations-wp/src",
            "PoPSchema\\\\CustomPostMediaMutations\\\\": "layers/Schema/packages/custompostmedia-mutations/src",
            "PoPSchema\\\\CustomPostMediaWP\\\\": "layers/Schema/packages/custompostmedia-wp/src",
            "PoPSchema\\\\CustomPostMedia\\\\": "layers/Schema/packages/custompostmedia/src",
            "PoPSchema\\\\CustomPostMetaWP\\\\": "layers/Schema/packages/custompostmeta-wp/src",
            "PoPSchema\\\\CustomPostMeta\\\\": "layers/Schema/packages/custompostmeta/src",
            "PoPSchema\\\\CustomPostMutationsWP\\\\": "layers/Schema/packages/custompost-mutations-wp/src",
            "PoPSchema\\\\CustomPostMutations\\\\": "layers/Schema/packages/custompost-mutations/src",
            "PoPSchema\\\\CustomPostsWP\\\\": "layers/Schema/packages/customposts-wp/src",
            "PoPSchema\\\\CustomPosts\\\\": "layers/Schema/packages/customposts/src",
            "PoPSchema\\\\EventMutationsWPEM\\\\": "layers/Schema/packages/event-mutations-wp-em/src",
            "PoPSchema\\\\EventMutations\\\\": "layers/Schema/packages/event-mutations/src",
            "PoPSchema\\\\EventsWPEM\\\\": "layers/Schema/packages/events-wp-em/src",
            "PoPSchema\\\\Events\\\\": "layers/Schema/packages/events/src",
            "PoPSchema\\\\EverythingElseWP\\\\": "layers/Schema/packages/everythingelse-wp/src",
            "PoPSchema\\\\EverythingElse\\\\": "layers/Schema/packages/everythingelse/src",
            "PoPSchema\\\\GenericCustomPosts\\\\": "layers/Schema/packages/generic-customposts/src",
            "PoPSchema\\\\GoogleTranslateDirectiveForCustomPosts\\\\": "layers/Schema/packages/google-translate-directive-for-customposts/src",
            "PoPSchema\\\\GoogleTranslateDirective\\\\": "layers/Schema/packages/google-translate-directive/src",
            "PoPSchema\\\\HighlightsWP\\\\": "layers/Schema/packages/highlights-wp/src",
            "PoPSchema\\\\Highlights\\\\": "layers/Schema/packages/highlights/src",
            "PoPSchema\\\\LocationPostsWP\\\\": "layers/Schema/packages/locationposts-wp/src",
            "PoPSchema\\\\LocationPosts\\\\": "layers/Schema/packages/locationposts/src",
            "PoPSchema\\\\LocationsWPEM\\\\": "layers/Schema/packages/locations-wp-em/src",
            "PoPSchema\\\\Locations\\\\": "layers/Schema/packages/locations/src",
            "PoPSchema\\\\MediaWP\\\\": "layers/Schema/packages/media-wp/src",
            "PoPSchema\\\\Media\\\\": "layers/Schema/packages/media/src",
            "PoPSchema\\\\MenusWP\\\\": "layers/Schema/packages/menus-wp/src",
            "PoPSchema\\\\Menus\\\\": "layers/Schema/packages/menus/src",
            "PoPSchema\\\\MetaQueryWP\\\\": "layers/Schema/packages/metaquery-wp/src",
            "PoPSchema\\\\MetaQuery\\\\": "layers/Schema/packages/metaquery/src",
            "PoPSchema\\\\Meta\\\\": "layers/Schema/packages/meta/src",
            "PoPSchema\\\\NotificationsWP\\\\": "layers/Schema/packages/notifications-wp/src",
            "PoPSchema\\\\Notifications\\\\": "layers/Schema/packages/notifications/src",
            "PoPSchema\\\\PagesWP\\\\": "layers/Schema/packages/pages-wp/src",
            "PoPSchema\\\\Pages\\\\": "layers/Schema/packages/pages/src",
            "PoPSchema\\\\PostMutations\\\\": "layers/Schema/packages/post-mutations/src",
            "PoPSchema\\\\PostTagsWP\\\\": "layers/Schema/packages/post-tags-wp/src",
            "PoPSchema\\\\PostTags\\\\": "layers/Schema/packages/post-tags/src",
            "PoPSchema\\\\PostsWP\\\\": "layers/Schema/packages/posts-wp/src",
            "PoPSchema\\\\Posts\\\\": "layers/Schema/packages/posts/src",
            "PoPSchema\\\\QueriedObjectWP\\\\": "layers/Schema/packages/queriedobject-wp/src",
            "PoPSchema\\\\QueriedObject\\\\": "layers/Schema/packages/queriedobject/src",
            "PoPSchema\\\\SchemaCommons\\\\": "layers/Schema/packages/schema-commons/src",
            "PoPSchema\\\\StancesWP\\\\": "layers/Schema/packages/stances-wp/src",
            "PoPSchema\\\\Stances\\\\": "layers/Schema/packages/stances/src",
            "PoPSchema\\\\TagsWP\\\\": "layers/Schema/packages/tags-wp/src",
            "PoPSchema\\\\Tags\\\\": "layers/Schema/packages/tags/src",
            "PoPSchema\\\\TaxonomiesWP\\\\": "layers/Schema/packages/taxonomies-wp/src",
            "PoPSchema\\\\Taxonomies\\\\": "layers/Schema/packages/taxonomies/src",
            "PoPSchema\\\\TaxonomyMetaWP\\\\": "layers/Schema/packages/taxonomymeta-wp/src",
            "PoPSchema\\\\TaxonomyMeta\\\\": "layers/Schema/packages/taxonomymeta/src",
            "PoPSchema\\\\TaxonomyQueryWP\\\\": "layers/Schema/packages/taxonomyquery-wp/src",
            "PoPSchema\\\\TaxonomyQuery\\\\": "layers/Schema/packages/taxonomyquery/src",
            "PoPSchema\\\\TranslateDirectiveACL\\\\": "layers/Schema/packages/translate-directive-acl/src",
            "PoPSchema\\\\TranslateDirective\\\\": "layers/Schema/packages/translate-directive/src",
            "PoPSchema\\\\UserMetaWP\\\\": "layers/Schema/packages/usermeta-wp/src",
            "PoPSchema\\\\UserMeta\\\\": "layers/Schema/packages/usermeta/src",
            "PoPSchema\\\\UserRolesACL\\\\": "layers/Schema/packages/user-roles-acl/src",
            "PoPSchema\\\\UserRolesAccessControl\\\\": "layers/Schema/packages/user-roles-access-control/src",
            "PoPSchema\\\\UserRolesWP\\\\": "layers/Schema/packages/user-roles-wp/src",
            "PoPSchema\\\\UserRoles\\\\": "layers/Schema/packages/user-roles/src",
            "PoPSchema\\\\UserStateAccessControl\\\\": "layers/Schema/packages/user-state-access-control/src",
            "PoPSchema\\\\UserStateMutationsWP\\\\": "layers/Schema/packages/user-state-mutations-wp/src",
            "PoPSchema\\\\UserStateMutations\\\\": "layers/Schema/packages/user-state-mutations/src",
            "PoPSchema\\\\UserStateWP\\\\": "layers/Schema/packages/user-state-wp/src",
            "PoPSchema\\\\UserState\\\\": "layers/Schema/packages/user-state/src",
            "PoPSchema\\\\UsersWP\\\\": "layers/Schema/packages/users-wp/src",
            "PoPSchema\\\\Users\\\\": "layers/Schema/packages/users/src",
            "PoPSitesWassup\\\\CommentMutations\\\\": "layers/Wassup/packages/comment-mutations/src",
            "PoPSitesWassup\\\\ContactUsMutations\\\\": "layers/Wassup/packages/contactus-mutations/src",
            "PoPSitesWassup\\\\ContactUserMutations\\\\": "layers/Wassup/packages/contactuser-mutations/src",
            "PoPSitesWassup\\\\CustomPostLinkMutations\\\\": "layers/Wassup/packages/custompostlink-mutations/src",
            "PoPSitesWassup\\\\CustomPostMutations\\\\": "layers/Wassup/packages/custompost-mutations/src",
            "PoPSitesWassup\\\\EventLinkMutations\\\\": "layers/Wassup/packages/eventlink-mutations/src",
            "PoPSitesWassup\\\\EventMutations\\\\": "layers/Wassup/packages/event-mutations/src",
            "PoPSitesWassup\\\\EverythingElseMutations\\\\": "layers/Wassup/packages/everythingelse-mutations/src",
            "PoPSitesWassup\\\\FlagMutations\\\\": "layers/Wassup/packages/flag-mutations/src",
            "PoPSitesWassup\\\\FormMutations\\\\": "layers/Wassup/packages/form-mutations/src",
            "PoPSitesWassup\\\\GravityFormsMutations\\\\": "layers/Wassup/packages/gravityforms-mutations/src",
            "PoPSitesWassup\\\\HighlightMutations\\\\": "layers/Wassup/packages/highlight-mutations/src",
            "PoPSitesWassup\\\\LocationMutations\\\\": "layers/Wassup/packages/location-mutations/src",
            "PoPSitesWassup\\\\LocationPostLinkMutations\\\\": "layers/Wassup/packages/locationpostlink-mutations/src",
            "PoPSitesWassup\\\\LocationPostMutations\\\\": "layers/Wassup/packages/locationpost-mutations/src",
            "PoPSitesWassup\\\\NewsletterMutations\\\\": "layers/Wassup/packages/newsletter-mutations/src",
            "PoPSitesWassup\\\\NotificationMutations\\\\": "layers/Wassup/packages/notification-mutations/src",
            "PoPSitesWassup\\\\PostLinkMutations\\\\": "layers/Wassup/packages/postlink-mutations/src",
            "PoPSitesWassup\\\\PostMutations\\\\": "layers/Wassup/packages/post-mutations/src",
            "PoPSitesWassup\\\\ShareMutations\\\\": "layers/Wassup/packages/share-mutations/src",
            "PoPSitesWassup\\\\SocialNetworkMutations\\\\": "layers/Wassup/packages/socialnetwork-mutations/src",
            "PoPSitesWassup\\\\StanceMutations\\\\": "layers/Wassup/packages/stance-mutations/src",
            "PoPSitesWassup\\\\SystemMutations\\\\": "layers/Wassup/packages/system-mutations/src",
            "PoPSitesWassup\\\\UserStateMutations\\\\": "layers/Wassup/packages/user-state-mutations/src",
            "PoPSitesWassup\\\\VolunteerMutations\\\\": "layers/Wassup/packages/volunteer-mutations/src",
            "PoPSitesWassup\\\\Wassup\\\\": "layers/Wassup/packages/wassup/src",
            "PoP\\\\APIClients\\\\": "layers/API/packages/api-clients/src",
            "PoP\\\\APIEndpointsForWP\\\\": "layers/API/packages/api-endpoints-for-wp/src",
            "PoP\\\\APIEndpoints\\\\": "layers/API/packages/api-endpoints/src",
            "PoP\\\\APIMirrorQuery\\\\": "layers/API/packages/api-mirrorquery/src",
            "PoP\\\\API\\\\": "layers/API/packages/api/src",
            "PoP\\\\AccessControl\\\\": "layers/Engine/packages/access-control/src",
            "PoP\\\\ApplicationWP\\\\": "layers/SiteBuilder/packages/application-wp/src",
            "PoP\\\\Application\\\\": "layers/SiteBuilder/packages/application/src",
            "PoP\\\\Base36Definitions\\\\": "layers/SiteBuilder/packages/definitions-base36/src",
            "PoP\\\\CacheControl\\\\": "layers/Engine/packages/cache-control/src",
            "PoP\\\\ComponentModel\\\\": "layers/Engine/packages/component-model/src",
            "PoP\\\\ConfigurableSchemaFeedback\\\\": "layers/Engine/packages/configurable-schema-feedback/src",
            "PoP\\\\ConfigurationComponentModel\\\\": "layers/SiteBuilder/packages/component-model-configuration/src",
            "PoP\\\\DefinitionPersistence\\\\": "layers/SiteBuilder/packages/definitionpersistence/src",
            "PoP\\\\Definitions\\\\": "layers/Engine/packages/definitions/src",
            "PoP\\\\EmojiDefinitions\\\\": "layers/SiteBuilder/packages/definitions-emoji/src",
            "PoP\\\\EngineWP\\\\": "layers/Engine/packages/engine-wp/src",
            "PoP\\\\Engine\\\\": "layers/Engine/packages/engine/src",
            "PoP\\\\FieldQuery\\\\": "layers/Engine/packages/field-query/src",
            "PoP\\\\FileStore\\\\": "layers/Engine/packages/filestore/src",
            "PoP\\\\FunctionFields\\\\": "layers/Engine/packages/function-fields/src",
            "PoP\\\\GraphQLAPI\\\\": "layers/API/packages/api-graphql/src",
            "PoP\\\\GuzzleHelpers\\\\": "layers/Engine/packages/guzzle-helpers/src",
            "PoP\\\\HooksWP\\\\": "layers/Engine/packages/hooks-wp/src",
            "PoP\\\\Hooks\\\\": "layers/Engine/packages/hooks/src",
            "PoP\\\\LooseContracts\\\\": "layers/Engine/packages/loosecontracts/src",
            "PoP\\\\MandatoryDirectivesByConfiguration\\\\": "layers/Engine/packages/mandatory-directives-by-configuration/src",
            "PoP\\\\ModuleRouting\\\\": "layers/Engine/packages/modulerouting/src",
            "PoP\\\\Multisite\\\\": "layers/SiteBuilder/packages/multisite/src",
            "PoP\\\\PoP\\\\": "src",
            "PoP\\\\QueryParsing\\\\": "layers/Engine/packages/query-parsing/src",
            "PoP\\\\RESTAPI\\\\": "layers/API/packages/api-rest/src",
            "PoP\\\\ResourceLoader\\\\": "layers/SiteBuilder/packages/resourceloader/src",
            "PoP\\\\Resources\\\\": "layers/SiteBuilder/packages/resources/src",
            "PoP\\\\Root\\\\": "layers/Engine/packages/root/src",
            "PoP\\\\RoutingWP\\\\": "layers/Engine/packages/routing-wp/src",
            "PoP\\\\Routing\\\\": "layers/Engine/packages/routing/src",
            "PoP\\\\SPA\\\\": "layers/SiteBuilder/packages/spa/src",
            "PoP\\\\SSG\\\\": "layers/SiteBuilder/packages/static-site-generator/src",
            "PoP\\\\SiteWP\\\\": "layers/SiteBuilder/packages/site-wp/src",
            "PoP\\\\Site\\\\": "layers/SiteBuilder/packages/site/src",
            "PoP\\\\TraceTools\\\\": "layers/Engine/packages/trace-tools/src",
            "PoP\\\\TranslationWP\\\\": "layers/Engine/packages/translation-wp/src",
            "PoP\\\\Translation\\\\": "layers/Engine/packages/translation/src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "GraphQLAPI\\\\ConvertCaseDirectives\\\\": "layers/GraphQLAPIForWP/plugins/convert-case-directives/tests",
            "GraphQLAPI\\\\GraphQLAPI\\\\": "layers/GraphQLAPIForWP/plugins/graphql-api-for-wp/tests",
            "GraphQLAPI\\\\SchemaFeedback\\\\": "layers/GraphQLAPIForWP/plugins/schema-feedback/tests",
            "GraphQLByPoP\\\\GraphQLClientsForWP\\\\": "layers/GraphQLByPoP/packages/graphql-clients-for-wp/tests",
            "GraphQLByPoP\\\\GraphQLEndpointForWP\\\\": "layers/GraphQLByPoP/packages/graphql-endpoint-for-wp/tests",
            "GraphQLByPoP\\\\GraphQLParser\\\\": "layers/GraphQLByPoP/packages/graphql-parser/tests",
            "GraphQLByPoP\\\\GraphQLQuery\\\\": "layers/GraphQLByPoP/packages/graphql-query/tests",
            "GraphQLByPoP\\\\GraphQLRequest\\\\": "layers/GraphQLByPoP/packages/graphql-request/tests",
            "GraphQLByPoP\\\\GraphQLServer\\\\": "layers/GraphQLByPoP/packages/graphql-server/tests",
            "Leoloso\\\\ExamplesForPoP\\\\": "layers/Misc/packages/examples-for-pop/tests",
            "PoPSchema\\\\BasicDirectives\\\\": "layers/Schema/packages/basic-directives/tests",
            "PoPSchema\\\\BlockMetadataWP\\\\": "layers/Schema/packages/block-metadata-for-wp/tests",
            "PoPSchema\\\\CDNDirective\\\\": "layers/Schema/packages/cdn-directive/tests",
            "PoPSchema\\\\CategoriesWP\\\\": "layers/Schema/packages/categories-wp/tests",
            "PoPSchema\\\\Categories\\\\": "layers/Schema/packages/categories/tests",
            "PoPSchema\\\\CommentMetaWP\\\\": "layers/Schema/packages/commentmeta-wp/tests",
            "PoPSchema\\\\CommentMeta\\\\": "layers/Schema/packages/commentmeta/tests",
            "PoPSchema\\\\CommentMutationsWP\\\\": "layers/Schema/packages/comment-mutations-wp/tests",
            "PoPSchema\\\\CommentMutations\\\\": "layers/Schema/packages/comment-mutations/tests",
            "PoPSchema\\\\CommentsWP\\\\": "layers/Schema/packages/comments-wp/tests",
            "PoPSchema\\\\Comments\\\\": "layers/Schema/packages/comments/tests",
            "PoPSchema\\\\ConvertCaseDirectives\\\\": "layers/Schema/packages/convert-case-directives/tests",
            "PoPSchema\\\\CustomPostMediaMutationsWP\\\\": "layers/Schema/packages/custompostmedia-mutations-wp/tests",
            "PoPSchema\\\\CustomPostMediaMutations\\\\": "layers/Schema/packages/custompostmedia-mutations/tests",
            "PoPSchema\\\\CustomPostMediaWP\\\\": "layers/Schema/packages/custompostmedia-wp/tests",
            "PoPSchema\\\\CustomPostMedia\\\\": "layers/Schema/packages/custompostmedia/tests",
            "PoPSchema\\\\CustomPostMetaWP\\\\": "layers/Schema/packages/custompostmeta-wp/tests",
            "PoPSchema\\\\CustomPostMeta\\\\": "layers/Schema/packages/custompostmeta/tests",
            "PoPSchema\\\\CustomPostMutationsWP\\\\": "layers/Schema/packages/custompost-mutations-wp/tests",
            "PoPSchema\\\\CustomPostMutations\\\\": "layers/Schema/packages/custompost-mutations/tests",
            "PoPSchema\\\\CustomPostsWP\\\\": "layers/Schema/packages/customposts-wp/tests",
            "PoPSchema\\\\CustomPosts\\\\": "layers/Schema/packages/customposts/tests",
            "PoPSchema\\\\EventMutationsWPEM\\\\": "layers/Schema/packages/event-mutations-wp-em/tests",
            "PoPSchema\\\\EventMutations\\\\": "layers/Schema/packages/event-mutations/tests",
            "PoPSchema\\\\EventsWPEM\\\\": "layers/Schema/packages/events-wp-em/tests",
            "PoPSchema\\\\Events\\\\": "layers/Schema/packages/events/tests",
            "PoPSchema\\\\EverythingElseWP\\\\": "layers/Schema/packages/everythingelse-wp/tests",
            "PoPSchema\\\\EverythingElse\\\\": "layers/Schema/packages/everythingelse/tests",
            "PoPSchema\\\\GenericCustomPosts\\\\": "layers/Schema/packages/generic-customposts/tests",
            "PoPSchema\\\\GoogleTranslateDirectiveForCustomPosts\\\\": "layers/Schema/packages/google-translate-directive-for-customposts/tests",
            "PoPSchema\\\\GoogleTranslateDirective\\\\": "layers/Schema/packages/google-translate-directive/tests",
            "PoPSchema\\\\HighlightsWP\\\\": "layers/Schema/packages/highlights-wp/tests",
            "PoPSchema\\\\Highlights\\\\": "layers/Schema/packages/highlights/tests",
            "PoPSchema\\\\LocationPostsWP\\\\": "layers/Schema/packages/locationposts-wp/tests",
            "PoPSchema\\\\LocationPosts\\\\": "layers/Schema/packages/locationposts/tests",
            "PoPSchema\\\\LocationsWPEM\\\\": "layers/Schema/packages/locations-wp-em/tests",
            "PoPSchema\\\\Locations\\\\": "layers/Schema/packages/locations/tests",
            "PoPSchema\\\\MediaWP\\\\": "layers/Schema/packages/media-wp/tests",
            "PoPSchema\\\\Media\\\\": "layers/Schema/packages/media/tests",
            "PoPSchema\\\\MenusWP\\\\": "layers/Schema/packages/menus-wp/tests",
            "PoPSchema\\\\Menus\\\\": "layers/Schema/packages/menus/tests",
            "PoPSchema\\\\MetaQueryWP\\\\": "layers/Schema/packages/metaquery-wp/tests",
            "PoPSchema\\\\MetaQuery\\\\": "layers/Schema/packages/metaquery/tests",
            "PoPSchema\\\\Meta\\\\": "layers/Schema/packages/meta/tests",
            "PoPSchema\\\\NotificationsWP\\\\": "layers/Schema/packages/notifications-wp/tests",
            "PoPSchema\\\\Notifications\\\\": "layers/Schema/packages/notifications/tests",
            "PoPSchema\\\\PagesWP\\\\": "layers/Schema/packages/pages-wp/tests",
            "PoPSchema\\\\Pages\\\\": "layers/Schema/packages/pages/tests",
            "PoPSchema\\\\PostMutations\\\\": "layers/Schema/packages/post-mutations/tests",
            "PoPSchema\\\\PostTagsWP\\\\": "layers/Schema/packages/post-tags-wp/tests",
            "PoPSchema\\\\PostTags\\\\": "layers/Schema/packages/post-tags/tests",
            "PoPSchema\\\\PostsWP\\\\": "layers/Schema/packages/posts-wp/tests",
            "PoPSchema\\\\Posts\\\\": "layers/Schema/packages/posts/tests",
            "PoPSchema\\\\QueriedObjectWP\\\\": "layers/Schema/packages/queriedobject-wp/tests",
            "PoPSchema\\\\QueriedObject\\\\": "layers/Schema/packages/queriedobject/tests",
            "PoPSchema\\\\SchemaCommons\\\\": "layers/Schema/packages/schema-commons/tests",
            "PoPSchema\\\\StancesWP\\\\": "layers/Schema/packages/stances-wp/tests",
            "PoPSchema\\\\Stances\\\\": "layers/Schema/packages/stances/tests",
            "PoPSchema\\\\TagsWP\\\\": "layers/Schema/packages/tags-wp/tests",
            "PoPSchema\\\\Tags\\\\": "layers/Schema/packages/tags/tests",
            "PoPSchema\\\\TaxonomiesWP\\\\": "layers/Schema/packages/taxonomies-wp/tests",
            "PoPSchema\\\\Taxonomies\\\\": "layers/Schema/packages/taxonomies/tests",
            "PoPSchema\\\\TaxonomyMetaWP\\\\": "layers/Schema/packages/taxonomymeta-wp/tests",
            "PoPSchema\\\\TaxonomyMeta\\\\": "layers/Schema/packages/taxonomymeta/tests",
            "PoPSchema\\\\TaxonomyQueryWP\\\\": "layers/Schema/packages/taxonomyquery-wp/tests",
            "PoPSchema\\\\TaxonomyQuery\\\\": "layers/Schema/packages/taxonomyquery/tests",
            "PoPSchema\\\\TranslateDirectiveACL\\\\": "layers/Schema/packages/translate-directive-acl/tests",
            "PoPSchema\\\\TranslateDirective\\\\": "layers/Schema/packages/translate-directive/tests",
            "PoPSchema\\\\UserMetaWP\\\\": "layers/Schema/packages/usermeta-wp/tests",
            "PoPSchema\\\\UserMeta\\\\": "layers/Schema/packages/usermeta/tests",
            "PoPSchema\\\\UserRolesACL\\\\": "layers/Schema/packages/user-roles-acl/tests",
            "PoPSchema\\\\UserRolesAccessControl\\\\": "layers/Schema/packages/user-roles-access-control/tests",
            "PoPSchema\\\\UserRolesWP\\\\": "layers/Schema/packages/user-roles-wp/tests",
            "PoPSchema\\\\UserRoles\\\\": "layers/Schema/packages/user-roles/tests",
            "PoPSchema\\\\UserStateAccessControl\\\\": "layers/Schema/packages/user-state-access-control/tests",
            "PoPSchema\\\\UserStateMutationsWP\\\\": "layers/Schema/packages/user-state-mutations-wp/tests",
            "PoPSchema\\\\UserStateMutations\\\\": "layers/Schema/packages/user-state-mutations/tests",
            "PoPSchema\\\\UserStateWP\\\\": "layers/Schema/packages/user-state-wp/tests",
            "PoPSchema\\\\UserState\\\\": "layers/Schema/packages/user-state/tests",
            "PoPSchema\\\\UsersWP\\\\": "layers/Schema/packages/users-wp/tests",
            "PoPSchema\\\\Users\\\\": "layers/Schema/packages/users/tests",
            "PoPSitesWassup\\\\CommentMutations\\\\": "layers/Wassup/packages/comment-mutations/tests",
            "PoPSitesWassup\\\\ContactUsMutations\\\\": "layers/Wassup/packages/contactus-mutations/tests",
            "PoPSitesWassup\\\\ContactUserMutations\\\\": "layers/Wassup/packages/contactuser-mutations/tests",
            "PoPSitesWassup\\\\CustomPostLinkMutations\\\\": "layers/Wassup/packages/custompostlink-mutations/tests",
            "PoPSitesWassup\\\\CustomPostMutations\\\\": "layers/Wassup/packages/custompost-mutations/tests",
            "PoPSitesWassup\\\\EventLinkMutations\\\\": "layers/Wassup/packages/eventlink-mutations/tests",
            "PoPSitesWassup\\\\EventMutations\\\\": "layers/Wassup/packages/event-mutations/tests",
            "PoPSitesWassup\\\\EverythingElseMutations\\\\": "layers/Wassup/packages/everythingelse-mutations/tests",
            "PoPSitesWassup\\\\FlagMutations\\\\": "layers/Wassup/packages/flag-mutations/tests",
            "PoPSitesWassup\\\\FormMutations\\\\": "layers/Wassup/packages/form-mutations/tests",
            "PoPSitesWassup\\\\GravityFormsMutations\\\\": "layers/Wassup/packages/gravityforms-mutations/tests",
            "PoPSitesWassup\\\\HighlightMutations\\\\": "layers/Wassup/packages/highlight-mutations/tests",
            "PoPSitesWassup\\\\LocationMutations\\\\": "layers/Wassup/packages/location-mutations/tests",
            "PoPSitesWassup\\\\LocationPostLinkMutations\\\\": "layers/Wassup/packages/locationpostlink-mutations/tests",
            "PoPSitesWassup\\\\LocationPostMutations\\\\": "layers/Wassup/packages/locationpost-mutations/tests",
            "PoPSitesWassup\\\\NewsletterMutations\\\\": "layers/Wassup/packages/newsletter-mutations/tests",
            "PoPSitesWassup\\\\NotificationMutations\\\\": "layers/Wassup/packages/notification-mutations/tests",
            "PoPSitesWassup\\\\PostLinkMutations\\\\": "layers/Wassup/packages/postlink-mutations/tests",
            "PoPSitesWassup\\\\PostMutations\\\\": "layers/Wassup/packages/post-mutations/tests",
            "PoPSitesWassup\\\\ShareMutations\\\\": "layers/Wassup/packages/share-mutations/tests",
            "PoPSitesWassup\\\\SocialNetworkMutations\\\\": "layers/Wassup/packages/socialnetwork-mutations/tests",
            "PoPSitesWassup\\\\StanceMutations\\\\": "layers/Wassup/packages/stance-mutations/tests",
            "PoPSitesWassup\\\\SystemMutations\\\\": "layers/Wassup/packages/system-mutations/tests",
            "PoPSitesWassup\\\\UserStateMutations\\\\": "layers/Wassup/packages/user-state-mutations/tests",
            "PoPSitesWassup\\\\VolunteerMutations\\\\": "layers/Wassup/packages/volunteer-mutations/tests",
            "PoPSitesWassup\\\\Wassup\\\\": "layers/Wassup/packages/wassup/tests",
            "PoP\\\\APIClients\\\\": "layers/API/packages/api-clients/tests",
            "PoP\\\\APIEndpointsForWP\\\\": "layers/API/packages/api-endpoints-for-wp/tests",
            "PoP\\\\APIEndpoints\\\\": "layers/API/packages/api-endpoints/tests",
            "PoP\\\\APIMirrorQuery\\\\": "layers/API/packages/api-mirrorquery/tests",
            "PoP\\\\API\\\\": "layers/API/packages/api/tests",
            "PoP\\\\AccessControl\\\\": "layers/Engine/packages/access-control/tests",
            "PoP\\\\ApplicationWP\\\\": "layers/SiteBuilder/packages/application-wp/tests",
            "PoP\\\\Application\\\\": "layers/SiteBuilder/packages/application/tests",
            "PoP\\\\Base36Definitions\\\\": "layers/SiteBuilder/packages/definitions-base36/tests",
            "PoP\\\\CacheControl\\\\": "layers/Engine/packages/cache-control/tests",
            "PoP\\\\ComponentModel\\\\": "layers/Engine/packages/component-model/tests",
            "PoP\\\\ConfigurableSchemaFeedback\\\\": "layers/Engine/packages/configurable-schema-feedback/tests",
            "PoP\\\\ConfigurationComponentModel\\\\": "layers/SiteBuilder/packages/component-model-configuration/tests",
            "PoP\\\\DefinitionPersistence\\\\": "layers/SiteBuilder/packages/definitionpersistence/tests",
            "PoP\\\\Definitions\\\\": "layers/Engine/packages/definitions/tests",
            "PoP\\\\EmojiDefinitions\\\\": "layers/SiteBuilder/packages/definitions-emoji/tests",
            "PoP\\\\EngineWP\\\\": "layers/Engine/packages/engine-wp/tests",
            "PoP\\\\Engine\\\\": "layers/Engine/packages/engine/tests",
            "PoP\\\\FieldQuery\\\\": "layers/Engine/packages/field-query/tests",
            "PoP\\\\FileStore\\\\": "layers/Engine/packages/filestore/tests",
            "PoP\\\\FunctionFields\\\\": "layers/Engine/packages/function-fields/tests",
            "PoP\\\\GraphQLAPI\\\\": "layers/API/packages/api-graphql/tests",
            "PoP\\\\GuzzleHelpers\\\\": "layers/Engine/packages/guzzle-helpers/tests",
            "PoP\\\\HooksWP\\\\": "layers/Engine/packages/hooks-wp/tests",
            "PoP\\\\Hooks\\\\": "layers/Engine/packages/hooks/tests",
            "PoP\\\\LooseContracts\\\\": "layers/Engine/packages/loosecontracts/tests",
            "PoP\\\\MandatoryDirectivesByConfiguration\\\\": "layers/Engine/packages/mandatory-directives-by-configuration/tests",
            "PoP\\\\ModuleRouting\\\\": "layers/Engine/packages/modulerouting/tests",
            "PoP\\\\Multisite\\\\": "layers/SiteBuilder/packages/multisite/tests",
            "PoP\\\\QueryParsing\\\\": "layers/Engine/packages/query-parsing/tests",
            "PoP\\\\RESTAPI\\\\": "layers/API/packages/api-rest/tests",
            "PoP\\\\ResourceLoader\\\\": "layers/SiteBuilder/packages/resourceloader/tests",
            "PoP\\\\Resources\\\\": "layers/SiteBuilder/packages/resources/tests",
            "PoP\\\\Root\\\\": "layers/Engine/packages/root/tests",
            "PoP\\\\RoutingWP\\\\": "layers/Engine/packages/routing-wp/tests",
            "PoP\\\\Routing\\\\": "layers/Engine/packages/routing/tests",
            "PoP\\\\SPA\\\\": "layers/SiteBuilder/packages/spa/tests",
            "PoP\\\\SSG\\\\": "layers/SiteBuilder/packages/static-site-generator/tests",
            "PoP\\\\SiteWP\\\\": "layers/SiteBuilder/packages/site-wp/tests",
            "PoP\\\\Site\\\\": "layers/SiteBuilder/packages/site/tests",
            "PoP\\\\TraceTools\\\\": "layers/Engine/packages/trace-tools/tests",
            "PoP\\\\TranslationWP\\\\": "layers/Engine/packages/translation-wp/tests",
            "PoP\\\\Translation\\\\": "layers/Engine/packages/translation/tests"
        }
    },
    "extra": {
        "wordpress-install-dir": "vendor/wordpress/wordpress",
        "merge-plugin": {
            "include": [
                "composer.local.json"
            ],
            "recurse": true,
            "replace": false,
            "ignore-duplicates": false,
            "merge-dev": true,
            "merge-extra": false,
            "merge-extra-deep": false,
            "merge-scripts": false
        }
    },
    "replace": {
        "getpop/access-control": "self.version",
        "getpop/api": "self.version",
        "getpop/api-clients": "self.version",
        "getpop/api-endpoints": "self.version",
        "getpop/api-endpoints-for-wp": "self.version",
        "getpop/api-graphql": "self.version",
        "getpop/api-mirrorquery": "self.version",
        "getpop/api-rest": "self.version",
        "getpop/application": "self.version",
        "getpop/application-wp": "self.version",
        "getpop/cache-control": "self.version",
        "getpop/component-model": "self.version",
        "getpop/component-model-configuration": "self.version",
        "getpop/configurable-schema-feedback": "self.version",
        "getpop/definitionpersistence": "self.version",
        "getpop/definitions": "self.version",
        "getpop/definitions-base36": "self.version",
        "getpop/definitions-emoji": "self.version",
        "getpop/engine": "self.version",
        "getpop/engine-wp": "self.version",
        "getpop/engine-wp-bootloader": "self.version",
        "getpop/field-query": "self.version",
        "getpop/filestore": "self.version",
        "getpop/function-fields": "self.version",
        "getpop/guzzle-helpers": "self.version",
        "getpop/hooks": "self.version",
        "getpop/hooks-wp": "self.version",
        "getpop/loosecontracts": "self.version",
        "getpop/mandatory-directives-by-configuration": "self.version",
        "getpop/migrate-api": "self.version",
        "getpop/migrate-api-graphql": "self.version",
        "getpop/migrate-component-model": "self.version",
        "getpop/migrate-component-model-configuration": "self.version",
        "getpop/migrate-engine": "self.version",
        "getpop/migrate-engine-wp": "self.version",
        "getpop/migrate-static-site-generator": "self.version",
        "getpop/modulerouting": "self.version",
        "getpop/multisite": "self.version",
        "getpop/query-parsing": "self.version",
        "getpop/resourceloader": "self.version",
        "getpop/resources": "self.version",
        "getpop/root": "self.version",
        "getpop/routing": "self.version",
        "getpop/routing-wp": "self.version",
        "getpop/site": "self.version",
        "getpop/site-wp": "self.version",
        "getpop/spa": "self.version",
        "getpop/static-site-generator": "self.version",
        "getpop/trace-tools": "self.version",
        "getpop/translation": "self.version",
        "getpop/translation-wp": "self.version",
        "graphql-api/convert-case-directives": "self.version",
        "graphql-api/graphql-api-for-wp": "self.version",
        "graphql-api/schema-feedback": "self.version",
        "graphql-by-pop/graphql-clients-for-wp": "self.version",
        "graphql-by-pop/graphql-endpoint-for-wp": "self.version",
        "graphql-by-pop/graphql-parser": "self.version",
        "graphql-by-pop/graphql-query": "self.version",
        "graphql-by-pop/graphql-request": "self.version",
        "graphql-by-pop/graphql-server": "self.version",
        "leoloso/examples-for-pop": "self.version",
        "pop-migrate-everythingelse/cssconverter": "self.version",
        "pop-migrate-everythingelse/ssr": "self.version",
        "pop-schema/basic-directives": "self.version",
        "pop-schema/block-metadata-for-wp": "self.version",
        "pop-schema/categories": "self.version",
        "pop-schema/categories-wp": "self.version",
        "pop-schema/cdn-directive": "self.version",
        "pop-schema/comment-mutations": "self.version",
        "pop-schema/comment-mutations-wp": "self.version",
        "pop-schema/commentmeta": "self.version",
        "pop-schema/commentmeta-wp": "self.version",
        "pop-schema/comments": "self.version",
        "pop-schema/comments-wp": "self.version",
        "pop-schema/convert-case-directives": "self.version",
        "pop-schema/custompost-mutations": "self.version",
        "pop-schema/custompost-mutations-wp": "self.version",
        "pop-schema/custompostmedia": "self.version",
        "pop-schema/custompostmedia-mutations": "self.version",
        "pop-schema/custompostmedia-mutations-wp": "self.version",
        "pop-schema/custompostmedia-wp": "self.version",
        "pop-schema/custompostmeta": "self.version",
        "pop-schema/custompostmeta-wp": "self.version",
        "pop-schema/customposts": "self.version",
        "pop-schema/customposts-wp": "self.version",
        "pop-schema/event-mutations": "self.version",
        "pop-schema/event-mutations-wp-em": "self.version",
        "pop-schema/events": "self.version",
        "pop-schema/events-wp-em": "self.version",
        "pop-schema/everythingelse": "self.version",
        "pop-schema/everythingelse-wp": "self.version",
        "pop-schema/generic-customposts": "self.version",
        "pop-schema/google-translate-directive": "self.version",
        "pop-schema/google-translate-directive-for-customposts": "self.version",
        "pop-schema/highlights": "self.version",
        "pop-schema/highlights-wp": "self.version",
        "pop-schema/locationposts": "self.version",
        "pop-schema/locationposts-wp": "self.version",
        "pop-schema/locations": "self.version",
        "pop-schema/locations-wp-em": "self.version",
        "pop-schema/media": "self.version",
        "pop-schema/media-wp": "self.version",
        "pop-schema/menus": "self.version",
        "pop-schema/menus-wp": "self.version",
        "pop-schema/meta": "self.version",
        "pop-schema/metaquery": "self.version",
        "pop-schema/metaquery-wp": "self.version",
        "pop-schema/migrate-categories": "self.version",
        "pop-schema/migrate-categories-wp": "self.version",
        "pop-schema/migrate-commentmeta": "self.version",
        "pop-schema/migrate-commentmeta-wp": "self.version",
        "pop-schema/migrate-comments": "self.version",
        "pop-schema/migrate-comments-wp": "self.version",
        "pop-schema/migrate-custompostmedia": "self.version",
        "pop-schema/migrate-custompostmedia-wp": "self.version",
        "pop-schema/migrate-custompostmeta": "self.version",
        "pop-schema/migrate-custompostmeta-wp": "self.version",
        "pop-schema/migrate-customposts": "self.version",
        "pop-schema/migrate-customposts-wp": "self.version",
        "pop-schema/migrate-events": "self.version",
        "pop-schema/migrate-events-wp-em": "self.version",
        "pop-schema/migrate-everythingelse": "self.version",
        "pop-schema/migrate-locations": "self.version",
        "pop-schema/migrate-locations-wp-em": "self.version",
        "pop-schema/migrate-media": "self.version",
        "pop-schema/migrate-media-wp": "self.version",
        "pop-schema/migrate-meta": "self.version",
        "pop-schema/migrate-metaquery": "self.version",
        "pop-schema/migrate-metaquery-wp": "self.version",
        "pop-schema/migrate-pages": "self.version",
        "pop-schema/migrate-pages-wp": "self.version",
        "pop-schema/migrate-post-tags": "self.version",
        "pop-schema/migrate-post-tags-wp": "self.version",
        "pop-schema/migrate-posts": "self.version",
        "pop-schema/migrate-posts-wp": "self.version",
        "pop-schema/migrate-queriedobject": "self.version",
        "pop-schema/migrate-queriedobject-wp": "self.version",
        "pop-schema/migrate-tags": "self.version",
        "pop-schema/migrate-tags-wp": "self.version",
        "pop-schema/migrate-taxonomies": "self.version",
        "pop-schema/migrate-taxonomies-wp": "self.version",
        "pop-schema/migrate-taxonomymeta": "self.version",
        "pop-schema/migrate-taxonomymeta-wp": "self.version",
        "pop-schema/migrate-taxonomyquery": "self.version",
        "pop-schema/migrate-taxonomyquery-wp": "self.version",
        "pop-schema/migrate-usermeta": "self.version",
        "pop-schema/migrate-usermeta-wp": "self.version",
        "pop-schema/migrate-users": "self.version",
        "pop-schema/migrate-users-wp": "self.version",
        "pop-schema/notifications": "self.version",
        "pop-schema/notifications-wp": "self.version",
        "pop-schema/pages": "self.version",
        "pop-schema/pages-wp": "self.version",
        "pop-schema/post-mutations": "self.version",
        "pop-schema/post-tags": "self.version",
        "pop-schema/post-tags-wp": "self.version",
        "pop-schema/posts": "self.version",
        "pop-schema/posts-wp": "self.version",
        "pop-schema/queriedobject": "self.version",
        "pop-schema/queriedobject-wp": "self.version",
        "pop-schema/schema-commons": "self.version",
        "pop-schema/stances": "self.version",
        "pop-schema/stances-wp": "self.version",
        "pop-schema/tags": "self.version",
        "pop-schema/tags-wp": "self.version",
        "pop-schema/taxonomies": "self.version",
        "pop-schema/taxonomies-wp": "self.version",
        "pop-schema/taxonomymeta": "self.version",
        "pop-schema/taxonomymeta-wp": "self.version",
        "pop-schema/taxonomyquery": "self.version",
        "pop-schema/taxonomyquery-wp": "self.version",
        "pop-schema/translate-directive": "self.version",
        "pop-schema/translate-directive-acl": "self.version",
        "pop-schema/user-roles": "self.version",
        "pop-schema/user-roles-access-control": "self.version",
        "pop-schema/user-roles-acl": "self.version",
        "pop-schema/user-roles-wp": "self.version",
        "pop-schema/user-state": "self.version",
        "pop-schema/user-state-access-control": "self.version",
        "pop-schema/user-state-mutations": "self.version",
        "pop-schema/user-state-mutations-wp": "self.version",
        "pop-schema/user-state-wp": "self.version",
        "pop-schema/usermeta": "self.version",
        "pop-schema/usermeta-wp": "self.version",
        "pop-schema/users": "self.version",
        "pop-schema/users-wp": "self.version",
        "pop-sites-wassup/comment-mutations": "self.version",
        "pop-sites-wassup/contactus-mutations": "self.version",
        "pop-sites-wassup/contactuser-mutations": "self.version",
        "pop-sites-wassup/custompost-mutations": "self.version",
        "pop-sites-wassup/custompostlink-mutations": "self.version",
        "pop-sites-wassup/event-mutations": "self.version",
        "pop-sites-wassup/eventlink-mutations": "self.version",
        "pop-sites-wassup/everythingelse-mutations": "self.version",
        "pop-sites-wassup/flag-mutations": "self.version",
        "pop-sites-wassup/form-mutations": "self.version",
        "pop-sites-wassup/gravityforms-mutations": "self.version",
        "pop-sites-wassup/highlight-mutations": "self.version",
        "pop-sites-wassup/location-mutations": "self.version",
        "pop-sites-wassup/locationpost-mutations": "self.version",
        "pop-sites-wassup/locationpostlink-mutations": "self.version",
        "pop-sites-wassup/newsletter-mutations": "self.version",
        "pop-sites-wassup/notification-mutations": "self.version",
        "pop-sites-wassup/post-mutations": "self.version",
        "pop-sites-wassup/postlink-mutations": "self.version",
        "pop-sites-wassup/share-mutations": "self.version",
        "pop-sites-wassup/socialnetwork-mutations": "self.version",
        "pop-sites-wassup/stance-mutations": "self.version",
        "pop-sites-wassup/system-mutations": "self.version",
        "pop-sites-wassup/user-state-mutations": "self.version",
        "pop-sites-wassup/volunteer-mutations": "self.version",
        "pop-sites-wassup/wassup": "self.version"
    },
    "authors": [
        {
            "name": "Leonardo Losoviz",
            "email": "leo@getpop.org",
            "homepage": "https://getpop.org"
        }
    ],
    "description": "Monorepo for all the PoP packages",
    "license": "GPL-2.0-or-later",
    "config": {
        "sort-packages": true,
        "platform-check": false
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://wpackagist.org"
        },
        {
            "type": "vcs",
            "url": "https://github.com/leoloso/wp-muplugin-loader.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/mcaskill/composer-merge-plugin.git"
        }
    ],
    "scripts": {
        "test": "phpunit",
        "check-style": "phpcs -n src $(monorepo-builder source-packages --subfolder=src --subfolder=tests)",
        "fix-style": "phpcbf -n src $(monorepo-builder source-packages --subfolder=src --subfolder=tests)",
        "analyse": "ci/phpstan.sh \\". $(monorepo-builder source-packages --skip-unmigrated)\\"",
        "preview-src-downgrade": "rector process $(monorepo-builder source-packages --subfolder=src) --config=rector-downgrade-code.php --ansi --dry-run || true",
        "preview-vendor-downgrade": "layers/Engine/packages/root/ci/downgrade_code.sh 7.1 rector-downgrade-code.php --dry-run || true",
        "preview-code-downgrade": [
            "@preview-src-downgrade",
            "@preview-vendor-downgrade"
        ],
        "build-server": [
            "lando init --source remote --remote-url https://wordpress.org/latest.tar.gz --recipe wordpress --webroot wordpress --name graphql-api-dev",
            "@start-server"
        ],
        "start-server": [
            "cd layers/GraphQLAPIForWP/plugins/graphql-api-for-wp && composer install",
            "lando start"
        ],
        "rebuild-server": "lando rebuild -y",
        "merge-monorepo": "monorepo-builder merge --ansi",
        "propagate-monorepo": "monorepo-builder propagate --ansi",
        "validate-monorepo": "monorepo-builder validate --ansi",
        "release": "monorepo-builder release patch --ansi"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
', $manipulator->getContents());
    }
}
