import React from 'react';
import PropTypes from 'prop-types';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  Button,
  TextField,
  Typography
} from '@mui/material';
import NextLink from 'next/link';

export function ForgotPassword(props) {
  const { invitationData, handleSubmit } = props;
  const formik = useFormik({
    initialValues: {
      email: '',
      submit: null,
    },
    validationSchema: Yup.object({
      email: Yup.string().email('Must be a valid email').max(255).required(invitationData)
    }),
    onSubmit: handleSubmit,
  });

  return (
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
        onChange={formik.handleChange}
        disabled={!!invitationData}
        type="email"
        value={formik.values.email}
      />

      {formik.errors.submit && (
      <Typography
        color="error"
        sx={{ mt: 3 }}
        variant="body2"
      >
        {formik.errors.submit}
      </Typography>
      )}

      <div className="flex items-center justify-between gap-4 flex-wrap">
        <NextLink
          href="/auth/login"
          className="link-0 link-1"
        >
          Back to login
        </NextLink>

        <Button
          className="button-0 !min-w-[200px]"
          type="submit"
        >
          Continue
        </Button>
      </div>
    </form>
  );
}

ForgotPassword.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  invitationData: PropTypes.object,
  handleSubmit: PropTypes.func,
};
