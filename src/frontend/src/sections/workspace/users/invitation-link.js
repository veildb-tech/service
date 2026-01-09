import PropTypes from 'prop-types';
import * as React from 'react';
import Dialog from '@mui/material/Dialog';
import CloseIcon from '@mui/icons-material/Close';
import {
  Typography, Button, Divider, Alert
} from '@mui/material';
import { REJECT_USER_INVITATION } from 'src/queries';
import { useMutation } from '@apollo/client';
import { format, parseISO } from 'date-fns';

export function InvitationLink(props) {
  const [open, setOpen] = React.useState(false);
  const [rejectInvitation, { loading, data: rejectedData }] = useMutation(
    REJECT_USER_INVITATION
  );
  const { invitation } = props;

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  const handleReject = () => {
    rejectInvitation({
      variables: {
        id: invitation.id
      }
    });
  };

  return (
    <>
      <Button
        onClick={handleClickOpen}
        className="link-0 link-1"
      >
        View
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
            <Typography variant="h4">Invitation link</Typography>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        { invitation.status === 'pending' && (
          <div className="dialog-0-content">
            <div>
              <Typography variant="p">
                Invitation email was sent to email
                &quot;
                {invitation.email}
                &quot;
              </Typography>
              <br />
              <Typography variant="subtitle1">You can copy invitation link below:</Typography>
            </div>
            <div className="mt-3">
              <Alert variant="outlined" severity="info" className="p-1 flex-row">
                <Typography variant="p">{invitation.url}</Typography>
              </Alert>
              <Typography variant="caption">
                Please note, this invitation link will expire at &nbsp;
                { format(parseISO(invitation.expiration_date), 'dd/MM/yyyy HH:mm:ss') }
              </Typography>
            </div>
          </div>
        )}
        { invitation.status === 'expired' && (
          <div className="dialog-0-content">
            <Alert variant="outlined" severity="error" sx={{ flexDirection: 'row' }}>
              <Typography variant="p">This invitation has expired</Typography>
            </Alert>
          </div>
        )}
        { (rejectedData && rejectedData.updateUserInvitation.userInvitation.status === 'canceled') && (
          <div className="dialog-0-content">
            <Alert variant="outlined" severity="error" sx={{ flexDirection: 'row' }}>
              <Typography variant="p">Invitation has been canceled</Typography>
            </Alert>
          </div>
        )}

        <Divider className="!my-6" />

        <div className="dialog-0-content flex items-center gap-3">
          <Button
            className="button-4 w-full"
            onClick={handleClose}
            disabled={loading}
          >
            Close
          </Button>
          {!rejectedData && (
            <Button
              className="w-full"
              variant="outlined"
              disabled={loading}
              color="error"
              onClick={handleReject}
            >
              Reject
            </Button>
          )}
        </div>
      </Dialog>
    </>
  );
}

InvitationLink.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  invitation: PropTypes.object,
};
