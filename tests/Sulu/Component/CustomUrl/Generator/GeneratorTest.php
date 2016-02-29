<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Generator;

use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Url\ReplacerFactory;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function provideGenerateData()
    {
        $locales = [new Localization(), new Localization()];

        $locales[0]->setLanguage('de');
        $locales[0]->setCountry('at');
        $locales[1]->setLanguage('en');

        return [
            [
                '*.sulu.io/test/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                null,
                'test-1.sulu.io/test/test-2',
            ],
            [
                '*.sulu.io/test',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                null,
                'test-1.sulu.io/test/test-2',
            ],
            [
                '*.sulu.io/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                null,
                'test-1.sulu.io/test-2',
            ],
            [
                '*.sulu.io/',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                null,
                'test-1.sulu.io/test-2',
            ],
            [
                '*.sulu.io/test/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[0],
                'test-1.sulu.io/test/test-2/de_at',
            ],
            [
                '*.sulu.io/test/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[1],
                'test-1.sulu.io/test/test-2/en',
            ],
            [
                '*.sulu.io/{localization}/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[0],
                'test-1.sulu.io/de_at/test-2',
            ],
            [
                '*.sulu.io/{localization}/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[1],
                'test-1.sulu.io/en/test-2',
            ],
            [
                '*.sulu.io/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[0],
                'test-1.sulu.io/test-2/de_at',
            ],
            [
                '*.sulu.io/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2']],
                $locales[1],
                'test-1.sulu.io/test-2/en',
            ],
            [
                '*.sulu.io/*/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2', 'test-3']],
                $locales[1],
                'test-1.sulu.io/test-2/test-3/en',
            ],
            [
                '*.sulu.io/*/{localization}/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2', 'test-3']],
                $locales[1],
                'test-1.sulu.io/test-2/en/test-3',
            ],
            [
                '*.sulu.io/{country}/*/{language}/*',
                ['prefix' => 'test-1', 'suffix' => ['test-2', 'test-3']],
                $locales[0],
                'test-1.sulu.io/at/test-2/de/test-3',
            ],
        ];
    }

    /**
     * @dataProvider provideGenerateData
     */
    public function testGenerate($baseDomain, $domainParts, $locales, $expected)
    {
        $generator = new Generator(new ReplacerFactory());
        $result = $generator->generate($baseDomain, $domainParts, $locales);

        self::assertEquals($expected, $result);
    }
}
