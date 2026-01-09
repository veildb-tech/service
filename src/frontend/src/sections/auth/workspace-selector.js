import React from 'react';
import {
  Button, MenuItem, Select, Stack, Typography
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import { useWorkspace } from 'src/hooks/use-workspace';
import { SplashScreen } from 'src/layouts/splash-screen';

export function WorkspaceSelector() {
  const { setCurrentWorkspace } = useWorkspace();

  const { user } = useAuth();
  const changeWorkspace = (event) => {
    const workspaceCode = event.target.value;

    // TODO: replace it to server side to avoid security issues
    setCurrentWorkspace(workspaceCode);
  };

  if (!user) {
    return <SplashScreen />;
  }

  return (
    <div>
      <Stack
        spacing={1}
        sx={{ mb: 3 }}
      >
        <Typography variant="h4">Select you workspace</Typography>
      </Stack>

      <form noValidate>
        <Stack spacing={3}>
          <Select
            label="Workspace"
            fullWidth
            onChange={changeWorkspace}
            name="status"
            required
          >
            {user.workspaces
              && user.workspaces.map((workspace) => (
                <MenuItem
                  key={workspace.code}
                  value={workspace.code}
                >
                  {workspace.name}
                </MenuItem>
              ))}
          </Select>
        </Stack>
        <Button
          fullWidth
          size="large"
          sx={{ mt: 3 }}
          type="submit"
          variant="contained"
        >
          Continue
        </Button>
      </form>
    </div>
  );
}
