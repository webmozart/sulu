<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Routing\Enhancers;

use Sulu\Component\Content\Compat\Structure;
use Sulu\Component\Content\Compat\Structure\PageBridge;
use Symfony\Component\HttpFoundation\Request;

class ExternalLinkEnhancerTest extends \PHPUnit_Framework_TestCase
{
    public function testEnhance()
    {
        $structure = $this->prophesize(PageBridge::class);
        $structure->getNodeType()->willReturn(Structure::NODE_TYPE_EXTERNAL_LINK);
        $structure->getResourceLocator()->willReturn('/test');
        $request = $this->prophesize(Request::class);

        $enhancer = new ExternalLinkEnhancer();
        $defaults = $enhancer->enhance(['_structure' => $structure->reveal()], $request->reveal());

        self::assertEquals(
            [
                '_structure' => $structure->reveal(),
                '_controller' => 'SuluWebsiteBundle:Default:redirect',
                'url' => '/test',
            ],
            $defaults
        );
    }

    public function testEnhanceNoStructure()
    {
        $structure = $this->prophesize(PageBridge::class);
        $structure->getNodeType()->willReturn(Structure::NODE_TYPE_EXTERNAL_LINK);
        $structure->getResourceLocator()->willReturn('/test');
        $request = $this->prophesize(Request::class);

        $enhancer = new ExternalLinkEnhancer();
        $defaults = $enhancer->enhance([], $request->reveal());

        self::assertEquals([], $defaults);
    }

    public function testEnhanceInternalLink()
    {
        $structure = $this->prophesize(PageBridge::class);
        $structure->getNodeType()->willReturn(Structure::NODE_TYPE_INTERNAL_LINK);
        $structure->getResourceLocator()->willReturn('/test');
        $request = $this->prophesize(Request::class);

        $enhancer = new ExternalLinkEnhancer();
        $defaults = $enhancer->enhance(['_structure' => $structure->reveal()], $request->reveal());

        self::assertEquals(['_structure' => $structure->reveal()], $defaults);
    }
}
