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
use Prophecy\Argument;
use Sulu\Bundle\CategoryBundle\Category\KeyWordManager;
use Sulu\Bundle\CategoryBundle\Category\KeyWordRepositoryInterface;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\CategoryMeta;
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
        }
        $repository->findByKeyWord($keyWordString, $locale)->willReturn($otherKeyWord ? $otherKeyWord->reveal() : null);

        $keyWord = $this->prophesize(KeyWord::class);
        $keyWord->getKeyWord()->willReturn($keyWordString);
        $keyWord->getLocale()->willReturn($locale);
        $keyWord->getId()->shouldNotBeCalled();

        $categoryMeta = $this->prophesize(CategoryMeta::class);
        $categoryMeta->hasKeyWord($exists ? $otherKeyWord->reveal() : $keyWord->reveal())->willReturn($has);
        $categoryMeta->addKeyWord($exists ? $otherKeyWord->reveal() : $keyWord->reveal())
            ->shouldBeCalledTimes($has ? 0 : 1);
        $categoryMeta->setChanged(Argument::any())->shouldBeCalledTimes($has ? 0 : 1);

        $category = $this->prophesize(Category::class);
        $category->findMetaByLocale($locale)->willReturn($categoryMeta->reveal());
        $category->setChanged(Argument::any())->shouldBeCalledTimes($has ? 0 : 1);

        if ($exists) {
            $otherKeyWord->addCategoryMeta($categoryMeta->reveal())->shouldBeCalledTimes($has ? 0 : 1);
        } else {
            $keyWord->addCategoryMeta($categoryMeta->reveal())->shouldBeCalledTimes($has ? 0 : 1);
        }

        $manager = new KeyWordManager($repository->reveal(), $entityManager->reveal());
        $result = $manager->save($keyWord->reveal(), $category->reveal());

        $this->assertEquals($exists ? $otherKeyWord->reveal() : $keyWord->reveal(), $result);
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
        $keyWord->isReferenced()->willReturn($multipleReferenced);

        $categoryMeta = $this->prophesize(CategoryMeta::class);
        $categoryMeta->hasKeyWord($keyWord->reveal())->willReturn(true);
        $categoryMeta->removeKeyWord($keyWord->reveal())->shouldBeCalled();
        $categoryMeta->setChanged(Argument::any())->shouldBeCalled();

        $category = $this->prophesize(Category::class);
        $category->findMetaByLocale($locale)->willReturn($categoryMeta->reveal());
        $category->setChanged(Argument::any())->shouldBeCalled();

        $keyWord->removeCategoryMeta($categoryMeta->reveal())->shouldBeCalled();

        if (!$multipleReferenced) {
            $entityManager->remove($keyWord->reveal())->shouldBeCalled();
        }

        $manager = new KeyWordManager($repository->reveal(), $entityManager->reveal());
        $result = $manager->delete($keyWord->reveal(), $category->reveal());

        $this->assertEquals(!$multipleReferenced, $result);
    }
}
