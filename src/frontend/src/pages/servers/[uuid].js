import React from 'react';
import Head from 'next/head';
import {
  CircularProgress
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { ServerDetails } from 'src/sections/server/server-details';
import { useQuery } from '@apollo/client';
import { useRouter } from 'next/router';
import { buildUrl } from 'src/utils/uuid';
import { GET_SERVER } from 'src/queries';
import NotFound from 'src/components/not-found';

function Page() {
  const router = useRouter();

  const id = buildUrl('servers', router.query.uuid);
  const { loading, data } = useQuery(GET_SERVER, {
    variables: { id },
  });

  if (!loading && !data) {
    return (<NotFound />);
  }

  return (
    <>
      <Head>
        <title>Servers</title>
      </Head>
      <main>
        { loading ? (
          <CircularProgress />
        ) : (
          <ServerDetails data={data.server} />
        )}
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
