<?php

namespace Sulu\Component\Content\Mapper\Translation;

class MultipleTranslatedPropertyTest extends \PHPUnit_Framework_TestCase
{
    protected $properties;

    public function setUp()
    {
        $this->properties = new MultipleTranslatedProperties(
            array(
                'changer',
                'shadow-base'
            ),
            'i18n',
            '-' 
        );

        $this->node = $this->getMockBuilder('Jackalope\Node')->disableOriginalConstructor()->getMock();
        $this->property1 = $this->getMockBuilder('Jackalope\Property')->disableOriginalConstructor()->getMock();
        $this->property2 = $this->getMockBuilder('Jackalope\Property')->disableOriginalConstructor()->getMock();
        $this->property3 = $this->getMockBuilder('Jackalope\Property')->disableOriginalConstructor()->getMock();
        $this->property4 = $this->getMockBuilder('Jackalope\Property')->disableOriginalConstructor()->getMock();
        $this->property5 = $this->getMockBuilder('Jackalope\Property')->disableOriginalConstructor()->getMock();

        $this->property1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('i18n:fr-template'));
        $this->property2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bas:barfoo'));
        $this->property3->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('i18n:it-barfoo'));
        $this->property4->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('i18n:de-template'));
        $this->property5->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('i18n:de-bbbaaaa'));

        $this->node->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue(array(
                $this->property1,
                $this->property2,
                $this->property3,
                $this->property4,
            )));
    }

    public function testGetLanguages()
    {
        $languages = $this->properties->getLanguagesForNode($this->node);

        // languages are only counted if they are on the "template" property
        $this->assertEquals(array('fr', 'de'), $languages);
    }
}