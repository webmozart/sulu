<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Tests\Functional\Xml;

use InvalidArgumentException;
use Sulu\Component\Content\Template\TemplateReader;

class TemplateReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testReadTemplate()
    {
        $template = array(
            'key' => 'template',
            'view' => 'page.html.twig',
            'controller' => 'SuluContentBundle:Default:index',
            'cacheLifetime' => 2400,
            'properties' => array(
                'title' => array(
                    'name' => 'title',
                    'title' => 'properties.title',
                    'type' => 'text_line',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.name',
                            'priority' => null
                        ),
                        array(
                            'name' => 'sulu.node.title',
                            'priority' => 10
                        )
                    ),
                    'params' => array()
                ),
                'url' => array(
                    'name' => 'url',
                    'title' => 'properties.url',
                    'type' => 'resource_locator',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.rlp.part',
                            'priority' => 1
                        )
                    ),
                    'params' => array()
                ),
                'article' => array(
                    'name' => 'article',
                    'title' => null,
                    'type' => 'text_area',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => false,
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.title',
                            'priority' => 5
                        )
                    ),
                    'params' => array()
                ),
                'pages' => array(
                    'name' => 'pages',
                    'title' => null,
                    'type' => 'smart_content_selection',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => false,
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.title',
                            'priority' => null
                        )
                    ),
                    'params' => array()
                ),
                'images' => array(
                    'name' => 'images',
                    'title' => null,
                    'type' => 'image_selection',
                    'minOccurs' => 0,
                    'maxOccurs' => 2,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => null,
                    'tags' => array(),
                    'params' => array(
                        array(
                            'name' => 'minLinks',
                            'value' => 1
                        ),
                        array(
                            'name' => 'maxLinks',
                            'value' => 10
                        )
                    )
                )
            )
        );

        $templateReader = new TemplateReader();
        $result = $templateReader->load(__DIR__ . '/../../../Resources/DataFixtures/Template/template.xml');
        $this->assertEquals($template, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testReadTypesInvalidPath()
    {
        $templateReader = new TemplateReader();
        $templateReader->load(
            __DIR__ . '/../../../Resources/DataFixtures/Template/template_not_exists.xml'
        );
    }

    public function testReadTypesEmptyProperties()
    {
        $template = array(
            'key' => 'template',
            'view' => 'page.html.twig',
            'controller' => 'SuluContentBundle:Default:index',
            'cacheLifetime' => 2400,
            'properties' => array()
        );

        $this->setExpectedException(
            '\Sulu\Component\Content\Template\Exception\InvalidXmlException',
            'The given XML is invalid! Tag(s) sulu.node.name required but not found'
        );
        $templateReader = new TemplateReader();
        $result = $templateReader->load(
            __DIR__ . '/../../../Resources/DataFixtures/Template/template_missing_properties.xml'
        );
        $this->assertEquals($template, $result);
    }

    /**
     * @expectedException \Sulu\Component\Content\Template\Exception\InvalidXmlException
     */
    public function testReadTypesMissingMandatory()
    {
        $templateReader = new TemplateReader();
        $templateReader->load(__DIR__ . '/../../../Resources/DataFixtures/Template/template_missing_mandatory.xml');
    }

    public function testReadBlockTemplate()
    {
        $template = array(
            'key' => 'complex',
            'view' => 'ClientWebsiteBundle:Website:complex.html.twig',
            'controller' => 'SuluWebsiteBundle:Default:index',
            'cacheLifetime' => '4800',
            'properties' => array(
                'title' => array(
                    'name' => 'title',
                    'title' => 'properties.title',
                    'type' => 'text_line',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.name',
                            'priority' => null
                        ),
                        array(
                            'name' => 'sulu.node.title',
                            'priority' => 10
                        )
                    ),
                    'params' => array()
                ),
                'url' => array(
                    'name' => 'url',
                    'title' => 'properties.url',
                    'type' => 'resource_locator',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.rlp.part',
                            'priority' => 1
                        )
                    ),
                    'params' => array()
                ),
                'article' => array(
                    'name' => 'article',
                    'title' => null,
                    'type' => 'text_editor',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(),
                    'params' => array()
                ),
                'block1' => array(
                    'name' => 'block1',
                    'title' => 'properties.block1',
                    'default-type' => 'default',
                    'minOccurs' => '2',
                    'maxOccurs' => '10',
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'type' => 'block',
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.block',
                            'priority' => 20
                        ),
                        array(
                            'name' => 'sulu.test.block',
                            'priority' => 1
                        )
                    ),
                    'params' => array(),
                    'types' => array(
                        'default' => array(
                            'name' => 'default',
                            'title' => null,
                            'properties' => array(
                                'title1.1' => array(
                                    'name' => 'title1.1',
                                    'title' => null,
                                    'type' => 'text_line',
                                    'minOccurs' => null,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags' => array(),
                                    'params' => array()
                                ),
                                'article1.1' => array(
                                    'name' => 'article1.1',
                                    'title' => null,
                                    'type' => 'text_area',
                                    'mandatory' => true,
                                    'minOccurs' => 2,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'tags' => array(),
                                    'params' => array()
                                ),
                                'block1.1' => array(
                                    'name' => 'block1.1',
                                    'title' => null,
                                    'default-type' => 'default',
                                    'minOccurs' => null,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => false,
                                    'type' => 'block',
                                    'tags' => array(),
                                    'params' => array(),
                                    'types' => array(
                                        'default' => array(
                                            'name' => 'default',
                                            'title' => null,
                                            'properties' => array(
                                                'block1.1.1' => array(
                                                    'name' => 'block1.1.1',
                                                    'title' => null,
                                                    'default-type' => 'default',
                                                    'minOccurs' => null,
                                                    'maxOccurs' => null,
                                                    'col' => null,
                                                    'cssClass' => null,
                                                    'mandatory' => false,
                                                    'type' => 'block',
                                                    'tags' => array(),
                                                    'params' => array(),
                                                    'types' => array(
                                                        'default' => array(
                                                            'name' => 'default',
                                                            'title' => null,
                                                            'properties' => array(
                                                                'article1.1.1' => array(
                                                                    'name' => 'article1.1.1',
                                                                    'title' => 'properties.title1',
                                                                    'type' => 'text_area',
                                                                    'minOccurs' => 2,
                                                                    'maxOccurs' => null,
                                                                    'col' => null,
                                                                    'cssClass' => null,
                                                                    'mandatory' => true,
                                                                    'tags' => array(
                                                                        array(
                                                                            'name' => 'sulu.node.title',
                                                                            'priority' => 5
                                                                        )
                                                                    ),
                                                                    'params' => array()
                                                                ),
                                                                'article2.1.2' => array(
                                                                    'name' => 'article2.1.2',
                                                                    'title' => null,
                                                                    'type' => 'text_area',
                                                                    'minOccurs' => 2,
                                                                    'maxOccurs' => null,
                                                                    'col' => null,
                                                                    'cssClass' => null,
                                                                    'mandatory' => true,
                                                                    'tags' => array(),
                                                                    'params' => array()
                                                                ),
                                                                'block1.1.3' => array(
                                                                    'name' => 'block1.1.3',
                                                                    'title' => null,
                                                                    'default-type' => 'default',
                                                                    'minOccurs' => null,
                                                                    'maxOccurs' => null,
                                                                    'col' => null,
                                                                    'cssClass' => null,
                                                                    'mandatory' => false,
                                                                    'type' => 'block',
                                                                    'tags' => array(),
                                                                    'params' => array(),
                                                                    'types' => array(
                                                                        'default' => array(
                                                                            'name' => 'default',
                                                                            'title' => null,
                                                                            'properties' => array(
                                                                                'article1.1.3.1' => array(
                                                                                    'name' => 'article1.1.3.1',
                                                                                    'title' => null,
                                                                                    'type' => 'text_area',
                                                                                    'minOccurs' => 2,
                                                                                    'maxOccurs' => null,
                                                                                    'col' => null,
                                                                                    'cssClass' => null,
                                                                                    'mandatory' => true,
                                                                                    'tags' => array(),
                                                                                    'params' => array()
                                                                                )
                                                                            )
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                ),
                                                'block1.1.2' => array(
                                                    'name' => 'block1.1.2',
                                                    'title' => null,
                                                    'default-type' => 'default',
                                                    'type' => 'block',
                                                    'minOccurs' => null,
                                                    'maxOccurs' => null,
                                                    'col' => null,
                                                    'cssClass' => null,
                                                    'mandatory' => false,
                                                    'tags' => array(),
                                                    'params' => array(),
                                                    'types' => array(
                                                        'default' => array(
                                                            'name' => 'default',
                                                            'title' => null,
                                                            'properties' => array(
                                                                'article1.1.2.1' => array(
                                                                    'name' => 'article1.1.2.1',
                                                                    'title' => null,
                                                                    'type' => 'text_area',
                                                                    'minOccurs' => 2,
                                                                    'maxOccurs' => null,
                                                                    'col' => null,
                                                                    'cssClass' => null,
                                                                    'mandatory' => true,
                                                                    'tags' => array(),
                                                                    'params' => array()
                                                                )
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                'blog' => array(
                    'name' => 'blog',
                    'title' => null,
                    'type' => 'text_editor',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(),
                    'params' => array()
                ),
            )
        );

        $templateReader = new TemplateReader();
        $result = $templateReader->load(__DIR__ . '/../../../Resources/DataFixtures/Template/template_block.xml');


        $this->assertEquals($template, $result);
    }

    public function testDuplicatedPriority()
    {
        $this->setExpectedException(
            '\Sulu\Component\Content\Template\Exception\InvalidXmlException',
            'The given XML is invalid! Priority 10 of tag sulu.node.title exists duplicated'
        );
        $templateReader = new TemplateReader();
        $result = $templateReader->load(
            __DIR__ . '/../../../Resources/DataFixtures/Template/template_duplicated_priority.xml'
        );
    }

    public function testBlockMultipleTypes()
    {
        $template = array(
            'key' => 'complex',
            'view' => 'ClientWebsiteBundle:Website:complex.html.twig',
            'controller' => 'SuluWebsiteBundle:Default:index',
            'cacheLifetime' => '4800',
            'properties' => array(
                'title' => array(
                    'name' => 'title',
                    'title' => 'properties.title',
                    'type' => 'text_line',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.name',
                            'priority' => null
                        ),
                        array(
                            'name' => 'sulu.node.title',
                            'priority' => 10
                        )
                    ),
                    'params' => array()
                ),
                'url' => array(
                    'name' => 'url',
                    'title' => 'properties.url',
                    'type' => 'resource_locator',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(
                        array(
                            'name' => 'sulu.rlp.part',
                            'priority' => 1
                        )
                    ),
                    'params' => array()
                ),
                'block1' => array(
                    'name' => 'block1',
                    'title' => 'properties.block1',
                    'default-type' => 'default',
                    'minOccurs' => '2',
                    'maxOccurs' => '10',
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'type' => 'block',
                    'tags' => array(
                        array(
                            'name' => 'sulu.node.block',
                            'priority' => 20
                        ),
                        array(
                            'name' => 'sulu.test.block',
                            'priority' => 1
                        )
                    ),
                    'params' => array(),
                    'types' => array(
                        'default' => array(
                            'name' => 'default',
                            'title' => 'type.default',
                            'properties' => array(
                                'title' => array(
                                    'name' => 'title',
                                    'title' => null,
                                    'type' => 'text_line',
                                    'minOccurs' => null,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags'=>array(),
                                    'params'=>array()
                                ),
                                'article' => array(
                                    'name' => 'article',
                                    'title' => null,
                                    'type' => 'text_area',
                                    'minOccurs' => 2,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags'=>array(),
                                    'params'=>array()
                                )
                            )
                        ),
                        'test' => array(
                            'name' => 'test',
                            'title' => 'type.test',
                            'properties' => array(
                                'title' => array(
                                    'name' => 'title',
                                    'title'=>null,
                                    'type' => 'text_line',
                                    'minOccurs' => null,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags'=>array(),
                                    'params'=>array()
                                ),
                                'name' => array(
                                    'name' => 'name',
                                    'title'=>null,
                                    'type' => 'text_line',
                                    'minOccurs' => 2,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags'=>array(),
                                    'params'=>array()
                                ),
                                'article' => array(
                                    'name' => 'article',
                                    'title'=>null,
                                    'type' => 'text_editor',
                                    'minOccurs' => 2,
                                    'maxOccurs' => null,
                                    'col' => null,
                                    'cssClass' => null,
                                    'mandatory' => true,
                                    'tags'=>array(),
                                    'params'=>array()
                                )
                            )
                        )
                    )
                ),
                'blog' => array(
                    'name' => 'blog',
                    'title' => null,
                    'type' => 'text_editor',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => true,
                    'tags' => array(),
                    'params' => array()
                )
            )
        );

        $templateReader = new TemplateReader();
        $result = $templateReader->load(__DIR__ . '/../../../Resources/DataFixtures/Template/template_block_types.xml');

        $this->assertEquals($template, $result);
    }

    public function testSections()
    {
        $template = array(
            'key' => 'template',
            'view' => 'page.html.twig',
            'controller' => 'SuluContentBundle:Default:index',
            'cacheLifetime' => 2400,
            'properties' => array(
                'title' => array(
                    'name' => 'title',
                    'title' => 'properties.title',
                    'type' => 'text_line',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => 6,
                    'cssClass' => null,
                    'mandatory' => 1,
                    'tags' => array(
                        '0' => array(
                            'name' => 'sulu.node.name',
                            'priority' => null
                        ),
                        '1' => array(
                            'name' => 'sulu.node.title',
                            'priority' => 10
                        )
                    ),
                    'params' => array()
                ),
                'test' => array(
                    'name' => 'test',
                    'type' => 'section',
                    'title' => 'sections.test',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => 'test',
                    'mandatory' => false,
                    'properties' => array(),
                    'tags' => array(),
                    'params' => array()
                ),
                'pages' => array(
                    'name' => 'pages',
                    'title' => null,
                    'type' => 'smart_content_selection',
                    'minOccurs' => null,
                    'maxOccurs' => null,
                    'col' => null,
                    'cssClass' => null,
                    'mandatory' => false,
                    'tags' => array(
                        '0' => array(
                            'name' => 'sulu.node.title',
                            'priority' => null
                        )
                    ),
                    'params' => array()
                ),
                'images' => array(
                    'name' => 'images',
                    'title' => null,
                    'type' => 'image_selection',
                    'minOccurs' => 0,
                    'maxOccurs' => 2,
                    'col' => 6,
                    'cssClass' => null,
                    'mandatory' => false,
                    'tags' => array(),
                    'params' => array(
                        '0' => array(
                            'name' => 'minLinks',
                            'value' => 1
                        ),
                        '1' => array(
                            'name' => 'maxLinks',
                            'value' => 10
                        )
                    )
                )
            )
        );

        $templateReader = new TemplateReader();
        $result = $templateReader->load(__DIR__ . '/../../../Resources/DataFixtures/Template/template_sections.xml');

        $this->assertEquals($template, $result);
    }

}
