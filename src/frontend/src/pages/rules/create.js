import React from 'react';
import Head from 'next/head';
import { Box, Container } from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { RulesProvider } from 'src/contexts/rule/rule-context';
import { RuleAdditionsProvider } from 'src/contexts/rule/rule-additions-context';
import { RuleDetails } from 'src/sections/rule/rule-details';
import Breadcrumbs from 'src/components/breadcrumbs';

function Page() {
  const initialData = {
    name: '',
    schedule: '0 7 * * *',
  };

  return (
    <>
      <Head>
        <title>Create new rule</title>
      </Head>
      <Box
        component="main"
        sx={{
          flexGrow: 1,
          py: 8,
        }}
      >
        <Container maxWidth="lg">
          <Breadcrumbs
            className={'mb-1'}
            collection={[
              { url: '/rules', title: 'Rules' },
              { title: 'Create new rule' }
            ]}
          />
          <RulesProvider>
            <RuleAdditionsProvider>
              <RuleDetails data={initialData} />
            </RuleAdditionsProvider>
          </RulesProvider>
        </Container>
      </Box>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
