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
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactory;
use Sulu\Component\Rest\ListBuilder\FieldDescriptorInterface;
use Sulu\Component\Rest\ListBuilder\ListRepresentation;
use Sulu\Component\Rest\RestController;
use Sulu\Component\Rest\RestHelperInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides key-words for categories.
 *
 * @RouteResource("key-words")
 */
class KeyWordController extends RestController implements ClassResourceInterface, SecuredControllerInterface
{
    protected static $entityKey = 'key-words';

    /**
     * Returns field-descriptors for key-words
     *
     * @param int $categoryId
     *
     * @return Response
     */
    public function fieldsAction($categoryId)
    {
        return $this->handleView($this->view(array_values($this->getFieldDescriptors())));
    }

    public function cgetAction($categoryId, Request $request)
    {
        /** @var RestHelperInterface $restHelper */
        $restHelper = $this->get('sulu_core.doctrine_rest_helper');

        /** @var DoctrineListBuilderFactory $factory */
        $factory = $this->get('sulu_core.doctrine_list_builder_factory');

        $fieldDescriptor = $this->getFieldDescriptors();

        $listBuilder = $factory->create($this->container->getParameter('sulu_category.entity.keyword'));
        $restHelper->initializeListBuilder($listBuilder, $fieldDescriptor);

        $listBuilder->where($fieldDescriptor['locale'], $request->get('locale'));
        // TODO $listBuilder->where($fieldDescriptor['category'], $categoryId);

        $listResponse = $listBuilder->execute();

        $list = new ListRepresentation(
            $listResponse,
            self::$entityKey,
            'cget_category_key-words',
            array_merge(['categoryId' => $categoryId], $request->query->all()),
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->handleView($this->view($list, 200));
    }

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
     * Delete given key-word from given category.
     *
     * @param int $categoryId
     * @param Request $request
     *
     * @return Response
     */
    public function cdeleteAction($categoryId, Request $request)
    {
        $category = $this->getCategoryManager()->findById($categoryId);

        $ids = array_filter(explode(',', $request->get('ids')));
        foreach ($ids as $id) {
            $keyWord = $this->getKeyWordRepository()->findById($id);
            $this->getKeyWordManager()->delete($keyWord, $category);
        }

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
     * Returns field descriptor for key-word.
     *
     * @return FieldDescriptorInterface[]
     */
    public function getFieldDescriptors()
    {
        return $this->get('sulu_core.list_builder.field_descriptor_factory')->getFieldDescriptorForClass(
            KeyWord::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityContext()
    {
        return 'sulu.settings.categories';
    }
}
