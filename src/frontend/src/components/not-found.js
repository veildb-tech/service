import React from 'react';
import NextLink from 'next/link';
import ArrowLeftIcon from '@heroicons/react/24/solid/ArrowLeftIcon';
import {
  Box, Button, Container, SvgIcon, Typography
} from '@mui/material';
import { IconDbVisor } from 'src/elements/icons';

function NotFound() {
  return (
    <Box
      component="main"
      sx={{
        alignItems: 'center',
        display: 'flex',
        flexGrow: 1,
        minHeight: '100%',
        minWidth: '100%',
        margin: 0
      }}
      className="gradient"
    >
      <Container maxWidth="md">
        <Box
          sx={{
            alignItems: 'center',
            display: 'flex',
            flexDirection: 'column',
          }}
        >
          <Box
            sx={{
              mt: 8,
              mb: 10,
              textAlign: 'center',
            }}
          >
            <IconDbVisor />
          </Box>
          <Typography
            align="center"
            variant="h3"
            className="text-dbm-color-primary-light !font-semibold !mb-7 !text-[64px]"
          >
            404: page not found
          </Typography>
          <Typography
            align="center"
            color="text.secondary"
            variant="body1"
            className="max-w-[490px] !font-medium"
          >
            You either tried some shady route or you came here by mistake.
            Whichever it is, try using the navigation
          </Typography>

          <Button
            component={NextLink}
            href="/"
            startIcon={(
              <SvgIcon fontSize="small">
                <ArrowLeftIcon />
              </SvgIcon>
            )}
            variant="contained"
            className="max-w-[334px] w-full !mt-10 !rounded-lg !py-[12px] !font-semibold"
          >
            Go back to dashboard
          </Button>
        </Box>
      </Container>
    </Box>
  );
}

export default NotFound;
