<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Category;

use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\KeyWord;

/**
 * Manages keyword for categories.
 */
class KeyWordManager implements KeyWordManagerInterface
{
    /**
     * @var KeyWordRepositoryInterface
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(KeyWordRepositoryInterface $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(KeyWord $keyWord, Category $category)
    {
        if (null !== $synonym = $this->findSynonym($keyWord)) {
            // reset entity and remove it from category
            if ($this->entityManager->contains($keyWord)) {
                $this->entityManager->refresh($keyWord);
            }
            $this->delete($keyWord, $category);

            // link this synonym to the category
            $keyWord = $synonym;
        }

        $categoryMeta = $category->findTranslationByLocale($keyWord->getLocale());

        // if key-word already exists in category
        if ($categoryMeta->hasKeyWord($keyWord)) {
            return $keyWord;
        }

        $keyWord->addCategoryTranslation($categoryMeta);
        $categoryMeta->addKeyWord($keyWord);

        // FIXME category and meta will no be updated if only keyword was changed
        $category->setChanged(new \DateTime());
        $categoryMeta->setChanged(new \DateTime());

        return $keyWord;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(KeyWord $keyWord, Category $category)
    {
        $categoryMeta = $category->findTranslationByLocale($keyWord->getLocale());

        $keyWord->removeCategoryTranslation($categoryMeta);
        $categoryMeta->removeKeyWord($keyWord);

        // FIXME category and meta will no be updated if only keyword was changed
        $category->setChanged(new \DateTime());
        $categoryMeta->setChanged(new \DateTime());

        if ($keyWord->isReferenced()) {
            return false;
        }

        $this->entityManager->remove($keyWord);

        return true;
    }

    /**
     * Find the same key-word in the database or returns null if no synonym exists.
     *
     * @param KeyWord $keyWord
     *
     * @return KeyWord|null
     */
    private function findSynonym(KeyWord $keyWord)
    {
        return $this->repository->findByKeyWord($keyWord->getKeyWord(), $keyWord->getLocale());
    }
}
