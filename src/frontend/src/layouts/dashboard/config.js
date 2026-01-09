import React from 'react';
import ChartBarIcon from '@heroicons/react/24/solid/ChartBarIcon';
import CircleStackIcon from '@heroicons/react/24/solid/CircleStackIcon';
import ServerIcon from '@heroicons/react/24/solid/ServerIcon';
import AdjustmentsHorizontalIcon from '@heroicons/react/24/solid/AdjustmentsHorizontalIcon';
import { SvgIcon } from '@mui/material';

export const navigation = [
  {
    title: 'Overview',
    path: '/',
    icon: (
      <SvgIcon fontSize="small">
        <ChartBarIcon />
      </SvgIcon>
    ),
  },
  {
    title: 'Databases',
    path: '/databases',
    permission: 'database.view',
    icon: (
      <SvgIcon fontSize="small">
        <CircleStackIcon />
      </SvgIcon>
    ),
  },
  {
    title: 'Rules',
    path: '/rules',
    permission: 'databaseRule.view',
    icon: (
      <SvgIcon fontSize="small">
        <AdjustmentsHorizontalIcon />
      </SvgIcon>
    ),
  },
  {
    title: 'Servers',
    path: '/servers',
    permission: 'server.view',
    icon: (
      <SvgIcon fontSize="small">
        <ServerIcon />
      </SvgIcon>
    ),
  },
];
