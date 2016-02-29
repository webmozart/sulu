<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Tests\Unit\Routing\Enhancers;

use Sulu\Component\CustomUrl\Document\CustomUrlDocument;
use Sulu\Component\CustomUrl\Routing\Enhancers\TrailingHTMLEnhancer;
use Sulu\Component\Webspace\Webspace;
use Symfony\Component\HttpFoundation\Request;

class TrailingHTMLEnhancerTest extends \PHPUnit_Framework_TestCase
{
    public function testEnhance()
    {
        $customUrl = $this->prophesize(CustomUrlDocument::class);
        $webspace = $this->prophesize(Webspace::class);

        $request = $this->prophesize(Request::class);
        $request->getRequestUri()->willReturn('/test.html');
        $request->getUri()->willReturn('sulu.io/test.html');

        $enhancer = new TrailingHTMLEnhancer();

        $defaults = $enhancer->enhance(
            ['_custom_url' => $customUrl->reveal(), '_webspace' => $webspace->reveal()],
            $request->reveal()
        );

        self::assertEquals(
            [
                '_custom_url' => $customUrl->reveal(),
                '_webspace' => $webspace->reveal(),
                '_finalized' => true,
                '_controller' => 'SuluWebsiteBundle:Default:redirect',
                'url' => 'sulu.io/test',
            ],
            $defaults
        );
    }

    public function testEnhanceWithoutHtml()
    {
        $customUrl = $this->prophesize(CustomUrlDocument::class);
        $webspace = $this->prophesize(Webspace::class);

        $request = $this->prophesize(Request::class);
        $request->getRequestUri()->willReturn('/test');

        $enhancer = new TrailingHTMLEnhancer();

        $defaults = $enhancer->enhance(
            ['_custom_url' => $customUrl->reveal(), '_webspace' => $webspace->reveal()],
            $request->reveal()
        );

        self::assertEquals(['_custom_url' => $customUrl->reveal(), '_webspace' => $webspace->reveal()], $defaults);
    }
}
