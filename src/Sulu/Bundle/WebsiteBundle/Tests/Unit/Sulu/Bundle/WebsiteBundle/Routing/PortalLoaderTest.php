<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\WebsiteBundle\Routing;

use Prophecy\Argument;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Portal;
use Sulu\Component\Webspace\PortalInformation;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PortalLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PortalLoader
     */
    private $portalLoader;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var LoaderResolverInterface
     */
    private $loaderResolver;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var Localization[]
     */
    private $localizations;

    public function setUp()
    {
        parent::setUp();

        $this->webspaceManager = $this->prophesize('Sulu\Component\Webspace\Manager\WebspaceManagerInterface');
        $this->loaderResolver = $this->prophesize('Symfony\Component\Config\Loader\LoaderResolverInterface');
        $this->loader = $this->prophesize('Symfony\Component\Config\Loader\LoaderInterface');

        $this->portalLoader = new PortalLoader($this->webspaceManager->reveal(), 'dev');
        $this->portalLoader->setResolver($this->loaderResolver->reveal());

        $de = new Localization();
        $de->setLanguage('de');
        $en = new Localization();
        $en->setLanguage('en');
        $this->localizations = [$de, $en];
    }

    public function testLoad()
    {
        $importedRouteCollection = new RouteCollection();
        $importedRouteCollection->add('route1', new Route('/example/route1'));
        $importedRouteCollection->add('route2', new Route('/route2'));

        $portal1 = new Portal();
        $portal1->setKey('sulu_lo');
        $portal1->setLocalizations($this->localizations);

        $portal2 = new Portal();
        $portal2->setKey('sulu_com');
        $portal2->setLocalizations($this->localizations);

        $portalInformations = [
            new PortalInformation(null, null, $portal1, $this->localizations[0], 'sulu.io/de'),
            new PortalInformation(null, null, $portal2, $this->localizations[1], 'sulu.com'),
        ];

        $this->loaderResolver->resolve(Argument::any(), Argument::any())->willReturn($this->loader->reveal());
        $this->loader->load(Argument::any(), Argument::any())->willReturn($importedRouteCollection);
        $this->webspaceManager->getPortalInformations(Argument::any())->willReturn($portalInformations);

        $routeCollection = $this->portalLoader->load('', 'portal');

        $this->assertCount(8, $routeCollection);

        $routes = $routeCollection->getIterator();
        $this->assertArrayHasKey('sulu.io/de.route1', $routes);
        $this->assertArrayHasKey('sulu.io/de.de.route1', $routes);
        $this->assertArrayHasKey('sulu.io/de.route2', $routes);
        $this->assertArrayHasKey('sulu.io/de.de.route2', $routes);
        $this->assertArrayHasKey('sulu.com.route1', $routes);
        $this->assertArrayHasKey('sulu.com.en.route1', $routes);
        $this->assertArrayHasKey('sulu.com.route2', $routes);
        $this->assertArrayHasKey('sulu.com.en.route2', $routes);

        $this->assertEquals('/de/example/route1', $routeCollection->get('sulu.io/de.route1')->getPath());
        $this->assertEquals('/de/example/route1', $routeCollection->get('sulu.io/de.de.route1')->getPath());
        $this->assertEquals('/de/route2', $routeCollection->get('sulu.io/de.route2')->getPath());
        $this->assertEquals('/de/route2', $routeCollection->get('sulu.io/de.de.route2')->getPath());
        $this->assertEquals('/example/route1', $routeCollection->get('sulu.com.route1')->getPath());
        $this->assertEquals('/example/route1', $routeCollection->get('sulu.com.en.route1')->getPath());
        $this->assertEquals('/route2', $routeCollection->get('sulu.com.route2')->getPath());
        $this->assertEquals('/route2', $routeCollection->get('sulu.com.en.route2')->getPath());
    }

    public function testLoadWithCustomUrls()
    {
        $importedRouteCollection = new RouteCollection();
        $importedRouteCollection->add('route1', new Route('/example/route1'));
        $importedRouteCollection->add('route2', new Route('/route2'));

        $portal1 = new Portal();
        $portal1->setKey('sulu_lo');
        $portal1->setLocalizations($this->localizations);

        $portal2 = new Portal();
        $portal2->setKey('sulu_com');
        $portal2->setLocalizations($this->localizations);

        $portalInformations = [
            new PortalInformation(null, null, $portal1, $this->localizations[0], 'sulu.io/de', null, null, null, true),
            new PortalInformation(null, null, $portal1, $this->localizations[1], 'sulu.io/en'),
            new PortalInformation(null, null, $portal2, null, 'sulu.com/*'),
        ];

        $this->loaderResolver->resolve(Argument::any(), Argument::any())->willReturn($this->loader->reveal());
        $this->loader->load(Argument::any(), Argument::any())->willReturn($importedRouteCollection);
        $this->webspaceManager->getPortalInformations(Argument::any())->willReturn($portalInformations);

        $routeCollection = $this->portalLoader->load('', 'portal');

        $this->assertCount(12, $routeCollection);

        $routes = $routeCollection->getIterator();
        $this->assertArrayHasKey('sulu.com/*.de.route1', $routes);
        $this->assertArrayHasKey('sulu.com/*.en.route1', $routes);
        $this->assertArrayHasKey('sulu.com/*.de.route2', $routes);
        $this->assertArrayHasKey('sulu.com/*.en.route2', $routes);

        $this->assertEquals('/de/example/route1', $routeCollection->get('sulu.com/*.de.route1')->getPath());
        $this->assertEquals('/en/example/route1', $routeCollection->get('sulu.com/*.en.route1')->getPath());
        $this->assertEquals('/de/route2', $routeCollection->get('sulu.com/*.de.route2')->getPath());
        $this->assertEquals('/en/route2', $routeCollection->get('sulu.com/*.en.route2')->getPath());
    }
}
