import React from 'react';
import PropTypes from 'prop-types';
import { UserList } from './users/list';
import { UserInvitations } from './users/invitations';

export function WorkspaceUsers(props) {
  const { data: currentWorkspaceData } = props;

  return (
    <div className="flex flex-col w-full gap-4 ">
      <UserInvitations currentWorkspaceData={currentWorkspaceData} />
      <UserList currentWorkspaceData={currentWorkspaceData} />
    </div>
  );
}

WorkspaceUsers.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
};
