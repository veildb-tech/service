import React from 'react';
import Head from 'next/head';
import { Box } from '@mui/material';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';
import { WorkspaceSelector } from 'src/sections/auth/workspace-selector';

function Page() {
  return (
    <>
      <Head>
        <title>Login | Select Workspace</title>
      </Head>
      <Box
        sx={{
          backgroundColor: 'background.paper',
          flex: '1 1 auto',
          alignItems: 'center',
          display: 'flex',
          justifyContent: 'center',
        }}
      >
        <Box
          sx={{
            maxWidth: 550,
            px: 3,
            py: '100px',
            width: '100%',
          }}
        >
          <WorkspaceSelector />
        </Box>
      </Box>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
