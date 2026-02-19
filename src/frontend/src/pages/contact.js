import * as React from 'react';
import Head from 'next/head';
import { Layout as DashboardLayout } from 'src/layouts/dashboard/layout';
import {
  Alert,
  Button,
  Divider,
  TextareaAutosize,
  TextField,
  Typography
} from '@mui/material';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { useMutation } from '@apollo/client';
import { SEND_SUPPORT_EMAIL } from 'src/queries';
import { useState } from 'react';
import { usePermission } from 'src/hooks/use-permission';

function Page() {
  const [sendSupportEmail] = useMutation(SEND_SUPPORT_EMAIL);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState(false);
  const { isAdmin } = usePermission();
  const formik = useFormik({
    initialValues: {
      subject: '',
      message: ''
    },
    validationSchema: Yup.object({
      subject: Yup.string().max(255).required(),
      message: Yup.string().required(),
    }),
    onSubmit: (data) => {
      try {
        sendSupportEmail({
          variables: {
            subject: data.subject,
            message: data.message
          }
        }).then(() => {
          setSuccess(true);
        }).catch(() => {
          setError(true);
        });
      } catch (exception) {
        setError(true);
      }
    },
  });

  return (
    <>
      <Head>
        <title>Contact Us | VeilDB</title>
      </Head>

      <div className="flex flex-col h-full">
        <main className="h-full">
          <Typography
            variant="h1"
            className="!mb-6"
          >
            Contact Us
          </Typography>
          {success && (
            <Alert severity="success">
              Your message has been successfully sent.
              Our support team has received your inquiry and will get back to you as soon as possible.
            </Alert>
          )}
          {error && (<Alert severity="error">Oops! Something Went Wrong</Alert>)}
          {/* eslint-disable-next-line react/no-unescaped-entities */}
          {!isAdmin() && (<Alert severity="error">You don't have permissions to view this page</Alert>)}
          {isAdmin() && (
            <form
              noValidate
              onSubmit={formik.handleSubmit}
              className="p-7 rounded-2xl flex flex-col gap-[22px]"
            >
              <div className="card">
                <div className="card-content flex flex-col gap-7">
                  <TextField
                    className="input-0 w-full"
                    error={!!(formik.touched.subject && formik.errors.subject)}
                    helperText={formik.touched.subject && formik.errors.subject}
                    label="Subject"
                    name="subject"
                    placeholder="Subject"
                    onBlur={formik.handleBlur}
                    onChange={formik.handleChange}
                    value={formik.values.subject}
                  />

                  <TextareaAutosize
                    minRows={4}
                    className="input-0 w-full p-4 rounded-2xl border-2"
                    error={!!(formik.touched.message && formik.errors.message)}
                    helperText={formik.touched.message && formik.errors.message}
                    label="Subject"
                    name="message"
                    onBlur={formik.handleBlur}
                    onChange={formik.handleChange}
                    value={formik.values.message}
                  />

                  <Divider className="!my-7" />

                  <div className="card-content flex justify-end w-full">
                    <Button
                      className="button-0 !min-w-[170px]"
                      type="submit"
                    >
                      Send
                    </Button>
                  </div>
                </div>
              </div>
            </form>
          )}
        </main>
      </div>
    </>
  );
}

Page.getLayout = (page) => <DashboardLayout>{page}</DashboardLayout>;

export default Page;
