import React from 'react';
import { Box, CircularProgress } from '@mui/material';

export function SplashScreen() {
  return (
    <Box
      display="flex"
      justifyContent="center"
      alignItems="center"
      minHeight="100vh"
    >
      <CircularProgress />
    </Box>
  );
}
