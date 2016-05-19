<?php

/**
 * @coversDefaultClass \Bican\Roles\Traits\HasRoleAndPermission
 */
class HasRoleAndPermissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     *
     * @return PHPUnit_Framework_MockObject_MockObject|\Bican\Roles\Traits\HasRoleAndPermission
     */
    private function getMockWithMethods(array $methods)
    {
        return $this->getMockForTrait(\Bican\Roles\Traits\HasRoleAndPermission::class,
            [], 'User', true, true, true, $methods /*mocked methods*/);
    }

    /**
     * @covers ::roles
     */
    public function test_has_roles_relation()
    {
        $mock = $this->getMockWithMethods([
            'belongsToMany',
            'withTimestamps',
            'getRoleClass',
        ]);

        $mock->expects($this->once())
             ->method('belongsToMany')
             ->will($this->returnSelf());

        $mock->expects($this->once())
             ->method('getRoleClass')
             ->will($this->returnValue('Roles'));

        $mock->expects($this->once())
             ->method('withTimestamps')
             ->will($this->returnSelf());

        $mock->roles();
    }

    /**
     * @covers ::getRoles
     */
    public function test_get_roles_returns_existing_collection()
    {
        $mock = $this->getMockWithMethods([]);
        $mock->setRoles('Collection');

        $this->assertEquals($mock->getRoles(), 'Collection');
    }

    /**
     * @covers ::getRoles
     */
    public function test_get_roles_returns_new_collection_when_empty()
    {
        $mock = $this->getMockWithMethods([
            'roles',
            'get',
        ]);

        $mock->expects($this->once())
             ->method('roles')
             ->will($this->returnSelf());

        $mock->expects($this->once())
             ->method('get')
             ->will($this->returnValue('Collection'));

        $this->assertEquals($mock->getRoles(), 'Collection');
    }
}