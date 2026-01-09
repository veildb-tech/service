import React, { useEffect, useState } from 'react';
import {
  Typography,
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';
import { useQuery } from '@apollo/client';
import { WORKSPACE_GROUPS_QUERY } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { DeleteGroup } from './groups/delete-group';
import { WorkspaceGroupDetail } from './groups/detail';

export function WorkspaceGroups() {
  const { workspaceGroupRoles } = useConfig();
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [page, setPage] = useState(0);

  const {
    loading, error, data, refetch
  } = useQuery(WORKSPACE_GROUPS_QUERY);
  const [reloadTime, setReloadTime] = useState(null);
  useEffect(() => {
    refetch();
  }, [reloadTime]);

  const groups = data && data.groups ? data.groups.collection : [];
  const totalCount = data && data.groups ? data.groups.paginationInfo.totalCount : 0;
  const handleRowsPerPageChange = (event) => {
    setItemsPerPage(event.target.value);
    setPage(0);
  };

  const handlePageChange = (event, page) => {
    setPage(page);
  };

  const columns = [
    {
      code: 'name',
      title: 'Group Name',
    },
    {
      code: 'permission',
      title: 'Permissions',
      type: 'status',
      getValue: (item) => workspaceGroupRoles.find((role) => role.value === item.permission).label,
    },
  ];

  const permissionStatusObj = {};
  // eslint-disable-next-line no-return-assign
  workspaceGroupRoles.map((role) => permissionStatusObj[role.label] = 'pendingDark');

  const getActions = (group) => (
    <div className="flex items-center flex-wrap justify-between gap-4">
      <WorkspaceGroupDetail
        data={group}
        reload={setReloadTime}
      />
      <DeleteGroup
        uuid={group.id}
        reload={setReloadTime}
      />
    </div>
  );

  return (
    <div className="w-full">
      <div className="flex flex-wrap items-center justify-between gap-4 mb-7">
        <Typography variant="h4">Workspace groups</Typography>

        <WorkspaceGroupDetail reload={setReloadTime} />
      </div>

      <DataGrid
        columns={columns}
        rows={groups}
        getActions={getActions}
        onRowsPerPageChange={handleRowsPerPageChange}
        onPageChange={handlePageChange}
        page={page}
        itemsPerPage={itemsPerPage}
        totalCount={totalCount}
        error={error}
        loading={loading}
        customStatusMap={permissionStatusObj}
      />
    </div>
  );
}
