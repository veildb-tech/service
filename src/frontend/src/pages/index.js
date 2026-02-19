import * as React from 'react';
import Head from 'next/head';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { Typography } from '@mui/material';
import { IndexInstallation } from 'src/sections/index/installation';
import { IndexOverview } from 'src/sections/index/overview';
import { useAuth } from 'src/hooks/use-auth';
import { WORKSPACE_AGGREGATION } from 'src/queries';
import { useQuery } from '@apollo/client';

function Page() {
  const { data } = useQuery(WORKSPACE_AGGREGATION);
  const auth = useAuth();
  const user = auth?.user;
  const firstname = user?.firstname;
  const isNewWorkspace = !data?.databases.paginationInfo.totalCount
    || !data?.servers.paginationInfo.totalCount;

  return (
    <>
      <Head>
        <title>Overview | VeilDB</title>
      </Head>

      <div className="flex flex-col h-full">
        <main className="h-full">
          <Typography
            variant="h1"
            className="!mb-6"
          >
            Overview
          </Typography>

          {
            data && (
              <div>
                <div className="max-h-[130px] h-full mb-14">
                  {
                    firstname
                    && (
                      <Typography
                        className="!mb-5"
                        variant="h4"
                      >
                        {`Hi, ${firstname}`}
                      </Typography>
                    )
                  }

                  <IndexOverview data={data} />
                </div>

                <IndexInstallation isNewWorkspace={isNewWorkspace} />
              </div>
            )
          }
        </main>
      </div>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
