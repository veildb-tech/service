'use client';

import { React, useEffect, useState } from 'react';
import {
  Alert,
  Badge, Button,
  CircularProgress,
  IconButton,
  Popover,
  SvgIcon,
  Tooltip, Typography
} from '@mui/material';
import { usePopover } from 'src/hooks/use-popover';
import BellIcon from '@heroicons/react/24/solid/BellIcon';
import { useQuery, useMutation } from '@apollo/client';
import NextLink from 'next/link';
import {
  CURRENT_WORKSPACE_NOTIFICATIONS_QUERY,
  UPDATE_NOTIFICATION,
  MARK_ALL_NOTIFICATIONS_AS_READ
} from 'src/queries';

export function WorkspaceNotifications() {
  const notificationPopover = usePopover();
  const [reload, setReload] = useState();

  const {
    data: notifications,
    error,
    loading,
    refetch
  } = useQuery(
    CURRENT_WORKSPACE_NOTIFICATIONS_QUERY,
    {
      variables: {
        itemsPerPage: 10
      }
    }
  );

  useEffect(() => {
    refetch();
  }, [refetch, reload]);

  const [updateNotification] = useMutation(UPDATE_NOTIFICATION);
  const [markAllAsReadMutation] = useMutation(MARK_ALL_NOTIFICATIONS_AS_READ);
  const notificationEmpty = !notifications || !notifications.notifications.collection.length;

  const markAsRead = (id) => {
    updateNotification({
      variables: {
        id,
        status: 0
      }
    }).then(() => setReload(Date.now()));
  };
  const markAllAsRead = () => {
    markAllAsReadMutation().then(() => setReload(Date.now()));
  };

  return (
    <>
      <Tooltip
        title="Notifications"
        ref={notificationPopover.anchorRef}
        onClick={notificationPopover.handleOpen}
      >
        <IconButton>
          <Badge
            color="success"
            variant={!notificationEmpty ? 'dot' : ''}
          >
            <SvgIcon fontSize="small">
              <BellIcon />
            </SvgIcon>
          </Badge>
        </IconButton>
      </Tooltip>
      <Popover
        anchorOrigin={{
          horizontal: 'left',
          vertical: 'bottom',
        }}
        anchorEl={notificationPopover.anchorRef.current}
        open={notificationPopover.open}
        PaperProps={{ sx: { maxWidth: 400, p: 2 } }}
        onClose={notificationPopover.handleClose}
      >
        {error && <Alert severity="error">Something went wrong fetching notifications.!</Alert>}
        {loading && <CircularProgress />}
        {(!notifications || !notifications.notifications.collection.length) && (
          <Alert severity="info">There are no new notifications</Alert>
        )}
        {!notificationEmpty && (
          <Button
            size="small"
            onClick={() => markAllAsRead()}
          >
            Mark all as read
          </Button>
        )}
        {notifications
          && notifications.notifications.collection.map((notification) => (
            <div key={notification.id}>
              <div className="relative">
                {notification.status === 1 && (
                  <Badge
                    color="primary"
                    sx={{
                      top: 65,
                      left: 28,
                      margin: 0,
                      padding: 0,
                      cursor: 'pointer',
                      position: 'absolute'
                    }}
                    onClick={() => markAsRead(notification.id)}
                    title="Mark as read"
                    size="large"
                    variant="dot"
                  />
                )}
                <Alert
                  key={notification.id}
                  severity={notification.level}
                >
                  {notification.message}
                  &nbsp;
                  {notification.externalUrl && (
                    <Typography variant="p" color="info.dark">
                      <NextLink href={notification.externalUrl} color="main" target="_blank">Learn more</NextLink>
                    </Typography>
                  )}
                </Alert>
              </div>
            </div>
          ))}
      </Popover>
    </>
  );
}
