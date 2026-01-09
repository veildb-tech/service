import React from 'react';
import {
  Typography,
  List,
  ListItem,
  ListItemText,
  ListItemButton
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';

import { useRouter } from 'next/router';
import { DeleteAccount } from './danger-zone/delete-account';
import { LeaveWorkspace } from './danger-zone/leave-workspace';

export function AccountProfileDanger() {
  const router = useRouter();
  const auth = useAuth();
  const { user } = auth;

  const onDelete = () => {
    auth.signOut();
    router.push('/auth/login');
  };

  return (
    <div className="card">
      <div className="card-content flex flex-col gap-1 mb-5">
        <Typography
          className="mb-1"
          variant="h4"
        >
          Danger Zone!
        </Typography>
        {/* eslint-disable-next-line react/no-unescaped-entities */}
        <div className="sub-heading-0">Be careful with removing your account. This action can't be undone.</div>
      </div>

      <div className="card-content flex flex-col gap-7">
        {user.workspaces.length > 1 && (
          <List subheader={<Typography className="mb-5" variant="subtitle2">Your workspaces</Typography>}>
            {user.workspaces.map((workspace) => (
              <ListItem disablePadding>
                <ListItemButton>
                  <ListItemText primary={workspace.name} />
                  <LeaveWorkspace
                    onLeave={onDelete}
                    workspaceId={workspace.id}
                    workspaceName={workspace.name}
                  />
                </ListItemButton>
              </ListItem>
            ))}
          </List>
        )}

        <Typography variant="subtitle2">Delete account</Typography>
        <div className="card-content">
          <Typography>
            All information related to the current user will be removed permanently. You will be unsigned from all workspaces.
            If you are the workspace owner, you need to remove the workspace first.
          </Typography>
          <DeleteAccount
            onDelete={onDelete}
            id={user.id}
          />
        </div>
      </div>
    </div>
  );
}
