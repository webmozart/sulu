<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Manager;

use Sulu\Component\CustomUrl\Document\CustomUrlDocument;

/**
 * Interface for custom-url manager.
 */
interface CustomUrlManagerInterface
{
    /**
     * Create a new custom-url with given data.
     *
     * @param string $webspaceKey
     * @param array $data
     *
     * @return CustomUrlDocument
     */
    public function create($webspaceKey, array $data);

    /**
     * Returns a list of custom-url data (in a assoc-array).
     *
     * @param string $webspaceKey
     *
     * @return array
     */
    public function readList($webspaceKey);

    /**
     * Returns a single custom-url object identified by uuid.
     *
     * @param string $uuid
     *
     * @return CustomUrlDocument
     */
    public function read($uuid);

    /**
     * Update a single custom-url object identified by uuid with given data.
     *
     * @param string $uuid
     * @param array $data
     *
     * @return CustomUrlDocument
     */
    public function update($uuid, array $data);

    /**
     * Delete custom-url identified by uuid.
     *
     * @param string $uuid
     */
    public function delete($uuid);

    /**
     * Returns existing fields in document.
     *
     * @return string[]
     */
    public function getFields();
}