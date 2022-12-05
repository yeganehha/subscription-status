<?php

namespace Services;

use App\Enums\StatusEnum;
use App\Services\AppsService;
use App\Services\PlatformsService;
use InvalidArgumentException;
use Tests\TestCase;

class AppsServiceTest extends TestCase
{
    public function testInsert()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $this->assertDatabaseHas('apps', [
            'id' => $inserted->id,
            'platform_id' => $platforms[0]->id,
            'uid' => 'test unique id',
        ]);
        $inserted = AppsService::insert('test 2 unique id' ,'test 2', $platforms[0]);
        $this->assertDatabaseHas('apps', [
            'id' => $inserted->id,
            'platform_id' => $platforms[0]->id,
            'name' => 'test 2',
            'uid' => 'test 2 unique id',
        ]);
        $this->expectException(InvalidArgumentException::class);
        AppsService::insert('test 2 unique id' ,'test 2', $platforms[0]);
        AppsService::insert('test 3 unique id' ,'test 2', 0);
    }

    public function testIsUidExist()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $this->assertTrue(AppsService::isUidExist($inserted->uid));
        $this->assertFalse(AppsService::isUidExist($inserted->uid ,  $inserted->id));
        $this->assertFalse(AppsService::isUidExist($inserted->uid ,  $inserted));
        $this->assertFalse(AppsService::isUidExist($inserted->uid .' Slat'));
    }

    public function testDeleteMultiply()
    {

        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $inserted2 = AppsService::insert('test 2 unique id' ,null, $platforms[0]->id);
        self::assertTrue(AppsService::deleteMultiply([$inserted->id , (string) $inserted2->id]));
        $this->assertNull(AppsService::getAppByID($inserted->id));
        $this->assertNull(AppsService::getAppByID($inserted2->id));
        $this->expectException(InvalidArgumentException::class);
        AppsService::deleteMultiply([$inserted->id , (string) $inserted2->id]);
    }

    public function testGetAppByUid()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $find = AppsService::getAppByUID($inserted->uid);
        $this->assertEquals($find->uid ,$inserted->uid );
        $this->assertNull(AppsService::getAppByUID(""));
    }

    public function testStatus()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $updated = AppsService::status($inserted->id , StatusEnum::Active);
        $this->assertEquals($updated->status , StatusEnum::Active);
        $updated = AppsService::status($inserted , StatusEnum::Pending);
        $this->assertEquals($updated->status , StatusEnum::Pending);
        $updated = AppsService::status($inserted , 'active');
        $this->assertEquals($updated->status , StatusEnum::Active);
        $updated = AppsService::status($inserted , 'Pending');
        $this->assertEquals($updated->status , StatusEnum::Pending);
        $updated = AppsService::status($inserted , 'actIve');
        $this->assertEquals($updated->status , StatusEnum::Active);
        $this->expectException(InvalidArgumentException::class);
        AppsService::status($inserted , 'reject');
    }

    public function testGetAppById()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $find = AppsService::getAppByID($inserted->id);
        $this->assertEquals($find->id ,$inserted->id );
        $this->assertNull(AppsService::getAppByID(0));
    }

    public function testUpdate()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        $inserted2 = AppsService::insert('test 2 unique id' ,null, $platforms[0]->id);
        AppsService::update($inserted , 'test unique id' ,'test 2' , $platforms[0]);
        $this->assertDatabaseHas('apps', [
            'id' => $inserted->id,
            'platform_id' => $platforms[0]->id,
            'uid' => 'test unique id',
            'name' => 'test 2'
        ]);

        AppsService::update($inserted->id , 'test unique id' ,'test 2' , $platforms[0]->id);
        $this->expectException(InvalidArgumentException::class);
        AppsService::update($inserted , 'test 2 unique id' ,'test 2' , $platforms[0]->id);
        AppsService::update($inserted ,'test 2 unique id' ,'test 2', $platforms[0]);
        AppsService::update($inserted ,'test 3 unique id' ,'test 2', 0);
        AppsService::update(0 ,'test 4 unique id' ,'test 2', $platforms[0]);
        $this->assertDatabaseHas('apps', [
            'id' => $inserted->id,
            'platform_id' => $platforms[0]->id,
            'uid' => 'test unique id',
            'name' => 'test 2'
        ]);
    }

    public function testDeleteApp()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $inserted = AppsService::insert('test unique id' ,null, $platforms[0]->id);
        self::assertTrue(AppsService::deleteApp($inserted));
        self::assertFalse(AppsService::deleteApp($inserted));
    }
}
