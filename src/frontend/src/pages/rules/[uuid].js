import React from 'react';
import Head from 'next/head';
import { CircularProgress } from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { RulesProvider } from 'src/contexts/rule/rule-context';
import { RuleAdditionsProvider } from 'src/contexts/rule/rule-additions-context';
import { RuleDetails } from 'src/sections/rule/rule-details';
import { useRouter } from 'next/router';
import { buildUrl } from 'src/utils/uuid';
import { useQuery } from '@apollo/client';
import { GET_RULE } from 'src/queries';
import Breadcrumbs from 'src/components/breadcrumbs';

function Page() {
  const router = useRouter();
  const id = buildUrl('database_rules', router.query.uuid);
  const { loading, data } = useQuery(GET_RULE, {
    variables: { id },
  });

  return (
    <>
      <Head>
        <title>Edit rule</title>
      </Head>

      <main>
        {loading ? (
          <div className="flex items-center justify-center h-screen overflow-hidden">
            <CircularProgress />
          </div>
        ) : (
          <>
            <Breadcrumbs
              className={'mb-1'}
              collection={[
                { url: '/rules', title: 'Rules' },
                { title: data.databaseRule.name }
              ]}
            />

            <RulesProvider initial={data.databaseRule.rule}>
              <RuleAdditionsProvider initial={data.databaseRule.addition}>
                <RuleDetails data={data.databaseRule} />
              </RuleAdditionsProvider>
            </RulesProvider>
          </>
        )}
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
