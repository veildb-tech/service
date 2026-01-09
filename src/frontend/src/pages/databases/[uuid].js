import React, { useState, useEffect } from 'react';
import Head from 'next/head';
import {
  CircularProgress,
} from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { DatabaseDetails } from 'src/sections/database/database-details';
import { useQuery } from '@apollo/client';
import { useRouter } from 'next/router';
import { buildUrl } from 'src/utils/uuid';
import { GET_DATABASE } from 'src/queries';

function Page() {
  const router = useRouter();
  const id = buildUrl('databases', router.query.uuid);
  const {
    loading, data, refetch
  } = useQuery(GET_DATABASE, {
    variables: { id },
  });

  const [reloadTime, setReloadTime] = useState(null);
  useEffect(() => {
    refetch();
  }, [
    reloadTime,
    refetch
  ]);

  if (data && data.database === null) {
    router.replace('/404');
    return '';
  }

  return (
    <>
      <Head>
        <title>Databases</title>
      </Head>

      <main>
        { loading ? (
          <CircularProgress />
        ) : (
          <DatabaseDetails
            reload={setReloadTime}
            data={data.database}
          />
        )}
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
