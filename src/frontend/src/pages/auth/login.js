import React, { useState } from 'react';
import Head from 'next/head';
import NextLink from 'next/link';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  Button,
  TextField,
  Typography,
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import { Layout as AuthLayout } from 'src/layouts/auth/layout';

function Page() {
  const auth = useAuth();
  const [loading, setLoading] = useState(false);
  const formik = useFormik({
    initialValues: {
      email: '',
      password: '',
      submit: null,
    },
    validationSchema: Yup.object({
      email: Yup.string().email('Must be a valid email').max(255).required('Email is required'),
      password: Yup.string().max(255).required('Password is required'),
    }),
    onSubmit: async (values, helpers) => {
      setLoading(true);
      try {
        await auth.signIn(values.email, values.password).then(() => {
          setLoading(false);
        });
      } catch (err) {
        setLoading(false);
        helpers.setStatus({ success: false });
        helpers.setErrors({ submit: err.message });
        helpers.setSubmitting(false);
      }
    },
  });

  return (
    <>
      <Head>
        <title>Login</title>
      </Head>

      <div className="flex flex-col items-center justify-center w-full bg-dbm-color-white">
        <div className="flex flex-col w-full max-w-[612px] p-4">
          <Typography
            variant="h1"
            className="!mb-3"
          >
            Login
          </Typography>

          <div className="flex gap-3.5 mb-9">
            <span className="text-dbm-color-3 text-sm">
              {'Don\'t have an account?'}
            </span>

            <NextLink
              href="/auth/register"
              className="link-0 link-1"
            >
              Register
            </NextLink>
          </div>

          <form
            noValidate
            onSubmit={formik.handleSubmit}
            className="shadow-dbm-1 p-7 rounded-2xl flex flex-col gap-[22px]"
          >

            <TextField
              className="input-2 w-full"
              error={!!(formik.touched.email && formik.errors.email)}
              helperText={formik.touched.email && formik.errors.email}
              label="Email Address"
              name="email"
              onBlur={formik.handleBlur}
              disabled={loading}
              onChange={formik.handleChange}
              type="email"
              value={formik.values.email}
            />

            <TextField
              className="input-2 w-full"
              error={!!(formik.touched.password && formik.errors.password)}
              helperText={formik.touched.password && formik.errors.password}
              label="Password"
              name="password"
              disabled={loading}
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              type="password"
              value={formik.values.password}
            />

            {formik.errors.submit && (
              <Typography
                color="error"
                variant="body2"
              >
                {formik.errors.submit}
              </Typography>
            )}

            <div className="flex items-center justify-between gap-4 flex-wrap">
              <NextLink
                href="/auth/forgot-password"
                className="link-0 link-1"
              >
                Forgot password?
              </NextLink>

              <Button
                className="button-0 !min-w-[200px]"
                type="submit"
                disabled={loading}
              >
                Continue
              </Button>
            </div>
          </form>
        </div>
      </div>
    </>
  );
}

Page.getLayout = (page) => <AuthLayout>{page}</AuthLayout>;

export default Page;
