import React from 'react';
import PropTypes from 'prop-types';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  Button,
  TextField,
  Typography
} from '@mui/material';

export function RegisterForm(props) {
  const { invitationData, handleSumbit } = props;
  const getCharacterValidationError = (str) => `Your password must have at least 1 ${str} character`;

  const formik = useFormik({
    initialValues: {
      email: '',
      firstname: '',
      lastname: '',
      company: '',
      password: '',
      submit: null,
    },
    validationSchema: Yup.object({
      email: Yup.string().email('Must be a valid email').max(255).required(invitationData),
      firstname: Yup.string().max(255).required('First name is required'),
      lastname: Yup.string().max(255).required('Last Name is required'),
      company: Yup.string().max(255).required('Company field is required'),
      password: Yup.string().max(255)
        .min(8, 'Password must have at least 8 characters')
        .matches(/[0-9]/, getCharacterValidationError('digit'))
        .matches(/[a-z]/, getCharacterValidationError('lowercase'))
        .matches(/[A-Z]/, getCharacterValidationError('uppercase'))
        .required('Password is required'),
    }),
    onSubmit: handleSumbit,
  });

  if (invitationData && invitationData.userInvitation) {
    formik.values.company = invitationData.userInvitation.workspace.name;
    formik.values.email = invitationData.userInvitation.email;
  }

  return (
    <form
      noValidate
      onSubmit={formik.handleSubmit}
      className="shadow-dbm-1 p-7 rounded-2xl flex flex-col gap-[22px]"
    >
      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.firstname && formik.errors.firstname)}
        helperText={formik.touched.firstname && formik.errors.firstname}
        label="First Name"
        name="firstname"
        onBlur={formik.handleBlur}
        onChange={formik.handleChange}
        value={formik.values.firstname}
      />
      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.lastname && formik.errors.lastname)}
        helperText={formik.touched.lastname && formik.errors.lastname}
        label="Last Name"
        name="lastname"
        onBlur={formik.handleBlur}
        onChange={formik.handleChange}
        value={formik.values.lastname}
      />
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
      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.company && formik.errors.company)}
        helperText={
              formik.errors.company
              ?? 'Please specify your company. This name will be used as workspace name'
          }
        label="Company / Workspace name"
        name="company"
        onBlur={formik.handleBlur}
        disabled={!!invitationData}
        onChange={formik.handleChange}
        value={formik.values.company}
      />
      <TextField
        className="input-2 w-full"
        error={!!(formik.touched.password && formik.errors.password)}
        helperText={formik.touched.password && formik.errors.password}
        label="Password"
        name="password"
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

      <div className="flex items-center justify-end gap-4 flex-wrap">
        <Button
          className="button-0 !w-auto !min-w-[200px]"
          type="submit"
        >
          Continue
        </Button>
      </div>
    </form>
  );
}

RegisterForm.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  invitationData: PropTypes.object,
  handleSumbit: PropTypes.func,
};
