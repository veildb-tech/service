import React, { useState } from 'react';
import Head from 'next/head';
import { useRouter } from 'next/router';
import {
  Alert,
  Typography
} from '@mui/material';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';
import { ChangePassword } from 'src/sections/auth/change-password';
import { CHECK_RESTORE_PASSWORD_HASH_MUTATION } from 'src/queries';
import { useMutation } from '@apollo/client';
import { SplashScreen } from 'src/layouts/splash-screen';

function Page() {
  const router = useRouter();
  const { hash } = router.query;

  const [error, setError] = useState(false);
  const [validated, setValidated] = useState(false);
  const [checkHash, { loading: checkingHash }] = useMutation(CHECK_RESTORE_PASSWORD_HASH_MUTATION);

  if (!validated && !checkingHash && hash) {
    checkHash({
      variables: {
        hash
      }
    }).catch(() => {
      setError(true);
    }).finally(() => setValidated(true));
  }

  if (checkingHash || !validated) {
    return <SplashScreen />;
  }

  return (
    <>
      <Head>
        <title>Restore password</title>
      </Head>

      <div className="flex flex-col items-center justify-center w-full bg-dbm-color-white">
        {error && validated && <Alert className="!p-0" severity="error">Restore password url is not valid or expired!</Alert>}

        {!error && (
          <div className="flex flex-col w-full max-w-[612px] p-4">
            <Typography
              variant="h1"
              className="!mb-3"
            >
              Change password
            </Typography>

            <div className="flex gap-3.5 mb-9">
              <span className="text-dbm-color-3 text-sm">
                {'Please enter new password.'}
              </span>
            </div>

            <ChangePassword hash={hash} />
          </div>
        )}
      </div>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
