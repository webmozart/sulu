<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Document;

use PHPCR\NodeInterface;
use Sulu\Component\DocumentManager\Behavior\Audit\BlameBehavior;
use Sulu\Component\DocumentManager\Behavior\Audit\TimestampBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\NodeNameBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\ParentBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\PathBehavior;
use Sulu\Component\DocumentManager\Behavior\Mapping\UuidBehavior;

/**
 * Contains information about custom-urls and the relations to the routes.
 */
class CustomUrlDocument implements
    CustomUrlBehavior,
    NodeNameBehavior,
    TimestampBehavior,
    BlameBehavior,
    UuidBehavior,
    PathBehavior,
    ParentBehavior
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var string
     */
    protected $baseDomain;

    /**
     * @var array
     */
    protected $domainParts;

    /**
     * @var bool
     */
    protected $multilingual;

    /**
     * @var bool
     */
    protected $canonical;

    /**
     * @var bool
     */
    protected $redirect;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $changed;

    /**
     * @var int
     */
    protected $creator;

    /**
     * @var int
     */
    protected $changer;

    /**
     * @var NodeInterface
     */
    protected $parent;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $nodeName;

    /**
     * {@inheritdoc}
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Set published state.
     *
     * @param bool $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDomain()
    {
        return $this->baseDomain;
    }

    /**
     * Set base domain.
     *
     * @param string $baseDomain
     */
    public function setBaseDomain($baseDomain)
    {
        $this->baseDomain = $baseDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainParts()
    {
        return $this->domainParts;
    }

    /**
     * Set domain parts.
     *
     * @param array $domainParts
     */
    public function setDomainParts($domainParts)
    {
        $this->domainParts = $domainParts;
    }

    /**
     * {@inheritdoc}
     */
    public function isMultilingual()
    {
        return $this->multilingual;
    }

    /**
     * Return true if multilingual is enabled.
     *
     * @param boolean $multilingual
     */
    public function setMultilingual($multilingual)
    {
        $this->multilingual = $multilingual;
    }

    /**
     * {@inheritdoc}
     */
    public function isCanonical()
    {
        return $this->canonical;
    }

    /**
     * Return true if canonicle is enabled.
     *
     * @param boolean $canonical
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return $this->redirect;
    }

    /**
     * Return true if redirect is enabled.
     *
     * @param boolean $redirect
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChanger()
    {
        return $this->changer;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
