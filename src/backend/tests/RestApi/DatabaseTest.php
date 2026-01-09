<?php

namespace App\Tests\RestApi;

use App\DataFixtures\UserFixture;
use App\Entity\Workspace\Workspace;
use App\Tests\AbstractAppTestCase;

class DatabaseTest extends AbstractAppTestCase
{
    public function testGetCollection(): void
    {
        $userFixture1 = UserFixture::$users[0];
        $userFixture2 = UserFixture::$users[1];

        $token = $this->getToken($userFixture1['email'], $userFixture1['password']);

        $databases = $this->getClient()->request(
            'GET',
            '/api/databases',
            [
                'headers' => $this->getHeaders($token)
            ]
        )->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($databases);

        // Verify user1 get only his workspace entities
        foreach ($databases as $database) {
            $databaseWorkspace = $this->getResourceByIri($database['workspace']);
            $this->assertEquals($databaseWorkspace->getName(), $userFixture1['workspace']);
        }

        // Verify user2 doesn't have access to server of user1
        $wrongUuid = $databases[0]['uid'];
        $token2 = $this->getToken($userFixture2['email'], $userFixture2['password']);

        $this->getClient()->request(
            'GET',
            sprintf("/api/databases/%s", $wrongUuid),
            [
                'headers' => $this->getHeaders($token2)
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateDatabase(): void
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
            '/api/databases',
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
