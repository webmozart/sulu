<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Category\Exception;

use Sulu\Bundle\CategoryBundle\Entity\KeyWord;
use Sulu\Component\Rest\Exception\RestException;

/**
 * Keyword is used in multiple categories and translations.
 */
class KeyWordIsMultipleReferencedException extends RestException
{
    /**
     * @var KeyWord
     */
    private $keyWord;

    public function __construct(KeyWord $keyWord)
    {
        parent::__construct(
            sprintf('The key-word "%s" is used in multiple categories or translations.', $keyWord->getKeyWord()),
            2002
        );

        $this->keyWord = $keyWord;
    }

    /**
     * @return KeyWord
     */
    public function getKeyWord()
    {
        return $this->keyWord;
    }
}
