import * as React from 'react';
import { useMutation } from '@apollo/client';
import Dialog from '@mui/material/Dialog';
import CloseIcon from '@mui/icons-material/Close';
import {
  Typography,
  Button,
  Divider,
  Alert,
} from '@mui/material';
import { LEAVE_WORKSPACE_MUTATION } from 'src/queries';

export function LeaveWorkspace(props) {
  const [open, setOpen] = React.useState(false);
  const [leaveWorkspace] = useMutation(LEAVE_WORKSPACE_MUTATION);
  const {
    workspaceName,
    workspaceId,
    error,
    onLeave
  } = props;

  const handleClose = () => {
    setOpen(false);
  };

  const submit = () => {
    leaveWorkspace({
      variables: {
        workspaceId
      }
    }).then(() => {
      handleClose();
      onLeave();
    });
  };

  return (
    <>
      <Button className="button-3" onClick={() => setOpen(true)}>Leave</Button>
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
          {`Are you sure you want to leave workspace ${workspaceName}?`}
          <br />
          {'You will be logged out after this action'}
        </div>

        { error && (
          <div className="mt-3">
            <Alert severity="error">{error}</Alert>
          </div>
        )}

        <Divider className="!my-6" />

        <div className="dialog-0-content justify-end flex items-center gap-3">
          <Button
            className="button-4"
            onClick={handleClose}
          >
            Cancel
          </Button>

          <Button
            className="button-0"
            onClick={submit}
          >
            Leave
          </Button>
        </div>
      </Dialog>
    </>
  );
}

LeaveWorkspace.propTypes = {
};
