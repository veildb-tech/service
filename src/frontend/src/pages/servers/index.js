import React, { useEffect, useState } from 'react';
import Head from 'next/head';
import { Typography } from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { useQuery } from '@apollo/client';
import { getUuidFromRelation } from 'src/utils/uuid';
import { GET_SERVERS } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { useUrl } from 'src/hooks/use-url';
import NextLink from 'next/link';
import Breadcrumbs from 'src/components/breadcrumbs';
import { useRouter } from 'next/router';
import { InfoIcon, EditIcon } from 'src/elements/icons';
import { DeleteServer } from 'src/sections/server/delete-server';

function Page() {
  const router = useRouter();
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [page, setPage] = useState(0);
  const [reloadTime, setReloadTime] = useState(router.query.reload ?? null);

  const {
    loading,
    error,
    data,
    refetch
  } = useQuery(GET_SERVERS, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
  });
  const url = useUrl();

  const servers = data && data.servers ? data.servers.collection : [];
  const totalCount = data && data.servers ? data.servers.paginationInfo.totalCount : 0;
  const handleRowsPerPageChange = (event) => {
    setItemsPerPage(event.target.value);
    setPage(0);
  };

  useEffect(() => {
    reloadTime && refetch();
  }, [refetch, reloadTime]);

  const handlePageChange = (event, page) => {
    setPage(page);
  };

  const columns = [
    {
      code: 'name',
      title: 'Name',
    },
    {
      code: 'url',
      title: 'Url',
    },
    {
      code: 'status',
      title: 'Status',
      type: 'status',
    },
  ];

  const getActions = (server) => (
    <div className="flex items-center justify-between">
      <NextLink
        href={url.getUrl(`servers/${getUuidFromRelation(server.id)}`)}
        className="link-0 link-1"
      >
        <EditIcon />
        Edit
      </NextLink>
      <DeleteServer
        reload={setReloadTime}
        uuid={server.id}
      />
    </div>
  );

  return (
    <>
      <Head>
        <title>Servers</title>
      </Head>

      <main>
        <div className="flex w-full justify-between">
          <div>
            <Breadcrumbs
              className={'mb-1'}
              collection={[
                { url: '/', title: 'Overview' },
                { title: 'Servers' }
              ]}
            />

            <Typography
              variant="h1"
              className="!mb-12 flex items-baseline"
            >
              Servers
              <NextLink
                target="_blank"
                className="ml-2"
                href="https://docs.dbvisor.pro/s/wiki/doc/servers-rG60T86EtH"
              >
                <InfoIcon />
              </NextLink>
            </Typography>
          </div>
        </div>

        <div className="card p-0">
          <DataGrid
            columns={columns}
            rows={servers}
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
