import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_USER_MUTATION } from 'src/queries';

export function DeleteUser(props) {
  const [deleteUser] = useMutation(DELETE_USER_MUTATION);
  const { uuid, reload } = props;

  const confirm = useCallback(() => {
    deleteUser({ variables: { id: uuid } }).then(() => {
      reload(Date.now());
    });
  }, []);

  return (
    <DeleteDialog
      onConfirm={confirm}
      message="After deleting of user all information will be lost. This user lost access to all data and backups"
    />
  );
}
DeleteUser.propTypes = {
  uuid: PropTypes.string,
  reload: PropTypes.func,
};
