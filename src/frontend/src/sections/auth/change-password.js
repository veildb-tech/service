import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  Alert,
  Button,
  TextField,
  Typography
} from '@mui/material';
import { useMutation } from '@apollo/client';
import { RESTORE_PASSWORD_MUTATION } from 'src/queries';
import { useRouter } from 'next/router';

export function ChangePassword(props) {
  const { hash } = props;

  const [success, setSuccess] = useState(false);
  const [restorePassword, { loading }] = useMutation(RESTORE_PASSWORD_MUTATION);

  const router = useRouter();

  const handleSubmit = async (values, helpers) => {
    restorePassword({
      variables: {
        newPassword: values.newPassword,
        confirmPassword: values.confirmPassword,
        hash
      }
    }).then(() => {
      setSuccess(true);
      setTimeout(() => router.push('/'), 3000);
    }).catch((error) => {
      helpers.setStatus({ success: false });
      helpers.setErrors({ submit: error.message });
      helpers.setSubmitting(false);
    });
  };

  const formik = useFormik({
    initialValues: {
      newPassword: '',
      confirmPassword: '',
      submit: null,
    },
    validationSchema: Yup.object({
      newPassword: Yup.string().max(255).required('Password is required'),
      confirmPassword: Yup.string()
        .max(255)
        .oneOf([Yup.ref('newPassword')], 'Passwords must match')
        .required('Please confirm new password'),
    }),
    onSubmit: handleSubmit,
  });

  if (success) {
    return <Alert severity="success">Password successfully updated! Redirecting ...</Alert>;
  }

  return (
    <form
      noValidate
      onSubmit={formik.handleSubmit}
      className="shadow-dbm-1 p-7 rounded-2xl flex flex-col gap-[22px]"
    >
      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.newPassword && formik.errors.newPassword)}
        helperText={formik.touched.newPassword && formik.errors.newPassword}
        label="Password"
        name="newPassword"
        onBlur={formik.handleBlur}
        onChange={formik.handleChange}
        type="password"
        value={formik.values.newPassword}
      />

      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.confirmPassword && formik.errors.confirmPassword)}
        helperText={formik.touched.confirmPassword && formik.errors.confirmPassword}
        label="Confirm Password"
        name="confirmPassword"
        onBlur={formik.handleBlur}
        onChange={formik.handleChange}
        type="password"
        value={formik.values.confirmPassword}
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

      <div className="flex items-center justify-end gap-4 flex-wrap">
        <Button
          className="button-0 !min-w-[200px]"
          type="submit"
          disabled={loading}
        >
          Reset Password
        </Button>
      </div>
    </form>
  );
}

ChangePassword.propTypes = {
  hash: PropTypes.string
};
