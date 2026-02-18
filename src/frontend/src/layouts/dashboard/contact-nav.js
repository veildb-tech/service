'use client';

import React from 'react';
import {
  Typography
} from '@mui/material';
import NextLink from 'next/link';

export function ContactNav() {
  return (
    <div className="mt-2">
      <Typography
        variant="p"
        className="text-dbm-color-3 text-base font-semibold p-2 pb-0"
      >
        <NextLink target="_blank" href="https://dbvisor.gitbook.io/">Documentation</NextLink>
      </Typography>

      <Typography
        variant="p"
        className="text-dbm-color-3 text-base font-semibold p-2 pt-0"
      >
        <NextLink href="/contact">Need help?</NextLink>
      </Typography>
    </div>
  );
}

ContactNav.propTypes = {
};
