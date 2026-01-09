import React, { useState, useEffect } from 'react';
import {
  Typography,
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';
import { DataGrid } from 'src/components/grid/data-grid';
import { CURRENT_WORKSPACE_WEBHOOKS } from 'src/queries';
import { useQuery } from '@apollo/client';
import NextLink from 'next/link';
import { InfoIcon } from 'src/elements/icons';
import { WebhookDetail } from './webhooks/detail';
import { DeleteWebhook } from './webhooks/delete';

export function WorkspaceWebhooks() {
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [page, setPage] = useState(0);
  const [reloadTime, setReloadTime] = useState(null);

  const { webhookOperations } = useConfig();

  const {
    data, error, loading, refetch
  } = useQuery(CURRENT_WORKSPACE_WEBHOOKS, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
  });

  useEffect(() => {
    refetch().then(() => {});
  }, [reloadTime, refetch]);

  const webhooks = data && data.webhooks ? data.webhooks.collection : [];
  const totalCount = data && data.webhooks ? data.webhooks.paginationInfo.totalCount : 0;
  const handleRowsPerPageChange = (event) => {
    setItemsPerPage(event.target.value);
    setPage(0);
  };

  const handlePageChange = (event, page) => {
    setPage(page);
  };

  const actions = (webhook) => (
    <div className="flex justify-between gap-4">
      <WebhookDetail
        data={webhook}
        reload={setReloadTime}
      />
      <DeleteWebhook
        uuid={webhook.id}
        reload={setReloadTime}
      />
    </div>
  );

  const columns = [
    {
      code: 'title',
      title: 'Title',
    },
    {
      code: 'status',
      title: 'Status',
      type: 'status',
    },
    {
      code: 'operation',
      title: 'Operation',
      getValue: (row) => webhookOperations.filter((operation) => operation.value === row.operation)[0].label,
    },
  ];

  return (
    <div className="w-full">
      <div className="flex justify-between gap-2 items-center mb-7">
        <Typography variant="h4" className="flex items-center">
          Webhooks
          <NextLink
            target="_blank"
            className="ml-1"
            href="https://docs.dbvisor.pro/s/wiki/doc/webhooks-cauIOT5Uug"
          >
            <InfoIcon />
          </NextLink>
        </Typography>

        <WebhookDetail reload={setReloadTime} />
      </div>

      <DataGrid
        columns={columns}
        rows={webhooks}
        getActions={actions}
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
