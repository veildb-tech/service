import PropTypes from 'prop-types';
import { useQuery } from '@apollo/client';
import { WORKSPACE_USER_INVITATIONS } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { useEffect, useState } from 'react';
import { Typography } from '@mui/material';
import * as React from 'react';
import { WorkspaceUserAdd } from './add';
import { InvitationLink } from './invitation-link';

export function UserInvitations(props) {
  const [itemsPerPage, setItemsPerPage] = useState(5);
  const [page, setPage] = useState(0);
  const [reloadTime, setReloadTime] = useState(null);

  const {
    data, error, loading, refetch
  } = useQuery(WORKSPACE_USER_INVITATIONS, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
  });

  useEffect(() => {
    refetch();
  }, [reloadTime]);

  const userInvitations = data && data.userInvitations ? data.userInvitations.collection : [];
  const totalCount = data && data.userInvitations ? data.userInvitations.paginationInfo.totalCount : 0;
  const handleRowsPerPageChange = (event) => {
    setItemsPerPage(event.target.value);
    setPage(0);
  };

  const handlePageChange = (event, page) => {
    setPage(page);
  };

  const { currentWorkspaceData } = props;
  const groups = currentWorkspaceData.workspace_groups.collection;

  const columns = [
    {
      code: 'email',
      title: 'Email',
    },
    {
      code: 'invitationGroups',
      title: 'Groups',
      // eslint-disable-next-line react/no-unstable-nested-components
      getValue: (item) => {
        const itemGroups = [];

        // eslint-disable-next-line array-callback-return
        item.invitationGroups.map((invitationGroup) => {
          const invGroup = groups.find((group) => group.id === invitationGroup);

          if (invGroup) {
            itemGroups.push(invGroup.name);
          }
        });

        return (
          <div className="chip-items-0">
            {itemGroups.map((groupName) => (
              <div
                key={groupName}
                className="chip-item-0"
              >
                {groupName}
              </div>
            ))}
          </div>
        );
      },
    },
    {
      code: 'status',
      title: 'Status',
      type: 'status'
    },
    {
      code: 'createdAt',
      title: 'Created At',
      type: 'date',
    },
  ];

  const getActions = (invitation) => (
    <InvitationLink invitation={invitation} />
  );

  return (
    <div className={'mb-8'}>
      <div className="flex justify-between gap-2 items-center mb-7">
        <Typography variant="h4">Invited users</Typography>

        <WorkspaceUserAdd
          groups={groups}
          reload={setReloadTime}
        />
      </div>

      <DataGrid
        columns={columns}
        rows={userInvitations}
        getActions={getActions}
        onRowsPerPageChange={handleRowsPerPageChange}
        onPageChange={handlePageChange}
        page={page}
        itemsPerPage={itemsPerPage}
        totalCount={totalCount}
        error={error}
        loading={loading}
        customStatusMap={{
          expired: 'error',
          canceled: 'error'
        }}
      />
    </div>
  );
}

UserInvitations.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  currentWorkspaceData: PropTypes.object,
};
