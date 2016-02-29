<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\MediaBundle\Tests\Functional\SearchIntegration;

use Sulu\Bundle\ContentBundle\Document\HomeDocument;
use Sulu\Bundle\MediaBundle\Api\Media as ApiMedia;
use Sulu\Bundle\MediaBundle\Content\MediaSelectionContainer;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\NodeManager;

class SearchIntegrationTest extends SuluTestCase
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var NodeManager
     */
    private $nodeManager;

    /**
     * @var HomeDocument
     */
    private $webspaceDocument;

    /**
     * @var ApiMedia
     */
    private $media;

    /**
     * @var MediaSelectionContainer
     */
    private $mediaSelectionContainer;

    protected function getKernelConfiguration()
    {
        return [
            'sulu_context' => 'website',
        ];
    }

    public function setUp()
    {
        $this->initPhpcr();
        $this->documentManager = $this->container->get('sulu_document_manager.document_manager');
        $this->nodeManager = $this->container->get('sulu_document_manager.node_manager');
        $this->webspaceDocument = $this->documentManager->find('/cmf/sulu_io/contents');

        $mediaEntity = new Media();
        $tagManager = $this->getMock('Sulu\Bundle\TagBundle\Tag\TagManagerInterface');
        $this->media = new ApiMedia($mediaEntity, 'de', null, $tagManager);

        $this->mediaSelectionContainer = $this->prophesize(MediaSelectionContainer::class);
        $this->mediaSelectionContainer->getData('de')->willReturn([$this->media]);
        $this->mediaSelectionContainer->toArray()->willReturn(null);
    }

    public function provideIndex()
    {
        return [
            ['170x170', null],
            ['invalid', '\InvalidArgumentException'],
        ];
    }

    /**
     * @dataProvider provideIndex
     */
    public function testIndex($format, $expectedException)
    {
        if ($expectedException) {
            $this->setExpectedException($expectedException);
        }

        $this->media->setFormats([
            $format => 'myimage.jpg',
        ]);

        $testAdapter = $this->container->get('massive_search.adapter.test');

        $document = $this->documentManager->create('page');
        $document->setTitle('Hallo');
        $document->setResourceSegment('/hallo/fo');
        $document->setStructureType('images');
        $document->setParent($this->webspaceDocument);
        $document->getStructure()->bind([
            'images' => $this->mediaSelectionContainer->reveal(),
        ], false);
        $this->documentManager->persist($document, 'de');
        $this->documentManager->flush();

        $documents = $testAdapter->getDocuments();
        $this->assertTrue(count($documents) >= 1);
        $document = end($documents);
        $this->assertInstanceOf('Massive\Bundle\SearchBundle\Search\Document', $document);
        $this->assertEquals('myimage.jpg', $document->getImageUrl());
    }

    public function testIndexNoMedia()
    {
        $document = $this->documentManager->create('page');
        $document->setStructureType('images');
        $document->setTitle('Hallo');
        $document->setResourceSegment('/hallo');
        $document->setParent($this->webspaceDocument);
        $this->documentManager->persist($document, 'de');
        $this->documentManager->flush();
    }
}
