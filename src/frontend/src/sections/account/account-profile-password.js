import React, { useState } from 'react';
import {
  Button,
  Alert,
  Divider,
  TextField,
  Typography
} from '@mui/material';
import * as Yup from 'yup';
import { useFormik } from 'formik';
import { UPDATE_CURRENT_USER_PASSWORD } from 'src/queries';
import { useMutation } from '@apollo/client';

export function AccountProfilePassword() {
  const [showSuccessMessage, setShowSuccessMessage] = useState(false);
  const [updatePassword, { loading }] = useMutation(
    UPDATE_CURRENT_USER_PASSWORD,
  );

  const formik = useFormik({
    initialValues: {
      newPassword: '',
      confirmPassword: '',
      oldPassword: '',
    },

    validationSchema: Yup.object({
      newPassword: Yup.string().max(255).required('This field is required'),
      confirmPassword: Yup.string()
        .max(255)
        .oneOf([Yup.ref('newPassword')], 'Passwords must match')
        .required('Please confirm new password'),
      oldPassword: Yup.string().max(255).required('You need to enter current password'),
    }),

    onSubmit: async (values, helpers) => {
      updatePassword({
        variables: values,
      })
        .then((result) => {
          if (result.data.updatePasswordUser.user.id) {
            setShowSuccessMessage(true);
          }
        })
        .catch((error) => {
          helpers.setStatus({ success: false });
          helpers.setErrors({ submit: error.message });
          helpers.setSubmitting(false);
        });
    },
  });

  return (
    <form
      autoComplete="off"
      onSubmit={formik.handleSubmit}
    >
      <div className="card">
        <div className="card-content flex flex-col gap-1 mb-5">
          <Typography
            className="mb-1"
            variant="h4"
          >
            Change password
          </Typography>

          <div className="sub-heading-0">The information can be edited</div>
        </div>

        <div className="card-content flex flex-col gap-7">
          {showSuccessMessage && (
            <Alert severity="success">Password has been changed successfully!</Alert>
          )}
          {formik.errors.submit && <Alert severity="error">{formik.errors.submit}</Alert>}

          <div className="flex w-full gap-7">
            <TextField
              className="input-0 w-full"
              error={!!(formik.touched.newPassword && formik.errors.newPassword)}
              fullWidth
              type="password"
              helperText={formik.touched.newPassword && formik.errors.newPassword}
              label="New Password"
              placeholder="New Password"
              name="newPassword"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.newPassword}
            />

            <TextField
              className="input-0 w-full"
              error={!!(formik.touched.confirmPassword && formik.errors.confirmPassword)}
              fullWidth
              helperText={formik.touched.confirmPassword && formik.errors.confirmPassword}
              label="Confirm Password"
              placeholder="Confirm Password"
              name="confirmPassword"
              type="password"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.confirmPassword}
            />
          </div>

          <div className="flex w-full gap-7">
            <TextField
              className="input-0 w-full"
              error={!!(formik.touched.oldPassword && formik.errors.oldPassword)}
              fullWidth
              helperText={formik.touched.oldPassword && formik.errors.oldPassword}
              label="Current Password"
              placeholder="Current Password"
              type="password"
              name="oldPassword"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.oldPassword}
            />

            <div className="w-full" />
          </div>
        </div>

        <Divider className="!my-7" />

        <div className="card-content flex justify-end w-full">
          <Button
            className="button-0 !min-w-[170px]"
            type="submit"
            disabled={loading}
          >
            Change password
          </Button>
        </div>
      </div>
    </form>
  );
}
