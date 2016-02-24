<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Functional\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\CategoryMeta;
use Sulu\Bundle\CategoryBundle\Entity\CategoryTranslation;
use Sulu\Bundle\CategoryBundle\Entity\KeyWord;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;

class KeyWordControllerTest extends SuluTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Category
     */
    private $category1;

    /**
     * @var Category
     */
    private $category2;

    public function setUp()
    {
        $this->entityManager = $this->db('ORM')->getOm();

        $this->initOrm();
    }

    public function initOrm()
    {
        $this->db('ORM')->purgeDatabase();

        $this->category1 = new Category();
        $this->category1->setKey('1');
        $this->category1->setDefaultLocale('de');
        $categoryTranslation1 = new CategoryTranslation();
        $categoryTranslation1->setCategory($this->category1);
        $categoryTranslation1->setTranslation('test-1');
        $categoryTranslation1->setLocale('de');
        $this->category1->addTranslation($categoryTranslation1);

        $this->category2 = new Category();
        $this->category2->setKey('2');
        $this->category2->setDefaultLocale('de');
        $categoryTranslation2 = new CategoryTranslation();
        $categoryTranslation2->setCategory($this->category2);
        $categoryTranslation2->setTranslation('test-2');
        $categoryTranslation2->setLocale('de');
        $this->category2->addTranslation($categoryTranslation2);

        $this->entityManager->persist($this->category1);
        $this->entityManager->persist($this->category2);
        $this->entityManager->persist($categoryTranslation1);
        $this->entityManager->persist($categoryTranslation2);
        $this->entityManager->flush();
    }

    public function testPost($keyword = 'Test', $locale = 'de')
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/categories/' . $this->category1->getId() . '/key-words',
            ['locale' => $locale, 'keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertNotNull($result['id']);

        return $result;
    }

    public function testPostExisting($keyword = 'Test', $locale = 'de')
    {
        $first = $this->testPost($keyword, $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/categories/' . $this->category1->getId() . '/key-words',
            ['locale' => $locale, 'keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);
    }

    public function testPostExistingOtherCategory($keyword = 'Test', $locale = 'de')
    {
        $first = $this->testPost($keyword, $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/categories/' . $this->category2->getId() . '/key-words',
            ['locale' => $locale, 'keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);

        return $result;
    }

    public function testPostExistingOtherKeyword($keyword = 'Test-1', $locale = 'de')
    {
        $first = $this->testPost('Test', $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/categories/' . $this->category2->getId() . '/key-words',
            ['locale' => $locale, 'keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertNotEquals($first['id'], $result['id']);
        $this->assertNotNull($result['id']);
    }

    public function testPut($keyword = 'Test-1', $locale = 'de')
    {
        $first = $this->testPost('Test', $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id'],
            ['keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);
    }

    public function testPutForce($keyword = 'Test-1', $locale = 'de')
    {
        $first = $this->testPost('Test', $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id'] . '?force=true',
            ['keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);
    }

    public function testPutMultipleCategories($keyword = 'Test-1', $locale = 'de')
    {
        $first = $this->testPostExistingOtherCategory('Test', $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id'],
            ['keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(409, $client->getResponse()->getStatusCode());

        $this->assertEquals('Test', $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);
    }

    public function testPutMultipleCategoriesForce($keyword = 'Test-1', $locale = 'de')
    {
        $first = $this->testPostExistingOtherCategory('Test', $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id'] . '?force=true',
            ['keyWord' => $keyword]
        );

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($keyword, $result['keyWord']);
        $this->assertEquals($locale, $result['locale']);
        $this->assertEquals($first['id'], $result['id']);
    }

    public function testDelete($keyword = 'Test', $locale = 'de')
    {
        $first = $this->testPost($keyword, $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'DELETE',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id']
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertNull($this->entityManager->find(KeyWord::class, $first['id']));
    }

    public function testDeleteMultipleCategories($keyword = 'Test', $locale = 'de')
    {
        $first = $this->testPostExistingOtherCategory($keyword, $locale);

        $client = $this->createAuthenticatedClient();
        $client->request(
            'DELETE',
            '/api/categories/' . $this->category1->getId() . '/key-words/' . $first['id']
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertNotNull($this->entityManager->find(KeyWord::class, $first['id']));
    }
}
