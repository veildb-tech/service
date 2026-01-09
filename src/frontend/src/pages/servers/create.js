import React from 'react';
import Head from 'next/head';
import { Box, Container, Stack } from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { ServerDetails } from 'src/sections/server/server-details';

function Page() {
  const initialValue = {
    name: '',
    status: 'enabled',
  };

  return (
    <>
      <Head>
        <title>Servers</title>
      </Head>
      <Box
        component="main"
        className="!py-0"
        sx={{
          flexGrow: 1,
          py: 8,
        }}
      >
        <Container maxWidth="xl">
          <Stack spacing={3}>
            <ServerDetails data={initialValue} />
          </Stack>
        </Container>
      </Box>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
