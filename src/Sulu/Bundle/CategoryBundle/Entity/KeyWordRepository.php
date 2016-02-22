<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Entity;

use Sulu\Bundle\CategoryBundle\Category\KeyWordRepositoryInterface;
use Sulu\Component\Persistence\Repository\ORM\EntityRepository;

/**
 * Implementation of keyword repository.
 */
class KeyWordRepository extends EntityRepository implements KeyWordRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->find($id);
    }
    /**
     * {@inheritdoc}
     */
    public function findByKeyWord($keyWord, $locale)
    {
        return $this->findOneBy(['keyWord' => $keyWord, 'locale' => $locale]);
    }
}
