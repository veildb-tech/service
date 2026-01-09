<?php

namespace App\Tests\RestApi;

use App\DataFixtures\UserFixture;
use App\Entity\Workspace\Workspace;
use App\Tests\AbstractAppTestCase;

class ServerTest extends AbstractAppTestCase
{

    public function testGetCollection(): void
    {
        $userFixture1 = UserFixture::$users[0];
        $userFixture2 = UserFixture::$users[1];

        $token = $this->getToken($userFixture1['email'], $userFixture1['password']);

        $servers = $this->getClient()->request(
            'GET',
            '/api/servers',
            [
                'headers' => $this->getHeaders($token)
            ]
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($servers);

        // Verify user1 get only his workspace entities
        foreach ($servers as $server) {
            $serverWorkspace = $this->getResourceByIri($server['workspace']);
            $this->assertEquals($serverWorkspace->getName(), $userFixture1['workspace']);
        }

        // Verify user2 doesn't have access to server of user1
        $wrongUuid = $servers[0]['uuid'];
        $token2 = $this->getToken($userFixture2['email'], $userFixture2['password']);
        $this->getClient()->request(
            'GET',
            sprintf("/api/servers/%s", $wrongUuid),
            [
                'headers' => $this->getHeaders($token2)
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateServer(): void
    {
        // Verify server creates successfully
        $userFixture1 = UserFixture::$users[0];
        $token = $this->getToken($userFixture1['email'], $userFixture1['password']);
        $user = $this->getClient()->request(
            'GET',
            '/api/users',
            [
                'headers' => $this->getHeaders($token),
                'query' => [
                    'email' => $userFixture1['email']
                ]
            ]
        )->toArray()[0];

        $workspaceIri = $this->findIriBy(Workspace::class, ['code' => $user['workspaces'][0]['code']]);
        $this->getClient()->request(
            'POST',
            '/api/servers',
            [
                'headers' => $this->getHeaders($token),
                'json' => [
                    'name' => "Test",
                    'workspace' => $workspaceIri,
                    'status' => 'enabled'
                ]
            ]
        )->toArray();

        $this->assertJsonContains([
            'name' => "Test",
            'workspace' => $workspaceIri,
            'status' => 'enabled'
        ]);
    }
}
