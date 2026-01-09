import { useAuth } from './use-auth';

/**
 * TODO: All this matrix has to me moved to backend side
 */

/**
 * This is replication of enum App\Enums\Workspace\UserGroupPermissionEnum
 * @type {{owner: number, read: number, edit: number, read_all: number, admin: number, edit_all: number}}
 */
const userGroupPermissions = {
  owner: 1,
  admin: 2,
  read: 3,
  edit: 4,
  read_all: 5,
  edit_all: 6,
};

const permissionPriority = [
  userGroupPermissions.owner,
  userGroupPermissions.admin,
  userGroupPermissions.edit_all,
  userGroupPermissions.edit,
  userGroupPermissions.read_all,
  userGroupPermissions.read,
];

const entityPermissions = {
  dbm_edit: [
    userGroupPermissions.owner,
    userGroupPermissions.admin,
    userGroupPermissions.edit,
    userGroupPermissions.edit_all,
  ],
  dbm_read: [
    userGroupPermissions.owner,
    userGroupPermissions.admin,
    userGroupPermissions.edit,
    userGroupPermissions.edit_all,
    userGroupPermissions.read,
    userGroupPermissions.read_all,
  ],
  dbm_admin: [userGroupPermissions.owner, userGroupPermissions.admin],
  dbm_owner: [userGroupPermissions.owner],
};

const entityPermissionsMatrix = {
  database: {
    view: entityPermissions.dbm_read,
    edit: entityPermissions.dbm_edit,
    delete: entityPermissions.dbm_edit,
  },
  server: {
    view: entityPermissions.dbm_admin,
    edit: entityPermissions.dbm_admin,
    delete: entityPermissions.dbm_admin,
  },
  databaseRule: {
    view: entityPermissions.dbm_edit,
    edit: entityPermissions.dbm_edit,
    delete: entityPermissions.dbm_edit,
  },
  workspace: {
    edit: entityPermissions.dbm_owner,
  },
  contact: {
    view: entityPermissions.dbm_admin
  }
};

export const usePermission = () => {
  const auth = useAuth();

  if (!auth.user) return;

  let userPermission = null;
  // eslint-disable-next-line array-callback-return
  auth.user.groups.collection.map((group) => {
    if (userPermission) {
      const currentPriority = permissionPriority.find((perm) => perm === userPermission);
      const newPermissionPriority = permissionPriority.find((perm) => perm === group.permission);

      if (newPermissionPriority < currentPriority) {
        userPermission = group.permission;
      }
    } else {
      userPermission = group.permission;
    }
  });

  /**
   * @param entityAction string. Expecting string like 'database.edit' where first part is entity, second one - action
   */
  const canSee = (entityAction) => {
    const [entity, action] = entityAction.split('.');
    return entityPermissionsMatrix[entity][action].includes(userPermission);
  };

  const isAdmin = () => userPermission === userGroupPermissions.owner
      || userPermission === userGroupPermissions.admin;

  return {
    isAdmin,
    canSee,
  };
};
