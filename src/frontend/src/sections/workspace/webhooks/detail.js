import PropTypes from 'prop-types';
import React, { useState } from 'react';
import {
  Button,
  Dialog,
  TextField,
  InputLabel,
  MenuItem,
  Select,
  Typography,
  FormControl
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';
import { useMutation } from '@apollo/client';
import { UPDATE_WEBHOOK, CREATE_WEBHOOK } from 'src/queries';
import { DatabaseSelector } from 'src/components/database/selector';
import CloseIcon from '@mui/icons-material/Close';
import { EditIcon } from 'src/elements/icons';

export function WebhookDetail(props) {
  const { data, reload } = props;
  const editMode = !!data;

  const [title, setTitle] = useState(data ? data.title : '');
  const [status, setStatus] = useState(data ? data.status : 'enabled');
  const [operation, setOperation] = useState(data ? data.operation : 'create_db_dump');
  const [domains, setDomains] = useState(data ? data.domains : '');
  const [selectedDb, setSelectedDb] = useState(data ? data.database.id : '');

  const { webhookOperations, webhookStatuses } = useConfig();

  const [openAddDialog, setOpenAddDialog] = useState(false);

  const query = editMode ? UPDATE_WEBHOOK : CREATE_WEBHOOK;
  const [saveWebhook] = useMutation(query);

  const handleClose = () => {
    setOpenAddDialog(false);
  };

  const handleClickOpen = () => {
    setOpenAddDialog(true);
  };

  const onSave = (event) => {
    event.preventDefault();
    const variables = {
      title,
      status,
      operation,
      domains,
      database: selectedDb,
    };

    if (editMode) {
      variables.id = data.id;
    }

    saveWebhook({
      variables,
    }).then(() => {
      reload(Date.now());
      handleClose();
    });
  };

  const handleDatabaseChange = (event) => {
    setSelectedDb(event.target.value);
  };

  return (
    <>
      <Button
        className={editMode ? 'link-0 link-1' : 'button-0'}
        variant={editMode ? 'small' : 'contained'}
        onClick={handleClickOpen}
      >
        {editMode && <EditIcon />}
        {editMode ? 'Edit' : 'Add Webhook'}
      </Button>

      <Dialog
        className="dialog-0"
        classes={{
          paper: '!max-w-[616px]'
        }}
        open={openAddDialog}
        onClose={handleClose}
        maxWidth="xl"
        fullWidth
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >
        <div className="dialog-0-content mb-6">
          <div className="flex justify-between">
            <Typography variant="h4">{editMode ? 'Edit Webhook' : 'Add new Webhook'}</Typography>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        <div className="dialog-0-content mb-6">
          <div className="flex items-center gap-6 mb-3">
            <TextField
              className="input-0 w-full"
              placeholder="Type Title"
              label="Title"
              name="title"
              required
              onChange={(event) => setTitle(event.target.value)}
              value={title}
            />

            <TextField
              className="input-0 w-full"
              placeholder="Type Domains"
              label="Domains"
              name="domains"
              required
              onChange={(event) => setDomains(event.target.value)}
              value={domains}
            />
          </div>

          <div className="flex items-center gap-6 mb-3">
            <FormControl
              variant="standard"
              className="select-0 w-full"
            >
              <InputLabel>Status</InputLabel>

              <Select
                className="w-full"
                name="status"
                labelId="webhook-status-label"
                onChange={(event) => setStatus(event.target.value)}
                value={status}
              >
                {webhookStatuses.map((status) => (
                  <MenuItem
                    key={`webhook-status_${status.value}`}
                    value={status.value}
                  >
                    {status.label}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>

            <FormControl
              variant="standard"
              className="select-0 w-full"
            >
              <InputLabel>Operation</InputLabel>

              <Select
                className="w-full"
                labelId="webhook-operation-label"
                name="operation"
                onChange={(event) => setOperation(event.target.value)}
                value={operation}
                disabled
              >
                {webhookOperations.map((operation) => (
                  <MenuItem
                    key={`webhook-operation${status.value}`}
                    value={operation.value}
                  >
                    {operation.label}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </div>

          <div className="flex items-center gap-6">
            <DatabaseSelector
              value={selectedDb}
              onChange={handleDatabaseChange}
            />

            {data && (
              <TextField
                className="input-0 w-full"
                placeholder="Type Url"
                label="Url"
                name="url"
                disabled
                value={data.url}
              />
            )}
          </div>
        </div>

        <div className="dialog-0-content flex items-center gap-3">
          <Button
            className="button-4 w-full"
            onClick={handleClose}
          >
            Cancel
          </Button>

          <Button
            className="button-0 w-full"
            onClick={onSave}
          >
            Save
          </Button>
        </div>
      </Dialog>
    </>
  );
}

WebhookDetail.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
  reload: PropTypes.func,
};
