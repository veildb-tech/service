import React, { useCallback, useState } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_GROUP_MUTATION } from 'src/queries';

export function DeleteGroup(props) {
  const [error, setError] = useState(false);
  const [deleteGroup] = useMutation(DELETE_GROUP_MUTATION);
  const { uuid, reload } = props;

  const confirm = useCallback(() => {
    deleteGroup({ variables: { id: uuid } }).then(() => {
      reload(Date.now());
    }).catch((exception) => {
      setError(exception.message);
    });
  }, []);

  return (
    <DeleteDialog
      onConfirm={confirm}
      error={error}
      message={'After deleting of group all information will be lost.'}
    />
  );
}
DeleteGroup.propTypes = {
  uuid: PropTypes.string,
  reload: PropTypes.func,
};
