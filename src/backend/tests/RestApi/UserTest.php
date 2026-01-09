<?php

namespace App\Tests\RestApi;

use App\DataFixtures\UserFixture;
use App\Tests\AbstractAppTestCase;

class UserTest extends AbstractAppTestCase
{

    public function testGetCollection(): void
    {
        $userFixture = UserFixture::$users[0];
        $token = $this->getToken($userFixture['email'], $userFixture['password']);

        $users = $this->getClient()->request(
            'GET',
            '/api/users',
            [
                'headers' => $this->getHeaders($token)
            ]
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($users);
        foreach ($users as $user) {
            foreach ($user['workspaces'] as $workspace) {
                $this->assertEquals($workspace['name'], $userFixture['workspace']);
            }
        }
    }

    public function testGetUserFromAnotherWorkspace(): void
    {
        $userFixture1 = UserFixture::$users[0];
        $userFixture2 = UserFixture::$users[1];

        $token = $this->getToken($userFixture1['email'], $userFixture1['password']);

        $users = $this->getClient()->request(
            'GET',
            '/api/users',
            [
                'headers' => $this->getHeaders($token),
                'query' => [
                    'email' => $userFixture2['email']
                ]
            ]
        )->toArray();

        $this->assertEmpty($users);
    }
}
