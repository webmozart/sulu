<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Tests\Unit\Category;

use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\CategoryBundle\Category\KeyWordManager;
use Sulu\Bundle\CategoryBundle\Category\KeyWordRepositoryInterface;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\KeyWord;

class KeyWordManagerTest extends \PHPUnit_Framework_TestCase
{
    public function provideSaveData()
    {
        return [
            [],
            [true],
            [true, true],
            [false, true],
        ];
    }

    /**
     * @dataProvider provideSaveData
     */
    public function testSave($exists = false, $has = false, $keyWordString = 'Test', $locale = 'de')
    {
        $repository = $this->prophesize(KeyWordRepositoryInterface::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $otherKeyWord = null;
        if ($exists) {
            $otherKeyWord = $this->prophesize(KeyWord::class);
            $otherKeyWord->getKeyWord()->willReturn($keyWordString);
            $otherKeyWord->getLocale()->willReturn($locale);
            $otherKeyWord->getId()->willReturn(15);
            $otherKeyWord = $otherKeyWord->reveal();
        }
        $repository->findByKeyWord($keyWordString, $locale)->willReturn($otherKeyWord);

        $keyWord = $this->prophesize(KeyWord::class);
        $keyWord->getKeyWord()->willReturn($keyWordString);
        $keyWord->getLocale()->willReturn($locale);
        $keyWord->getId()->shouldNotBeCalled();

        $category = $this->prophesize(Category::class);
        $category->hasKeyWord($exists ? $otherKeyWord : $keyWord->reveal())->willReturn($has);
        $category->addKeyWord($exists ? $otherKeyWord : $keyWord->reveal())->shouldBeCalledTimes($has ? 0 : 1);

        $manager = new KeyWordManager($repository->reveal(), $entityManager->reveal());
        $result = $manager->save($keyWord->reveal(), $category->reveal());

        $this->assertEquals($exists ? $otherKeyWord : $keyWord->reveal(), $result);
    }

    public function provideDeleteData()
    {
        return [
            [],
            [true],
        ];
    }

    /**
     * @dataProvider provideDeleteData
     */
    public function testDelete($multipleReferenced = false, $keyWordString = 'Test', $locale = 'de')
    {
        $repository = $this->prophesize(KeyWordRepositoryInterface::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $keyWord = $this->prophesize(KeyWord::class);
        $keyWord->getKeyWord()->willReturn($keyWordString);
        $keyWord->getLocale()->willReturn($locale);
        $keyWord->getId()->shouldNotBeCalled();
        $keyWord->isReferencedMultiple()->willReturn($multipleReferenced);

        $category = $this->prophesize(Category::class);
        $category->removeKeyWord($keyWord->reveal())->shouldBeCalled();

        if (!$multipleReferenced) {
            $entityManager->remove($keyWord->reveal())->shouldBeCalled();
        }

        $manager = new KeyWordManager($repository->reveal(), $entityManager->reveal());
        $result = $manager->delete($keyWord->reveal(), $category->reveal());

        $this->assertEquals(!$multipleReferenced, $result);
    }
}
