import React, { useState } from 'react';
import {
  Button,
  Alert,
  Divider,
  TextField, Typography
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import * as Yup from 'yup';
import { useFormik } from 'formik';
import { UPDATE_CURRENT_USER } from 'src/queries';
import { useMutation } from '@apollo/client';

export function AccountProfileDetails() {
  const auth = useAuth();
  const { user } = auth;

  const [showSuccessMessage, setShowSuccessMessage] = useState(false);
  const [updateUser, { loading }] = useMutation(UPDATE_CURRENT_USER);

  const formik = useFormik({
    initialValues: {
      firstname: user.firstname,
      lastname: user.lastname,
      email: user.email,
    },

    validationSchema: Yup.object({
      firstname: Yup.string().max(255).required(),
      lastname: Yup.string().max(255).required(),
      email: Yup.string().max(255).email().required(),
    }),

    onSubmit: async (values, helpers) => {
      updateUser({
        variables: values,
      })
        .then((result) => {
          if (result.data.updateCurrentUser.user) {
            setShowSuccessMessage(true);
            auth.reload(result.data.updateCurrentUser.user);
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
            Profile
          </Typography>

          <div className="sub-heading-0">The information can be edited</div>
        </div>

        <div className="card-content flex flex-col gap-7">
          {showSuccessMessage && (
            <Alert severity="success">Information has been saved successfully!</Alert>
          )}

          {formik.errors.submit && <Alert severity="error">{formik.errors.submit}</Alert>}
          <div className="flex w-full gap-7">
            <TextField
              className="input-0"
              error={!!(formik.touched.firstname && formik.errors.firstname)}
              fullWidth
              helperText={formik.touched.firstname && formik.errors.firstname}
              label="First Name"
              placeholder="Type First Name"
              name="firstname"
              type="text"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.firstname}
            />

            <TextField
              className="input-0"
              error={!!(formik.touched.lastname && formik.errors.lastname)}
              fullWidth
              helperText={formik.touched.lastname && formik.errors.lastname}
              label="Last Name"
              placeholder="Type Last Name"
              name="lastname"
              type="text"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.lastname}
            />
          </div>

          <div className="flex w-full gap-7">
            <TextField
              className="input-0 w-full"
              error={!!(formik.touched.email && formik.errors.email)}
              fullWidth
              helperText={formik.touched.email && formik.errors.email}
              label="Email"
              placeholder="Type Email"
              name="email"
              type="email"
              onBlur={formik.handleBlur}
              onChange={formik.handleChange}
              value={formik.values.email}
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
            Save details
          </Button>
        </div>
      </div>
    </form>
  );
}
