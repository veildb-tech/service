import PropTypes from 'prop-types';
import * as React from 'react';
import Dialog from '@mui/material/Dialog';
import CloseIcon from '@mui/icons-material/Close';
import {
  Typography,
  Button,
  Divider,
  Alert,
  Input
} from '@mui/material';

export function DeleteDialog(props) {
  const [open, setOpen] = React.useState(false);
  const [confirmationError, setConfirmationError] = React.useState(false);
  const [confirmation, setConfirmation] = React.useState('');
  const {
    onConfirm,
    title,
    message,
    error,
    confirmationRequired,
    confirmValue
  } = props;
  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  const submit = (event) => {
    let canProceed = true;
    if (confirmationRequired && confirmValue !== confirmation) {
      canProceed = false;
      setConfirmationError(true);
    }

    if (canProceed) {
      onConfirm(event);
    }
  };

  return (
    <>
      <Button
        className="button-2"
        onClick={handleClickOpen}
      >
        { title ?? 'Delete'}
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
            <Typography variant="h4">Are you sure?</Typography>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        <div className="dialog-0-content">
          {message}
        </div>

        { error && (
          <div className="mt-3">
            <Alert severity="error">{error}</Alert>
          </div>
        )}

        <Divider className="!my-6" />

        { confirmationRequired && (
          <div className="dialog-0-content mb-5">
            <Typography variant="caption">
              To confirm removing enter:
              <b>
                <i>
                  {confirmValue}
                </i>
              </b>
            </Typography>
            <Input
              className="input-3 w-150 p-1 ml-5"
              placeholder="Verify your action"
              error={confirmationError}
              required
              onChange={(event) => setConfirmation(event.target.value)}
            />
          </div>
        )}

        <div className="dialog-0-content justify-end flex items-center gap-3">
          <Button
            className="button-4"
            onClick={handleClose}
          >
            Cancel
          </Button>

          <Button
            className="button-0"
            disabled={confirmationRequired && confirmValue !== confirmation}
            onClick={submit}
          >
            Delete
          </Button>
        </div>
      </Dialog>
    </>
  );
}

DeleteDialog.propTypes = {
  onConfirm: PropTypes.func,
  message: PropTypes.any,
  title: PropTypes.string,
  confirmValue: PropTypes.string,
  confirmationRequired: PropTypes.bool
};
