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

use Sulu\Bundle\CategoryBundle\Entity\KeyWord;
use Sulu\Component\Persistence\Repository\RepositoryInterface;

/**
 * Interface for keyword repository.
 */
interface KeyWordRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns keyword.
     *
     * @param int $id
     *
     * @return KeyWord
     */
    public function findById($id);

    /**
     * Returns keyword.
     *
     * @param string $keyWord
     * @param string $locale
     *
     * @return KeyWord
     */
    public function findByKeyWord($keyWord, $locale);
}
