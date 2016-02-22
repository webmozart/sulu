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
 * KeyWord.
 */
class KeyWord implements AuditableInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $keyWord;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection
     */
    private $categories;

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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return KeyWord
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set keyWord
     *
     * @param string $keyWord
     *
     * @return KeyWord
     */
    public function setKeyWord($keyWord)
    {
        $this->keyWord = $keyWord;

        return $this;
    }

    /**
     * Get keyWord
     *
     * @return string
     */
    public function getKeyWord()
    {
        return $this->keyWord;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return KeyWord
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
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

    /**
     * @return bool
     */
    public function isReferencedMultiple()
    {
        return $this->getCategories()->count() > 1;
    }

    /**
     * @return bool
     */
    public function isReferenced()
    {
        return $this->getCategories()->count() > 0;
    }

    /**
     * @param KeyWord $keyWord
     *
     * @return bool
     */
    public function compareWith(KeyWord $keyWord)
    {
        return $keyWord->getKeyWord() === $this->getKeyWord() && $keyWord->getLocale() === $this->getLocale();
    }
}
