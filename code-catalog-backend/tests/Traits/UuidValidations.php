<?php

namespace Tests\Traits;

use Ramsey\Uuid\Uuid;

trait UuidValidations
{   
    public function assertStringIsUuid(string $text) {
        $this->assertTrue(Uuid::isValid($text));
    }
    
    public function assertUuidIsV4(Uuid $uuid) {
        $this->assertEquals(
            $uuid->getVersion(), 
            Uuid::UUID_TYPE_RANDOM
        );
    }

    public function assertIdIsUuid4(string $id) {        
        $this->assertStringIsUuid($id);
        $this->assertUuidIsV4(Uuid::fromString($id));
    }
}