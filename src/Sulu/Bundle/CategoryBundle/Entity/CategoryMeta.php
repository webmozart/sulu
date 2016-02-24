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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CategoryMeta.
 */
class CategoryMeta
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var int
     */
    private $id;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Collection
     */
    private $keyWords;

    public function __construct()
    {
        $this->keyWords = new ArrayCollection();
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return CategoryMeta
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return CategoryMeta
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return CategoryMeta
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category.
     *
     * @param Category $category
     *
     * @return CategoryMeta
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add keyWord
     *
     * @param KeyWord $keyWord
     *
     * @return Category
     */
    public function addKeyWord(KeyWord $keyWord)
    {
        $this->keyWords[] = $keyWord;

        return $this;
    }

    /**
     * Remove keyWord
     *
     * @param KeyWord $keyWord
     */
    public function removeKeyWord(KeyWord $keyWord)
    {
        $this->keyWords->removeElement($keyWord);
    }

    /**
     * Get keyWords
     *
     * @return Collection
     */
    public function getKeyWords()
    {
        return $this->keyWords;
    }

    /**
     * Returns true if given keyword already linked with the category.
     *
     * @return bool
     */
    public function hasKeyWord(KeyWord $keyWord)
    {
        return $this->getKeyWords()->exists(
            function ($key, KeyWord $element) use ($keyWord) {
                return $element->compareWith($keyWord);
            }
        );
    }
}
