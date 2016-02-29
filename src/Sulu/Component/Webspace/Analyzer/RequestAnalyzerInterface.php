<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Webspace\Analyzer;

use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Portal;
use Sulu\Component\Webspace\PortalInformation;
use Sulu\Component\Webspace\Segment;
use Sulu\Component\Webspace\Webspace;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines the interface for the request analyzer, who is responsible for return the required information for the
 * current request.
 */
interface RequestAnalyzerInterface
{
    /**
     * Type for a full match.
     *
     * A full match is when the URL completely starts with the given URL
     */
    const MATCH_TYPE_FULL = 1;

    /**
     * Type for a partial match.
     *
     * A partial match is when only the partial URL matches the given URL
     */
    const MATCH_TYPE_PARTIAL = 2;

    /**
     * Type for a redirect.
     *
     * A redirect is when the given URL is just defined to be a redirect
     */
    const MATCH_TYPE_REDIRECT = 3;

    /**
     * Analyzes the current request, and saves the values for portal, language, country and segment for further usage.
     *
     * @param Request $request The request to analyze
     *
     * @return
     */
    public function analyze(Request $request);

    /**
     * Update localization in this class and given request.
     *
     * @param string $locale
     * @param Request $request
     */
    public function updateLocale($locale, Request $request);

    /**
     * Returns the current match type for this request.
     *
     * @return int
     */
    public function getMatchType();

    /**
     * Returns the current webspace for this request.
     *
     * @return Webspace
     */
    public function getWebspace();

    /**
     * Returns the current portal for this request.
     *
     * @return Portal
     */
    public function getPortal();

    /**
     * Returns the current segment for this request.
     *
     * @return Segment
     */
    public function getSegment();

    /**
     * Returns the current localization for this Request.
     *
     * @return Localization
     */
    public function getCurrentLocalization();

    /**
     * Returns the redirect url.
     *
     * @return string
     */
    public function getRedirect();

    /**
     * Returns the url of the current portal.
     *
     * @return string
     */
    public function getPortalUrl();

    /**
     * Returns the path of the current request, which is the url without host, language and so on.
     *
     * @return string
     */
    public function getResourceLocator();

    /**
     * Returns the prefix required before the resource locator.
     *
     * @return string
     */
    public function getResourceLocatorPrefix();

    /**
     * Returns the post parameters.
     *
     * @return array
     */
    public function getPostParameters();

    /**
     * Returns the get parameters.
     *
     * @return array
     */
    public function getGetParameters();

    /**
     * Returns the analytics key.
     *
     * @return string
     */
    public function getAnalyticsKey();

    /**
     * Returns portal-information of request.
     *
     * @return PortalInformation
     */
    public function getPortalInformation();
}
