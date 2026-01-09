import React from 'react';
import Head from 'next/head';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';
import { useRouter } from 'next/router';
import { SplashScreen } from 'src/layouts/splash-screen';
import { WorkspaceInvitation } from 'src/sections/auth/invitation';

function Page() {
  const router = useRouter();
  const { invitation: invitationId } = router.query;
  const content = !invitationId ? (
    <SplashScreen />
  ) : (
    <WorkspaceInvitation invitationId={invitationId} />
  );

  return (
    <>
      <Head>
        <title>Invitation login</title>
      </Head>

      <div className="flex flex-col items-center justify-center w-full bg-dbm-color-white">
        <div className="flex flex-col w-full max-w-[612px] p-4">
          {content}
        </div>
      </div>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
