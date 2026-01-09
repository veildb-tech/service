import React from 'react';
import Head from 'next/head';
import {
  Typography,
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { WorkspaceNavigation } from 'src/sections/workspace/navigation';
import { WorkspaceWebhooks } from 'src/sections/workspace/webhooks';
import Breadcrumbs from 'src/components/breadcrumbs';

function Page() {
  return (
    <>
      <Head>
        <title>Webhooks</title>
      </Head>

      <main>
        <Breadcrumbs
          className={'mb-1'}
          collection={[
            { url: '/manage', title: 'Workspace Configurations' },
            { title: 'Webhooks' }
          ]}
        />

        <Typography
          variant="h1"
          className="!mb-12"
        >
          Webhooks
        </Typography>

        <div className="flex gap-10">
          <WorkspaceNavigation />

          <WorkspaceWebhooks />
        </div>

      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
