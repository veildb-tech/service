import React, { useState } from 'react';
import Head from 'next/head';
import PlusIcon from '@heroicons/react/24/solid/PlusIcon';
import {
  Button, SvgIcon, Alert, Typography
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { useQuery } from '@apollo/client';
import { getUuidFromRelation } from 'src/utils/uuid';
import { GET_RULES } from 'src/queries';
import { DataGrid } from 'src/components/grid/data-grid';
import { useUrl } from 'src/hooks/use-url';
import NextLink from 'next/link';
import Breadcrumbs from 'src/components/breadcrumbs';
import { EditIcon, InfoIcon } from 'src/elements/icons';

function Page() {
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [page, setPage] = useState(0);

  const { loading, error, data } = useQuery(GET_RULES, {
    variables: {
      itemsPerPage,
      page: page + 1,
    },
  });
  const url = useUrl();

  const databaseRules = [];
  if (data && data.databaseRules) {
    // eslint-disable-next-line array-callback-return
    data.databaseRules.collection.map((rule) => {
      databaseRules.push({
        ...rule,
        database: rule.db ? (
          rule.db.name
        ) : (
          <Alert
            variant="outlined"
            severity="error"
          >
            Database is not assigned to this rule
          </Alert>
        ),
      });
    });
  }

  const totalCount = data && data.databaseRules ? data.databaseRules.paginationInfo.totalCount : 0;
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
      code: 'database',
      title: 'Database',
    },
  ];

  const getActions = (rule) => (
    <NextLink
      href={url.getUrl(`rules/${getUuidFromRelation(rule.id)}`)}
      className="link-0 link-1"
    >
      <EditIcon />
      Edit
    </NextLink>
  );

  return (
    <>
      <Head>
        <title>Database Rules</title>
      </Head>

      <main>
        <div className="flex w-full justify-between">
          <div>
            <Breadcrumbs
              className={'mb-1'}
              collection={[
                { url: '/', title: 'Overview' },
                { title: 'Database Rules' }
              ]}
            />

            <Typography
              variant="h1"
              className="!mb-12 flex items-baseline"
            >
              Database Rules
              <NextLink
                target="_blank"
                className="ml-2"
                href="https://dbvisor.gitbook.io/"
              >
                <InfoIcon />
              </NextLink>
            </Typography>
          </div>
          <div>
            <Button
              className="button-0 !min-w-[91px]"
              href="rules/create"
            >
              <SvgIcon fontSize="small">
                <PlusIcon />
              </SvgIcon>
              <span>Add</span>
            </Button>
          </div>
        </div>

        <div className="card p-0">
          <DataGrid
            columns={columns}
            rows={databaseRules}
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
