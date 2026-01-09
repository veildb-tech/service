import React, { useState } from 'react';
import Head from 'next/head';
import {
  Alert,
  Typography
} from '@mui/material';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';
import { ForgotPassword } from 'src/sections/auth/forgot-password';
import { FORGOT_PASSWORD_MUTATION } from 'src/queries';
import { useMutation } from '@apollo/client';

function Page() {
  const [success, setSuccess] = useState(false);

  const [forgotPasswordRequest] = useMutation(FORGOT_PASSWORD_MUTATION);

  const handleForgotPassword = async (values, helpers) => {
    forgotPasswordRequest({
      variables: {
        email: values.email
      }
    }).then(() => {
      setSuccess(true);
    }).catch((error) => {
      helpers.setStatus({ success: false });
      helpers.setErrors({ submit: error.message });
      helpers.setSubmitting(false);
    });
  };

  return (
    <>
      <Head>
        <title>Forgot password</title>
      </Head>

      <div className="flex flex-col items-center justify-center w-full bg-dbm-color-white">
        <div className="flex flex-col w-full max-w-[612px] p-4">
          <Typography
            variant="h1"
            className="!mb-3"
          >
            Forgot password?
          </Typography>

          <div className="flex flex-wrap gap-3.5 mb-9 items-center">
            <span className="text-dbm-color-3 text-sm">
              Please enter your email address below to receive a password reset link.
            </span>
          </div>

          {success && <Alert className="mb-4" severity="success">Reset password instructions have been sent to your email.</Alert>}

          <ForgotPassword handleSubmit={handleForgotPassword} />
        </div>
      </div>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
