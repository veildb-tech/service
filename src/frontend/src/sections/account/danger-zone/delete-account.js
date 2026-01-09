import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_CURRENT_USER } from 'src/queries';
import { Typography } from '@mui/material';

export function DeleteAccount(props) {
  const [deleteAccount] = useMutation(DELETE_CURRENT_USER);
  const { onDelete, id } = props;

  const confirm = useCallback(() => {
    deleteAccount({ variables: { id } }).then(() => {
      onDelete();
    });
  }, []);

  return (
    <DeleteDialog
      onConfirm={confirm}
      title="Delete current account"
      confirmValue={'delete'}
      confirmationRequired
      message={(
        <div>
          <Typography
            className="!text-[14px] !normal-case !font-medium block items-center"
          >
            This operation will remove all data related to your account. You will lose access to all databases and servers.
            <br />
            If you are the workspace owner, you need to remove the workspace first.
          </Typography>
        </div>
      )}
    />
  );
}
DeleteAccount.propTypes = {
  id: PropTypes.string,
  onDelete: PropTypes.func,
};
