<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CategoryBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\CategoryBundle\Category\CategoryManagerInterface;
use Sulu\Bundle\CategoryBundle\Category\KeyWordManager;
use Sulu\Bundle\CategoryBundle\Category\KeyWordRepositoryInterface;
use Sulu\Bundle\CategoryBundle\Entity\KeyWord;
use Sulu\Component\Rest\RestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides key-words for categories.
 *
 * @RouteResource("key-word")
 */
class KeyWordController extends RestController implements ClassResourceInterface, SecuredControllerInterface
{
    /**
     * Creates new key-word for given category.
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function postAction($categoryId, Request $request)
    {
        /** @var KeyWord $keyWord */
        $keyWord = $this->getKeyWordRepository()->createNew();
        $category = $this->getCategoryManager()->findById($categoryId);
        $keyWord->setKeyWord($request->get('keyWord'));
        $keyWord->setLocale($request->get('locale'));

        $keyWord = $this->getKeyWordManager()->save($keyWord, $category);

        $this->getEntityManager()->persist($keyWord);
        $this->getEntityManager()->flush();

        return $this->handleView($this->view($keyWord));
    }

    /**
     * Updates given key-word for given category.
     *
     * @param int $categoryId
     * @param int $keyWordId
     * @param Request $request
     *
     * @return Response
     */
    public function putAction($categoryId, $keyWordId, Request $request)
    {
        $keyWord = $this->getKeyWordRepository()->findById($keyWordId);

        // overwrite existing keyword if force is present
        if ($request->get('force') === null && $keyWord->isReferencedMultiple()) {
            // return conflict if key-word is used by other categories
            return $this->handleView($this->view($keyWord, 409));
        }

        // TODO handle force = overwrite and force = detach

        $category = $this->getCategoryManager()->findById($categoryId);
        $keyWord->setKeyWord($request->get('keyWord'));

        $keyWord = $this->getKeyWordManager()->save($keyWord, $category);

        $this->getEntityManager()->flush();

        return $this->handleView($this->view($keyWord));
    }

    /**
     * Delete given key-word from given category.
     *
     * @param int $categoryId
     * @param int $keyWordId
     *
     * @return Response
     */
    public function deleteAction($categoryId, $keyWordId)
    {
        $keyWord = $this->getKeyWordRepository()->findById($keyWordId);
        $category = $this->getCategoryManager()->findById($categoryId);
        $this->getKeyWordManager()->delete($keyWord, $category);

        $this->getEntityManager()->flush();

        return $this->handleView($this->view());
    }

    /**
     * @return KeyWordManager
     */
    private function getKeyWordManager()
    {
        return $this->get('sulu_category.keyword_manager');
    }

    /**
     * @return KeyWordRepositoryInterface
     */
    private function getKeyWordRepository()
    {
        return $this->get('sulu_category.keyword_repository');
    }

    /**
     * @return CategoryManagerInterface
     */
    private function getCategoryManager()
    {
        return $this->get('sulu_category.category_manager');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityContext()
    {
        return 'sulu.settings.categories';
    }
}
