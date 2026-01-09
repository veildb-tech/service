import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_DATABASE } from 'src/queries';
import { Typography } from '@mui/material';

export function DeleteDatabase(props) {
  const [deleteDatabase] = useMutation(DELETE_DATABASE);
  const { uuid, reload } = props;

  const confirm = useCallback(() => {
    deleteDatabase({ variables: { id: uuid } }).then(() => {
      reload(Date.now());
    });
  }, []);

  const deleteMessage = (
    <Typography>
      <b>After deleting of database all information will be lost.</b>
      <br />
      Please note that this action removes the database only from the service side.
      <br />
      It should be done manually if you need to remove the database from the server. Contact support for more information.
    </Typography>
  );
  return (
    <DeleteDialog
      onConfirm={confirm}
      message={deleteMessage}
    />
  );
}
DeleteDatabase.propTypes = {
  uuid: PropTypes.string,
  reload: PropTypes.func,
};
