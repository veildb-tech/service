import React from 'react';
import { format, parseISO } from 'date-fns';
import PropTypes from 'prop-types';
import {
  Alert,
  Button,
  Divider,
  IconButton,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Tooltip,
  Typography
} from '@mui/material';
import { SeverityPill } from 'src/components/severity-pill';
import { SCHEDULE_BACKUP } from 'src/queries';
import { useMutation } from '@apollo/client';
import { usePermission } from 'src/hooks/use-permission';
import NextLink from 'next/link';
import { DumpLogs } from './dump/logs';

const statusMap = {
  scheduled: 'warning',
  ready: 'success',
  error: 'error',
  processing: 'pending',
  ready_with_error: 'warning',
  cancelled: 'pendingDark'
};

export function DatabaseDumps(props) {
  const { dumps, database, reload } = props;
  const [scheduleBackup, { error: scheduleError }] = useMutation(SCHEDULE_BACKUP);
  const { canSee } = usePermission();
  const canEdit = canSee('database.edit');

  const schedule = () => {
    scheduleBackup({
      variables: {
        db: database.id,
      },
    }).then(() => {
      reload(Date.now());
    });
  };

  if (database.status === 'pending') {
    return '';
  }

  if (!database.databaseRule) {
    return (
      <div className="card">
        <div className="card-content flex gap-6 mb-5 w-full">
          <Alert severity="warning">
            Please configure rules for the database to have the ability to schedule new dumps.
            &nbsp;
            <Typography variant="p" color="info.dark">
              <NextLink href="/rules/create" color="main">Add new rule</NextLink>
            </Typography>
          </Alert>
        </div>
      </div>
    );
  }

  return (
    <div className="card">
      <div className="card-content flex gap-6 mb-5">
        <div className="flex flex-col gap-1">
          <Typography
            className="mb-1"
            variant="h4"
          >
            Latest dumps
          </Typography>

          <div className="sub-heading-0">You could schedule new dump.</div>
        </div>

        { canEdit && (
        <Button
          className="button-5"
          variant="outlined"
          onClick={schedule}
        >
          Schedule
        </Button>
        )}
      </div>

      <Divider className="!mb-5" />

      <div className="card-content">
        { scheduleError && <Alert className="!mb-5 !p-0" severity="error">{ scheduleError.message }</Alert> }

        <Table className="table-0">
          <TableHead>
            <TableRow>
              <TableCell>Filename</TableCell>
              <TableCell>Created At</TableCell>
              <TableCell
                sortDirection="desc"
                className="!pl-[55px]"
              >
                Status
              </TableCell>
              <TableCell
                className="!pl-[44px]"
              >
                Actions
              </TableCell>
            </TableRow>
          </TableHead>

          <TableBody>
            {dumps.map((dump) => {
              const createdAt = format(parseISO(dump.node.created_at), 'dd/MM/yyyy');
              return (
                <TableRow
                  hover
                  key={dump.node.id}
                >
                  <TableCell>{dump.node.filename}</TableCell>
                  <TableCell>{createdAt}</TableCell>
                  <TableCell>
                    <span className={dump.node.status !== 'ready_with_error' && (
                      'min-w-[34px] inline-block'
                    )}
                    >
                      {
                        dump.node.status === 'ready_with_error' && (
                          <Tooltip
                            placement="left"
                            title="Dump is ready, but pay attention to error in log's"
                          >
                            <IconButton className="!p-0 !bg-inherit">
                              <Alert
                                className="
                                !p-0
                                !bg-inherit
                                "
                                severity="warning"
                              />
                            </IconButton>
                          </Tooltip>
                        )
                      }
                    </span>
                    <span className="inline-block h-[43px]">
                      <SeverityPill color={statusMap[dump.node.status]}>
                        {
                          dump.node.status === 'ready_with_error' ? (
                            'Warning!'
                          ) : (
                            dump.node.status
                          )
                        }
                      </SeverityPill>
                    </span>
                  </TableCell>
                  <TableCell>
                    <DumpLogs dumpUuid={dump.node.id} />
                  </TableCell>
                </TableRow>
              );
            })}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}

DatabaseDumps.prototype = {
  // eslint-disable-next-line react/forbid-prop-types
  dumps: PropTypes.array,
  // eslint-disable-next-line react/forbid-prop-types
  database: PropTypes.object,
  reload: PropTypes.func,
};
