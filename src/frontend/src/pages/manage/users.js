import Head from 'next/head';
import {
  Alert,
  CircularProgress, Typography
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { WorkspaceNavigation } from 'src/sections/workspace/navigation';
import { WorkspaceUsers } from 'src/sections/workspace/users';
import { useQuery } from '@apollo/client';
import { CURRENT_WORKSPACE_USERS_QUERY } from 'src/queries';
import * as React from 'react';
import Breadcrumbs from 'src/components/breadcrumbs';

function Page() {
  const { loading, error, data } = useQuery(CURRENT_WORKSPACE_USERS_QUERY);

  return (
    <>
      <Head>
        <title>Manage Workspace Users</title>
      </Head>

      <main>
        <Breadcrumbs
          className={'mb-1'}
          collection={[
            { url: '/manage', title: 'Workspace Configurations' },
            { title: 'Manage users' }
          ]}
        />

        <Typography
          variant="h1"
          className="!mb-12"
        >
          Manage workspace
        </Typography>

        <div className="flex gap-10">
          <WorkspaceNavigation />

          {loading && <CircularProgress />}
          {error && <Alert severity="error">{error.message}</Alert>}
          {data && <WorkspaceUsers data={data.currentWorkspace} />}
        </div>
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
