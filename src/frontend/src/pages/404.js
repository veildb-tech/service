import React from 'react';
import Head from 'next/head';
import NotFound from 'src/components/not-found';

function Page() {
  return (
    <>
      <Head>
        <title>404 | DB Visor</title>
      </Head>

      <NotFound />
    </>
  );
}

export default Page;
