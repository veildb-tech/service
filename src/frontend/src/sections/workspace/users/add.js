import * as React from 'react';
import { useState } from 'react';
import PropTypes from 'prop-types';
import {
  Dialog,
  Button,
  TextField,
  CircularProgress,
  Alert, Typography, Divider
} from '@mui/material';
import { useMutation } from '@apollo/client';
import { CREATE_USER_INVITATION_MUTATION } from 'src/queries';
import CloseIcon from '@mui/icons-material/Close';
import Multiselect1 from 'src/elements/multiselect1';
import * as Yup from 'yup';
import { useFormik } from 'formik';

export function WorkspaceUserAdd(props) {
  const { groups: allGroups, reload } = props;
  const [openAddDialog, setOpenAddDialog] = useState(false);
  const [groups, setGroups] = useState([]);

  const [createUser, { loading, error }] = useMutation(
    CREATE_USER_INVITATION_MUTATION
  );

  const handleClose = () => {
    // eslint-disable-next-line no-use-before-define
    formik.resetForm();
    setOpenAddDialog(false);
  };

  const onAdd = (values) => {
    createUser({
      variables: {
        email: values.email,
        groups
      }
    }).then(() => {
      handleClose();
      reload(Date.now());
    });
  };

  const formik = useFormik({
    initialValues: {
      email: '',
      groups: []
    },
    validationSchema: Yup.object({
      email: Yup.string().email('Must be a valid email').max(255).required(),
      groups: Yup.array().required().min(1, 'Select at least 1 group')
    }),
    onSubmit: onAdd,
  });

  const handleClickOpen = () => {
    setOpenAddDialog(true);
  };

  const changeGroup = (value) => {
    const groups = typeof value === 'string' ? value.split(',') : value;
    setGroups(
      // On autofill if we get a stringifies value.
      groups
    );

    formik.setFieldValue('groups', groups);
  };

  return (
    <>
      <Button
        className="button-0"
        onClick={handleClickOpen}
      >
        Add User
      </Button>

      <Dialog
        className="dialog-0"
        open={openAddDialog}
        onClose={handleClose}
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >

        <form
          noValidate
          onSubmit={formik.handleSubmit}
        >
          <div className="dialog-0-content mb-6">
            <div className="flex justify-between">
              <Typography variant="h4">Add new user</Typography>

              <Button
                onClick={handleClose}
                className="button-3"
              >
                <CloseIcon />
              </Button>
            </div>
          </div>
          {error && <Alert severity="error">{error.message}</Alert>}

          <div className="dialog-0-content flex gap-3">
            <TextField
              className="input-0 w-full"
              placeholder="Email"
              label="Email"
              name="email"
              disabled={loading}
              required
              error={!!(formik.touched.email && formik.errors.email)}
              helperText={formik.touched.email && formik.errors.email}
              onChange={formik.handleChange}
              value={formik.values.email}
            />

            <Multiselect1
              options={allGroups}
              selectedOptions={groups}
              setSelectedOptions={changeGroup}
              error={!!(formik.touched.groups && formik.errors.groups)}
              helperText={formik.touched.groups && formik.errors.groups}
              onChange={formik.handleChange}
              value={formik.values.groups}
              valueKey="id"
              labelKey="name"
              className="w-full"
              topLabel="Group"
              placeholder="Select Group"
            />
          </div>

          <Divider className="!my-6" />

          <div className="dialog-0-content flex items-center gap-3">
            <Button
              className="button-4 w-full"
              disabled={loading}
              onClick={handleClose}
            >
              Cancel
            </Button>

            <Button
              className="button-0 w-full"
              type="submit"
              disabled={loading}
            >
              {loading && <CircularProgress className="mr-4" size={20} color="info" />}
              Add user
            </Button>
          </div>
        </form>
      </Dialog>
    </>
  );
}

WorkspaceUserAdd.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  groups: PropTypes.array,
  reload: PropTypes.func
};
