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
use Sulu\Bundle\CategoryBundle\Entity\CategoryTranslation;
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

        $categoryTranslation = $this->prophesize(CategoryTranslation::class);
        $categoryTranslation->hasKeyWord($exists ? $otherKeyWord->reveal() : $keyWord->reveal())->willReturn($has);
        $categoryTranslation->addKeyWord($exists ? $otherKeyWord->reveal() : $keyWord->reveal())
            ->shouldBeCalledTimes($has ? 0 : 1);
        $categoryTranslation->setChanged(Argument::any())->shouldBeCalledTimes($has ? 0 : 1);

        $category = $this->prophesize(Category::class);
        $category->findTranslationByLocale($locale)->willReturn($categoryTranslation->reveal());
        $category->setChanged(Argument::any())->shouldBeCalledTimes($has ? 0 : 1);

        if ($exists) {
            $otherKeyWord->addCategoryTranslation($categoryTranslation->reveal())->shouldBeCalledTimes($has ? 0 : 1);
        } else {
            $keyWord->addCategoryTranslation($categoryTranslation->reveal())->shouldBeCalledTimes($has ? 0 : 1);
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

        $categoryTranslation = $this->prophesize(CategoryTranslation::class);
        $categoryTranslation->hasKeyWord($keyWord->reveal())->willReturn(true);
        $categoryTranslation->removeKeyWord($keyWord->reveal())->shouldBeCalled();
        $categoryTranslation->setChanged(Argument::any())->shouldBeCalled();

        $category = $this->prophesize(Category::class);
        $category->findTranslationByLocale($locale)->willReturn($categoryTranslation->reveal());
        $category->setChanged(Argument::any())->shouldBeCalled();

        $keyWord->removeCategoryTranslation($categoryTranslation->reveal())->shouldBeCalled();

        if (!$multipleReferenced) {
            $entityManager->remove($keyWord->reveal())->shouldBeCalled();
        }

        $manager = new KeyWordManager($repository->reveal(), $entityManager->reveal());
        $result = $manager->delete($keyWord->reveal(), $category->reveal());

        $this->assertEquals(!$multipleReferenced, $result);
    }
}
