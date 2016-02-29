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
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Security\Authentication\UserInterface;

/**
 * CategoryTranslation.
 */
class CategoryTranslation implements AuditableInterface
{
    /**
     * @var string
     */
    private $translation;

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

    /**
     * @var UserInterface
     */
    private $creator;

    /**
     * @var UserInterface
     */
    private $changer;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $changed;

    public function __construct()
    {
        $this->keyWords = new ArrayCollection();
    }

    /**
     * Set translation.
     *
     * @param string $translation
     *
     * @return CategoryTranslation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * Get translation.
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return CategoryTranslation
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
     * @return CategoryTranslation
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

    /**
     * {@inheritdoc}
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param UserInterface $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChanger()
    {
        return $this->changer;
    }

    /**
     * @param UserInterface $changer
     */
    public function setChanger($changer)
    {
        $this->changer = $changer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * {@inheritdoc}
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param \DateTime $changed
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
    }
}
