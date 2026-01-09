import PropTypes from 'prop-types';

import * as React from 'react';
import {
  Dialog,
  Button,
  Typography,
  TextField,
  Divider
} from '@mui/material';
import { useState } from 'react';
import { UPDATE_USER_GROUPS } from 'src/queries';
import { useMutation } from '@apollo/client';
import CloseIcon from '@mui/icons-material/Close';
import Multiselect1 from 'src/elements/multiselect1';
import { EditIcon } from 'src/elements/icons';

export function EditUser(props) {
  const { user, groups: allGroups } = props;

  const [open, setOpen] = useState(false);
  const [groups, setGroups] = useState(user.groups.collection.map((group) => group.id));
  const [updateUserGroups] = useMutation(UPDATE_USER_GROUPS);

  const changeGroup = (value) => {
    setGroups(
      // On autofill if we get a stringified value.
      typeof value === 'string' ? value.split(',') : value
    );
  };

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  const handleSave = () => {
    updateUserGroups({
      variables: {
        id: user.id,
        groups
      }
    }).then(() => handleClose());
  };

  return (
    <>
      <Button
        onClick={handleClickOpen}
        className="link-0 link-1"
      >
        <EditIcon />
        Edit
      </Button>

      <Dialog
        className="dialog-0"
        open={open}
        onClose={handleClose}
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >
        <div className="dialog-0-content mb-6">
          <div className="flex justify-between">
            <Typography variant="h4">Edit User</Typography>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        <div className="dialog-0-content grid grid-cols-2 gap-3">
          <TextField
            type="text"
            className="input-0 w-full"
            name="firstname"
            disabled
            value={user.firstname}
            label="First Name"
            placeholder="Type First Name"
          />

          <TextField
            type="text"
            className="input-0 w-full"
            name="lastname"
            disabled
            value={user.lastname}
            label="Last Name"
            placeholder="Type Last Name"
          />

          <TextField
            type="text"
            className="input-0 w-full"
            disabled
            name="email"
            value={user.email}
            label="Email"
            placeholder="Type Email"
          />

          <Multiselect1
            options={allGroups}
            selectedOptions={groups}
            setSelectedOptions={changeGroup}
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
            onClick={handleClose}
          >
            Close
          </Button>

          <Button
            className="button-0 w-full"
            onClick={handleSave}
          >
            Save
          </Button>
        </div>
      </Dialog>
    </>
  );
}

EditUser.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  user: PropTypes.object
};
