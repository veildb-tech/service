import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_SERVER } from 'src/queries';
import { Typography } from '@mui/material';

export function DeleteServer(props) {
  const [deleteServer] = useMutation(DELETE_SERVER);
  const { uuid, reload } = props;

  const confirm = useCallback(() => {
    deleteServer({ variables: { id: uuid } }).then(() => {
      reload(Date.now());
    });
  }, []);

  const deleteMessage = (
    <Typography>
      <b>Are you sure you want to remove this server? All databases and rules related to this server will be removed.</b>
      <br />
      Please note that this action does not remove `dbvisor-agent` tool. You need to remove it manually from your server.
    </Typography>
  );
  return (
    <DeleteDialog
      onConfirm={confirm}
      message={deleteMessage}
    />
  );
}
DeleteServer.propTypes = {
  uuid: PropTypes.string,
  reload: PropTypes.func,
};
