# Groups and permission management

## Permission Levels

**There are 6 permission levels**:


1. Owner - can do everything.
2. Admin - can manage workspace, servers and databases.
3. Edit all - can manage databases and create new
4. Edit - can manage specific databases and create new
5. Read all - can view all databases
6. Read - can view specific databases.

## Understanding of permissions at the server level

### 1. Workspace Filtering

First of all entities should be filtered by workspace - every API request (doesn't matter if it is GraphQL or REST) it check if user/token has access to this workspace entity. There are 2 conditions:


1. Send selected workspace code in the query (see `App\Service\Workspace\GetSelectedWorkspace::getSelectedWorkspace()` method). As you can see from this method there are several ways to send workspace: cookie, request, or even in the post request.
2. If workspace is not sent it will retrieve all users/token workspace and filter entities by workspaces assigned to user/token (see `App\Api\FilterByWorkspaceExtension`)

Also, to enabled entity filtering by workspace need to specify such entities in `config/service.yml` file:

```yaml
    App\Api\FilterByWorkspaceExtension:
        arguments:
            $entityToFilter:
                - 'App\Entity\Database\Database'
                - 'App\Entity\Database\DatabaseDump'
                - 'App\Entity\Server'
                - 'App\Entity\Workspace\Notification'
            $subEntityToFilter:
                - 'App\Entity\Database\DatabaseRule'
                - 'App\Entity\Database\DatabaseDump'
```


Where **entityToFilter** - entity which has directly workspace property (for example Database has workspace_id in the databases table). **subEntityToFilter** - entities which have access through `Database` propery (e.g. entity has database property. In that case it will filter by the workspace from `database` property).


:::warning
**Important!** While generating user for server it set ApiWorkspaceCode to this "temporary" user. So in cases above it checks first for `$user→getApiWorkspaceCode()` and after that try to retrieve workspaces from user.

:::


### 2. User Groups / Permissions

All users (except service admin) filters by workspace. There are should be way to get any data for user from another workspace!

According to permissions level there are several types of permissions. To specify permission level need to add security attribute to ApiResources:

```yaml
#[ApiResource(
    ...
    security: "is_granted('dbm_edit', object)",
)]
```

Where `dbm_edit` - minimum required level to get access to this resource. According to `App\Enums\Workspace\UserRoleEnum` currently there are 4 levels of access:

```php
    case DBM_EDIT = 'dbm_edit';
    case DBM_READ = 'dbm_read';
    case DBM_ADMIN = 'dbm_admin';
    case DBM_OWNER = 'dbm_owner';
```

Entry point for this verification is default Symfony functionality - voter (see `App\Security\Voter\GeneralVoter`). This voter will work if entity has specified **is_granted** in the security property.


### 3. Database access

Third level is database validation. User could have access to different groups and levels. Each group could have access (read or edit) to specified databases. User could be added to this group and will have access according to group configurations.

Access to databases validates in 2 points:


1. First it filters in the `App\Api\ValidateUserPermission` extension. So it get user's workspaces and filters databases according to workspaces and permissions.
2. Second gate is voter `App\Security\Voter\GeneralVoter` . In this voter there is double check of user permissions. If user doesn't have permission to some entity it will *throws* AccessDeniedException (even if user has permission to another entities).


:::info
This validation skips for user from **owner** and **admin** groups

:::


:::info
Also this validation skips for API Users which has ApiWorkspaceCode propery. **Such tokens have permissions same as admin user.**

:::


## Debug permissions

According to information above there several point which should be debugged first:

* `App\Api\ValidateUserPermission `
* `App\Api\FilterByWorkspaceExtension`
* `App\Security\ValidatePermissions `
* `App\Security\Voter\GeneralVoter`
* `App\Service\Workspace\GetSelectedWorkspace`