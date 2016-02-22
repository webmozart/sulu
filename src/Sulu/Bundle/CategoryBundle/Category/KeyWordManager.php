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
            $keyWord = $synonym;
        }

        // if key-word already exists in category
        if ($category->hasKeyWord($keyWord)) {
            return $keyWord;
        }

        $category->addKeyWord($keyWord);
        $keyWord->addCategory($category);

        return $keyWord;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(KeyWord $keyWord, Category $category)
    {
        $keyWord->removeCategory($category);
        $category->removeKeyWord($keyWord);

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
