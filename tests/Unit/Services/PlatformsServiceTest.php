<?php

namespace Services;

use App\Platforms\Providers\AndroidPlatform;
use App\Services\PlatformsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Tests\TestCase;

class PlatformsServiceTest extends TestCase
{
    public function testIsValidProvider()
    {
        $this->assertTrue(PlatformsService::isValidProvider(AndroidPlatform::class));
        $this->assertFalse(PlatformsService::isValidProvider('Simple Text'));
    }

    public function testListProviders()
    {
        $this->assertArrayHasKey(1 , PlatformsService::listProviders());
    }

    public function testInsert()
    {
        $providers = PlatformsService::listProviders() ;
        $inserted = PlatformsService::insert('test 1' , $providers[0]);
        $this->assertDatabaseHas('platforms', [
            'id' => $inserted->id,
            'provider' => $providers[0],
            'name' => 'test 1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        PlatformsService::insert('test 1' , 'Simple Text');
    }

    public function testUpdate()
    {
        $providers = PlatformsService::listProviders() ;
        $inserted = PlatformsService::insert('test 1' , $providers[0]);
        PlatformsService::update($inserted , 'test 2' , $providers[0]);
        $this->assertDatabaseHas('platforms', [
            'id' => $inserted->id,
            'provider' => $providers[0],
            'name' => 'test 2',
        ]);
        $this->expectException(InvalidArgumentException::class);
        PlatformsService::update($inserted , 'test 1' , 'Simple Text');
    }

    public function testFindById()
    {
        $providers = PlatformsService::listProviders() ;
        $inserted = PlatformsService::insert('test 1' , $providers[0]);
        $checked = PlatformsService::findById($inserted->id);
        $this->assertEquals($checked->id , $inserted->id);
        $this->expectException(ModelNotFoundException::class);
        PlatformsService::findById(0);
    }

    public function testDelete()
    {
        $providers = PlatformsService::listProviders() ;
        $inserted = PlatformsService::insert('test 1' , $providers[0]);
        self::assertTrue(PlatformsService::deletePlatform($inserted));
        self::assertFalse(PlatformsService::deletePlatform($inserted));
    }

    public function testDeleteMultiply()
    {
        $providers = PlatformsService::listProviders() ;
        $inserted = PlatformsService::insert('test 1' , $providers[0]);
        $inserted2 = PlatformsService::insert('test 2' , $providers[0]);
        self::assertTrue(PlatformsService::deleteMultiply([$inserted->id , (string) $inserted2->id]));
        $this->expectException(ModelNotFoundException::class);
        PlatformsService::findById($inserted->id);
        PlatformsService::findById($inserted2->id);
        PlatformsService::deleteMultiply([$inserted->id , (string) $inserted2->id]);
    }
}
