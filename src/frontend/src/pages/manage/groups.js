import React from 'react';
import Head from 'next/head';
import {
  Typography,
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { WorkspaceNavigation } from 'src/sections/workspace/navigation';
import { WorkspaceGroups } from 'src/sections/workspace/groups';
import Breadcrumbs from 'src/components/breadcrumbs';

function Page() {
  return (
    <>
      <Head>
        <title>Manage Workspace Groups</title>
      </Head>

      <main>
        <Breadcrumbs
          className={'mb-1'}
          collection={[
            { url: '/manage', title: 'Workspace Configurations' },
            { title: 'Workspace groups' }
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

          <WorkspaceGroups />
        </div>
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
