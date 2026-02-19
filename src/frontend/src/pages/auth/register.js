import React, { useState } from 'react';
import Head from 'next/head';
import NextLink from 'next/link';
import { useRouter } from 'next/router';
import {
  Alert,
  Typography
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';
import { useQuery } from '@apollo/client';
import { buildUrl } from 'src/utils/uuid';
import { RegisterForm } from 'src/sections/auth/register-form';
import { USER_INVITATION_QUERY } from 'src/queries';

function Page() {
  const router = useRouter();
  const auth = useAuth();
  const [success, setSuccess] = useState(false);
  const { invitation: invitationId } = router.query;
  const { data: invitationData } = useQuery(USER_INVITATION_QUERY, {
    variables: {
      id: buildUrl('user_invitations', invitationId),
    },
    skip: !invitationId,
  });

  const handleRegistration = async (values, helpers) => {
    try {
      await auth
        .signUp(
          values.firstname,
          values.lastname,
          values.password,
          values.email,
          values.company,
          invitationId,
        )
        .then(() => {
          setSuccess(true);
          auth.signIn(values.email, values.password);
        });
    } catch (err) {
      helpers.setStatus({ success: false });
      helpers.setErrors({ submit: err.message });
      helpers.setSubmitting(false);
    }
  };

  return (
    <>
      <Head>
        <title>Register | VeilDB</title>
      </Head>

      <div className="flex flex-col items-center justify-center w-full bg-dbm-color-white">
        {success && <Alert className="mb-6" severity="success">Account successfully created!</Alert>}

        <div className="flex flex-col w-full max-w-[612px] p-4">
          <Typography
            variant="h1"
            className="!mb-3"
          >
            Register
          </Typography>

          <div className="flex gap-3.5 mb-9">
            <span className="text-dbm-color-3 text-sm">
              Already have an account?
            </span>

            <NextLink
              href="/auth/login"
              className="link-0 link-1"
            >
              Log in
            </NextLink>
          </div>

          <RegisterForm
            invitationData={invitationData}
            handleSumbit={handleRegistration}
          />
        </div>
      </div>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
