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

/**
 * Interface for custom-url data-class.
 */
interface CustomUrlBehavior
{
    /**
     * Returns title of custom-url.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns state of custom-url.
     *
     * @return string
     */
    public function isPublished();
}
