import React from 'react';
import {
  Avatar,
  Box,
  Typography
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';

export function AccountProfile() {
  const { user } = useAuth();

  return (
    <div className="card min-w-[300px]">
      <div className="card-content">
        <Box
          sx={{
            alignItems: 'center',
            display: 'flex',
            flexDirection: 'column',
          }}
        >
          <Avatar className="min-w-[80px] min-h-[80px] mb-4" />

          <Typography variant="h4">
            {`${user.firstname} ${user.lastname}`}
          </Typography>

          <div className="link-0 pointer-events-none">
            {user.email}
          </div>
        </Box>
      </div>
    </div>
  );
}
