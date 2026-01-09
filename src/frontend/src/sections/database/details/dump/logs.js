import PropTypes from 'prop-types';
import { format, parseISO } from 'date-fns';
import * as React from 'react';
import { SeverityPill } from 'src/components/severity-pill';
import { useQuery } from '@apollo/client';
import { useConfig } from 'src/contexts/config-context';
import {
  CircularProgress,
  DialogContent,
  Dialog,
  Button,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Alert,
  Typography, TableContainer
} from '@mui/material';
import { GET_DUMP } from 'src/queries';
import CloseIcon from '@mui/icons-material/Close';

const statusMap = {
  processing: 'pending',
  success: 'success',
  error: 'error',
};

export function DumpLogs(props) {
  const [open, setOpen] = React.useState(false);
  const { dumpLogsStatuses } = useConfig();
  const { dumpUuid } = props;
  const { loading, error, data } = useQuery(GET_DUMP, {
    variables: { id: dumpUuid },
  });

  const getStatusLabel = (status) => {
    const option = dumpLogsStatuses.find((option) => option.value === status);
    return option ? option.label : status;
  };

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  let duration = 0;
  if (data && data.databaseDump.databaseDumpLogs.length) {
    const lastLogTime = parseISO(data.databaseDump.databaseDumpLogs[data.databaseDump.databaseDumpLogs.length - 1].createdAt).getTime();
    const firstLogTime = parseISO(data.databaseDump.databaseDumpLogs[0].createdAt).getTime();
    const durationDate = new Date(0);
    durationDate.setMilliseconds(lastLogTime - firstLogTime);
    duration = durationDate.toISOString().substring(11, 19);
  }

  return (
    <>
      <Button
        onClick={handleClickOpen}
        className="link-0 link-1"
      >
        View Logs
      </Button>

      <Dialog
        className="dialog-0"
        maxWidth="lg"
        open={open}
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >
        <div className="dialog-0-content mb-6">
          <div className="flex justify-between">
            <div>
              <Typography variant="h4">Dump logs</Typography>
              <Typography variant="heading">
                Duration:&nbsp;
                {duration}
              </Typography>
            </div>

            <Button
              onClick={handleClose}
              className="button-3"
            >
              <CloseIcon />
            </Button>
          </div>
        </div>

        <DialogContent sx={{ width: '100%', overflow: 'hidden' }}>
          {loading && <CircularProgress />}
          {error && <Alert severity="error">{error}</Alert>}
          {data && (
            <TableContainer sx={{ maxHeight: 440 }}>
              <Table stickyHeader className="table-0">
                <TableHead>
                  <TableRow>
                    <TableCell>Message</TableCell>
                    <TableCell sortDirection="desc">Status</TableCell>
                    <TableCell>Created At</TableCell>
                  </TableRow>
                </TableHead>

                <TableBody>
                  {data.databaseDump.databaseDumpLogs.map((log) => {
                    const createdAt = format(parseISO(log.createdAt), 'dd/MM/yyyy HH:mm:ss');
                    return (
                      <TableRow
                        hover
                        key={log.id}
                      >
                        <TableCell>{log.message}</TableCell>
                        <TableCell>
                          <SeverityPill color={statusMap[log.status]}>
                            {getStatusLabel(log.status)}
                          </SeverityPill>
                        </TableCell>
                        <TableCell>{createdAt}</TableCell>
                      </TableRow>
                    );
                  })}
                </TableBody>
              </Table>
            </TableContainer>
          )}
        </DialogContent>

        <div className="dialog-0-content">
          <Button
            className="button-4 w-full"
            onClick={handleClose}
          >
            Close
          </Button>
        </div>
      </Dialog>
    </>
  );
}

DumpLogs.propTypes = {
  dumpUuid: PropTypes.string,
};
