<?php

namespace Services;

use App\Platforms\Providers\AndroidPlatform;
use App\Services\PlatformsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Tests\TestCase;

class PlatformsServiceTest extends TestCase
{
    public $providers ;
    public function setUp(): void
    {
        parent::setUp();
        $this->providers = PlatformsService::listProviders() ;
    }

    public function testValidProvider()
    {
        $this->assertTrue(PlatformsService::isValidProvider(AndroidPlatform::class));
    }

    public function testInvalidProvider()
    {
        $this->assertFalse(PlatformsService::isValidProvider('Simple Text'));
    }

    public function testListProviders()
    {
        $this->assertArrayHasKey(1 , PlatformsService::listProviders());
    }

    public function testInsert()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        $this->assertDatabaseHas('platforms', [
            'id' => $inserted->id,
            'provider' => $this->providers[0],
            'name' => 'test 1',
        ]);
    }

    public function testInsertInvalidProvider()
    {
        $this->expectException(InvalidArgumentException::class);
        PlatformsService::insert('test 1' , 'Simple Text');
    }

    public function testUpdate()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        PlatformsService::update($inserted , 'test 2' , $this->providers[0]);
        $this->assertDatabaseHas('platforms', [
            'id' => $inserted->id,
            'provider' => $this->providers[0],
            'name' => 'test 2',
        ]);
        $this->expectException(InvalidArgumentException::class);
        PlatformsService::update($inserted , 'test 1' , 'Simple Text');
    }

    public function testFindById()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        $checked = PlatformsService::findById($inserted->id);
        $this->assertEquals($checked->id , $inserted->id);
    }

    public function testFindWrongId()
    {
        $this->expectException(ModelNotFoundException::class);
        PlatformsService::findById(0);
    }

    public function testDelete()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        self::assertTrue(PlatformsService::deletePlatform($inserted));
    }

    public function testDeleteAfterOneTimeDelete()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        PlatformsService::deletePlatform($inserted);
        self::assertFalse(PlatformsService::deletePlatform($inserted));
    }

    public function testDeleteMultiply()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        $inserted2 = PlatformsService::insert('test 2' , $this->providers[0]);
        self::assertTrue(PlatformsService::deleteMultiply([$inserted->id , (string) $inserted2->id]));
        $this->expectException(ModelNotFoundException::class);
        PlatformsService::findById($inserted->id);
        PlatformsService::findById($inserted2->id);
    }

    public function testDeleteMultiplyWhenDeleteBefore()
    {
        $inserted = PlatformsService::insert('test 1' , $this->providers[0]);
        $inserted2 = PlatformsService::insert('test 2' , $this->providers[0]);
        PlatformsService::deleteMultiply([$inserted->id , (string) $inserted2->id]);
        $this->expectException(ModelNotFoundException::class);
        PlatformsService::deleteMultiply([$inserted->id , (string) $inserted2->id]);
    }
}
