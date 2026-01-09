import PropTypes from 'prop-types';
import React, { useState } from 'react';
import {
  Button,
  Dialog,
  FormControl,
  InputLabel,
  TextField,
  MenuItem,
  Select,
  CircularProgress,
  Alert, Typography,
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';
import { useMutation } from '@apollo/client';
import { CREATE_GROUP_MUTATION, UPDATE_GROUP_MUTATION } from 'src/queries';
import CloseIcon from '@mui/icons-material/Close';
import { EditIcon } from 'src/elements/icons';
import GroupDatabases from './databases';

export function WorkspaceGroupDetail(props) {
  const { data, reload } = props;
  const editMode = !!data;

  const [openAddDialog, setOpenAddDialog] = useState(false);
  const { workspaceGroupRoles } = useConfig();

  const [name, setName] = useState(data ? data.name : '');
  const [permission, setPermission] = useState(data ? data.permission : 3);

  let userGroupDatabases = [];
  if (data && data.databases) {
    userGroupDatabases = data.databases.collection.map((database) => database.id);
  }
  const [selectedDatabases, setSelectedDatabases] = useState(userGroupDatabases);
  const query = editMode ? UPDATE_GROUP_MUTATION : CREATE_GROUP_MUTATION;
  const [saveGroup, { loading, error }] = useMutation(query);

  const handleClose = () => {
    setOpenAddDialog(false);
  };

  const handleClickOpen = () => {
    setOpenAddDialog(true);
  };

  const onSave = (event) => {
    event.preventDefault();
    const variables = {
      name,
      permission,
      databases: selectedDatabases,
    };

    if (editMode) {
      variables.id = data.id;
    }

    saveGroup({
      variables,
    }).then(() => {
      reload(Date.now());
      handleClose();
    });
  };

  return (
    <>
      <Button
        onClick={handleClickOpen}
        className={editMode ? 'link-0 link-1' : 'button-0'}
      >
        {editMode && <EditIcon />}
        {editMode ? 'Edit' : 'Add Group'}
      </Button>

      <Dialog
        className="dialog-0"
        open={openAddDialog}
        onClose={handleClose}
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >
        <div className="dialog-0-content mb-6">
          <div className="flex justify-between">
            <Typography variant="h4">{editMode ? 'Edit group' : 'Add new group'}</Typography>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        {error && <Alert severity="error">{error.message}</Alert>}

        <div className="dialog-0-content flex items-center gap-3 mb-9">
          <TextField
            className="input-0 w-full"
            name="name"
            disabled={loading}
            required
            onChange={(event) => setName(event.target.value)}
            value={name}
            label="Group Name"
            placeholder="Type Group Name"
          />

          <FormControl
            variant="standard"
            className="select-0 w-full"
          >
            <InputLabel>Choose permission type</InputLabel>

            <Select
              className="w-full"
              disabled={!workspaceGroupRoles || loading}
              onChange={(event) => setPermission(event.target.value)}
              value={permission}
            >
              {workspaceGroupRoles
                  && workspaceGroupRoles.map((role) => (
                    <MenuItem
                      disabled={role.value === 1}
                      key={`${role.value}-role`}
                      value={role.value}
                    >
                      {role.label}
                    </MenuItem>
                  ))}
            </Select>
          </FormControl>
        </div>

        <div className="dialog-0-content">
          <GroupDatabases
            selectedDatabases={selectedDatabases}
            updateSelectedDatabases={setSelectedDatabases}
          />
        </div>

        <div className="dialog-0-content flex items-center gap-6">
          {loading && <CircularProgress />}

          <Button
            onClick={handleClose}
            className="button-4 w-full"
          >
            Cancel
          </Button>

          <Button
            onClick={onSave}
            className="button-0 w-full"
            autoFocus
          >
            {editMode ? 'Save edits' : 'Save'}
          </Button>
        </div>
      </Dialog>
    </>
  );
}

WorkspaceGroupDetail.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
  reload: PropTypes.func,
};
