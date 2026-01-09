import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_WEBHOOK } from 'src/queries';

export function DeleteWebhook(props) {
  const [deleteWebhook] = useMutation(DELETE_WEBHOOK);
  const { uuid, reload } = props;

  const confirm = useCallback(() => {
    deleteWebhook({ variables: { id: uuid } }).then(() => {
      reload(Date.now());
    });
  }, []);

  return (
    <DeleteDialog
      onConfirm={confirm}
      message="After deleting of webhook all information will be lost."
    />
  );
}
DeleteWebhook.propTypes = {
  uuid: PropTypes.string,
  reload: PropTypes.func,
};
