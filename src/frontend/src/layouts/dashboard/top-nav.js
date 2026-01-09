import React from 'react';
import {
  Avatar,
  Tooltip
} from '@mui/material';
import { usePopover } from 'src/hooks/use-popover';
import { useAuth } from 'src/hooks/use-auth';
import { usePermission } from 'src/hooks/use-permission';
import { WorkspaceNotifications } from 'src/components/notifications';
import { AccountPopover } from './account-popover';
import { ContactNav } from './contact-nav';

function stringToColor(string) {
  let hash = 0;
  let i;

  /* eslint-disable no-bitwise */
  for (i = 0; i < string.length; i += 1) {
    hash = string.charCodeAt(i) + ((hash << 5) - hash);
  }

  let color = '#';

  for (i = 0; i < 3; i += 1) {
    const value = (hash >> (i * 8)) & 0xff;
    color += `00${value.toString(16)}`.slice(-2);
  }
  /* eslint-enable no-bitwise */

  return color;
}

function stringAvatar(fullName) {
  return {
    sx: {
      bgcolor: stringToColor(fullName),
      cursor: 'pointer'
    },
    children: `${fullName.split(' ')[0][0]}${fullName.split(' ')[1][0]}`,
  };
}

export function TopNav() {
  const accountPopover = usePopover();
  const { user } = useAuth();
  const fullName = `${user.firstname} ${user.lastname}`;
  const { isAdmin } = usePermission();

  return (
    <header className="
      flex gap-3
      justify-end
      top-0
      z-40
      py-5
      px-10
      mb-7
      border-b
      border-dbm-color-6"
    >
      <ContactNav />

      { isAdmin() && <WorkspaceNotifications />}

      <Tooltip
        title={`${fullName} personal menu`}
      >
        <Avatar
          onClick={accountPopover.handleOpen}
          ref={accountPopover.anchorRef}
          sx={{
            height: 40,
            width: 40,
          }}
          {...stringAvatar(fullName)}
        />
      </Tooltip>

      <AccountPopover
        anchorEl={accountPopover.anchorRef.current}
        open={accountPopover.open}
        onClose={accountPopover.handleClose}
      />
    </header>
  );
}
