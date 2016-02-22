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

use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\KeyWord;

/**
 * Manages keyword for categories.
 */
interface KeyWordManagerInterface
{
    /**
     * Add given keyword to the category.
     *
     * @param KeyWord $keyWord
     * @param Category $category
     *
     * @return KeyWord
     */
    public function save(KeyWord $keyWord, Category $category);

    /**
     * Removes keyword from given category.
     *
     * @param KeyWord $keyWord
     * @param Category $category
     *
     * @return bool true if keyword is deleted completely from the database otherwise only from the category
     */
    public function delete(KeyWord $keyWord, Category $category);
}
