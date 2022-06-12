<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function getCollection()
    {
        $response = static::createClient()->request('GET', '/products');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld-json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@id' => '/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/products?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/products?page=1',
                'hydra:last' => '/products?page=4',
                'hydra:next' => '/products?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }

    public function testCreateProduct()
    {
        $response = static::createClient()->request('POST', '/products', ['json' => [
            'name' => 'pirelli',
            'vendorCode' => 'p-1234567',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@type' => 'Product',
            'name' => 'pirelli',
            'vendorCode' => 'p-1234567',
        ]);
    }

    public function testCreateInvalidProduct()
    {
        $response = static::createClient()->request('POST', '/products', ['json' => []]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => "vendorCode: This value should not be blank.\nname: This value should not be blank.",
        ]);
    }

    public function testUpdateProduct()
    {
        $iri = $this->findIriBy(Product::class, ['vendorCode' => 'a-5794390']);

        static::createClient()->request('PUT', $iri, ['json' => [
            'name' => 'updated name',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'vendorCode' => 'a-5794390',
            'name' => 'updated name',
        ]);
    }

    public function testDeleteProduct()
    {
        $iri = $this->findIriBy(Product::class, ['vendorCode' => 'a-5794390']);
        static::createClient()->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()
                ->get('doctrine')
                ->getRepository(Product::class)
                ->findOneBy(['vendorCode' => 'a-5794390'])
        );
    }

    public function testSearch()
    {
        $response = static::createClient()->request('GET', '/products?name=w&vendorCode=w');

        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@id' => '/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/products?name=w&vendorCode=w',
                '@type' => 'hydra:PartialCollectionView',
            ],
        ]);
        $items = $response->toArray()['hydra:member'];
        $this->assertCount(5, $items);
        foreach ($items as $item) {
            $this->assertTrue(strpos($item['name'], 'w') !== false || strpos($item['vendorCode'], 'w') !== false);
        }
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }
}
