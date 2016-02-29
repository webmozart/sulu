<?php
/*
 * This file is part of the Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\WebsiteBundle\Twig\Core;

/**
 * Twig extension providing generally useful utilities which are available
 * in the Sulu\Component\Util namespace.
 */
class UtilTwigExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'sulu_util';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sulu_util_multisort', 'Sulu\Component\Util\SortUtils::multisort'),
            new \Twig_SimpleFilter('sulu_util_filter', 'Sulu\Component\Util\ArrayUtils::filter'),
        ];
    }
}
