import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { useQuery } from '@apollo/client';
import { WORKSPACE_USERS } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { Typography } from '@mui/material';
import { EditUser } from './edit-user';
import { DeleteUser } from './delete-user';

export function UserList(props) {
  const [itemsPerPage, setItemsPerPage] = useState(5);
  const [page, setPage] = useState(0);
  const [reloadTime, setReloadTime] = useState(null);

  const {
    data, error, loading, refetch
  } = useQuery(WORKSPACE_USERS, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
  });

  useEffect(() => {
    refetch();
  }, [reloadTime]);

  const users = data && data.users ? data.users.collection : [];
  const totalCount = data && data.users ? data.users.paginationInfo.totalCount : 0;
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
      code: 'firstname',
      title: 'Firstname',
    },
    {
      code: 'lastname',
      title: 'Lastname',
    },
    {
      code: 'email',
      title: 'Email',
    },
  ];

  const getActions = (user) => (
    <div className="flex justify-between gap-2">
      <EditUser
        user={user}
        groups={groups}
      />
      <DeleteUser
        uuid={user.id}
        reload={setReloadTime}
      />
    </div>
  );

  return (
    <div>
      <div className="mb-7">
        <Typography variant="h4">Workspace users</Typography>
      </div>

      <DataGrid
        columns={columns}
        rows={users}
        getActions={getActions}
        onRowsPerPageChange={handleRowsPerPageChange}
        onPageChange={handlePageChange}
        page={page}
        itemsPerPage={itemsPerPage}
        totalCount={totalCount}
        error={error}
        loading={loading}
      />

    </div>
  );
}

UserList.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  currentWorkspaceData: PropTypes.object,
};
