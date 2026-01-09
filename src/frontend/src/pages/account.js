import React from 'react';
import Head from 'next/head';
import { Typography } from '@mui/material';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import { AccountProfile } from 'src/sections/account/account-profile';
import { AccountProfileDetails } from 'src/sections/account/account-profile-details';
import { AccountProfilePassword } from 'src/sections/account/account-profile-password';
import Breadcrumbs from 'src/components/breadcrumbs';
import { AccountProfileDanger } from '../sections/account/account-profile-danger';

function Page() {
  return (
    <>
      <Head>
        <title>Account</title>
      </Head>

      <main>
        <Breadcrumbs
          className={'mb-1'}
          collection={[
            { url: '/', title: 'Overview' },
            { title: 'Account' }
          ]}
        />

        <Typography
          variant="h1"
          className="!mb-12"
        >
          Account
        </Typography>

        <div className="flex gap-7 items-start">
          <AccountProfile />

          <div className="flex flex-col gap-7 w-full">
            <AccountProfileDetails />
            <AccountProfilePassword />
            <AccountProfileDanger />
          </div>
        </div>
      </main>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
