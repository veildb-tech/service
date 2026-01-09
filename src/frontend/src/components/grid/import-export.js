import React from 'react';
import ArrowDownOnSquareIcon from '@heroicons/react/24/solid/ArrowDownOnSquareIcon';
import ArrowUpOnSquareIcon from '@heroicons/react/24/solid/ArrowUpOnSquareIcon';
import {
  Button, Stack, SvgIcon
} from '@mui/material';

export function ImportExport() {
  return (
    <Stack spacing={1}>
      <Stack
        alignItems="center"
        direction="row"
        spacing={1}
      >
        <Button
          color="inherit"
          startIcon={(
            <SvgIcon fontSize="small">
              <ArrowUpOnSquareIcon />
            </SvgIcon>
          )}
        >
          Import
        </Button>
        <Button
          color="inherit"
          startIcon={(
            <SvgIcon fontSize="small">
              <ArrowDownOnSquareIcon />
            </SvgIcon>
          )}
        >
          Export
        </Button>
      </Stack>
    </Stack>
  );
}
