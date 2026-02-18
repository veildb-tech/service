import React, { useState, useEffect } from 'react';
import Head from 'next/head';
import {
  Typography
} from '@mui/material';
import NextLink from 'next/link';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { getUuidFromRelation } from 'src/utils/uuid';
import { DeleteDatabase } from 'src/sections/database/delete-database';
import { GET_DATABASES } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { useQuery } from '@apollo/client';
import { useUrl } from 'src/hooks/use-url';
import { usePermission } from 'src/hooks/use-permission';
import { useRouter } from 'next/router';
import Breadcrumbs from 'src/components/breadcrumbs';
import { EditIcon, InfoIcon } from 'src/elements/icons';

function Page() {
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const router = useRouter();
  const [page, setPage] = useState(0);
  const [reloadTime, setReloadTime] = useState(router.query.reload ?? null);
  const { canSee } = usePermission();
  const canEdit = canSee('database.edit');

  const {
    data, error, loading, refetch
  } = useQuery(GET_DATABASES, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
    fetchPolicy: 'network-only'
  });
  const url = useUrl();

  useEffect(() => {
    reloadTime && refetch();
  }, [refetch, reloadTime]);

  const databases = data && data.databases ? data.databases.collection : [];
  const totalCount = data && data.databases ? data.databases.paginationInfo.totalCount : 0;
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
      title: 'Name',
    },
    {
      code: 'status',
      title: 'Status',
      type: 'status',
    },
    {
      code: 'engine',
      title: 'Engine',
    },
    {
      code: 'updated_at',
      title: 'Updated At',
      type: 'date',
    },
  ];

  if (canSee('server.view')) {
    columns.push({
      code: 'server',
      title: 'Server',
      getValue: (item) => item.server.name
    });
  }

  const getActions = (database) => (
    <div className="flex items-center justify-between">
      <NextLink
        className="link-0 link-1"
        href={url.getUrl(`databases/${getUuidFromRelation(database.id)}`)}
      >
        {canEdit && <EditIcon /> }
        { canEdit ? 'Edit' : 'View' }
      </NextLink>
      {
        canEdit
        && (
        <DeleteDatabase
          reload={setReloadTime}
          uuid={database.id}
        />
        )
      }
    </div>
  );

  return (
    <>
      <Head>
        <title>Databases</title>
      </Head>

      <main>
        <Breadcrumbs
          className={'mb-1'}
          collection={[
            { url: '/', title: 'Overview' },
            { title: 'Databases' }
          ]}
        />

        <Typography
          variant="h1"
          className="!mb-12 flex items-baseline"
        >
          Databases
          <NextLink
            target="_blank"
            className="ml-2"
            href="https://dbvisor.gitbook.io/"
          >
            <InfoIcon />
          </NextLink>
        </Typography>

        <div className="card p-0">
          <DataGrid
            columns={columns}
            rows={databases}
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
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
