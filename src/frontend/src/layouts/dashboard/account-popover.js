'use client';

import React, { useCallback } from 'react';
import { useRouter } from 'next/router';
import PropTypes from 'prop-types';
import {
  Button,
  Divider,
  Popover,
  Typography
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import { useUrl } from 'src/hooks/use-url';
import { usePermission } from 'src/hooks/use-permission';
import NextLink from 'next/link';

export function AccountPopover(props) {
  const { anchorEl, onClose, open } = props;
  const router = useRouter();
  const auth = useAuth();
  const url = useUrl();
  const { canSee } = usePermission();

  const handleSignOut = useCallback(() => {
    onClose?.();
    auth.signOut();
    router.push('/auth/login');
  }, [onClose, auth, router]);

  return (
    <Popover
      anchorEl={anchorEl}
      anchorOrigin={{
        horizontal: 'left',
        vertical: 'bottom',
      }}
      transformOrigin={{
        vertical: -10,
        horizontal: 230
      }}
      onClose={onClose}
      open={open}
      PaperProps={{ sx: { width: 250 } }}
    >
      <div
        className="flex flex-col p-4"
      >
        <Typography
          variant="h4"
          className="!mb-2"
        >
          Account
        </Typography>

        <Typography
          variant="h7"
          className="text-dbm-color-3 text-base font-semibold"
        >
          {`${auth.user.firstname} ${auth.user.lastname}`}
        </Typography>
      </div>

      <Divider />

      <div className="flex flex-col gap-3 p-4">
        <NextLink
          href="/account"
          className="button-0 !bg-dbm-color-primary-light !text-dbm-color-white hover:!bg-dbm-color-primary"
          onClick={() => onClose()}
        >
          Manage Account
        </NextLink>

        {canSee('workspace.edit') && (
          <NextLink
            href={url.getUrl('manage')}
            className="button-0 !bg-dbm-color-primary-light !text-dbm-color-white hover:!bg-dbm-color-primary"
            onClick={() => onClose()}
          >
            Manage Workspace
          </NextLink>
        )}
      </div>

      <Divider />

      <div className="p-4">
        <Button
          onClick={handleSignOut}
          className="button-4 w-full"
        >
          Sign out
        </Button>
      </div>
    </Popover>
  );
}

AccountPopover.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  anchorEl: PropTypes.any,
  onClose: PropTypes.func,
  open: PropTypes.bool.isRequired,
};
