import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useMutation } from '@apollo/client';
import { DELETE_WORKSPACE_MUTATION } from 'src/queries';
import NextLink from 'next/link';
import { Typography } from '@mui/material';
import { DocumentIcon } from 'src/elements/icons';

export function DeleteWorkspace(props) {
  const [deleteWorkspace] = useMutation(DELETE_WORKSPACE_MUTATION);
  const { code, onDelete, id } = props;

  const confirm = useCallback(() => {
    deleteWorkspace({ variables: { id } }).then(() => {
      onDelete();
    });
  }, []);

  return (
    <DeleteDialog
      onConfirm={confirm}
      title="Delete workspace"
      confirmValue={code}
      confirmationRequired
      message={(
        <div>
          <Typography
            className="!text-[14px] !normal-case !font-medium block items-center"
          >
            This operation will remove all data related to the current workspace, including databases, servers, rules, and users.
            <br />
            <br />
            <span>
              <strong>Please note:&nbsp;</strong>
              all VeilDB Agent information stored on your servers should be removed manually.
            </span>

            <br />

            <span className="inline-block">
              Follow
              <NextLink
                href="https://veildb.gitbook.io/"
                className="link-0 link-1 normal-case ml-3 mr-2 !inline-block with-document-icon"
                target="_blank"
              >
                <DocumentIcon />
                steps
              </NextLink>
              from documentation to remove VeilDB Agent from your servers
            </span>
          </Typography>
        </div>
      )}
    />
  );
}
DeleteWorkspace.propTypes = {
  code: PropTypes.string,
  id: PropTypes.string,
  onDelete: PropTypes.func,
};
