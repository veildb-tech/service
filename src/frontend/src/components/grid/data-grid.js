import React from 'react';
import PropTypes from 'prop-types';
import {
  Alert,
  CircularProgress,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TablePagination,
  TableRow,
  TableFooter
} from '@mui/material';
import { SeverityPill } from 'src/components/severity-pill';
import { format, parseISO } from 'date-fns';

let statusMap = {
  disabled: 'error',
  enabled: 'success',
};

export function DataGrid(props) {
  const {
    onRowsPerPageChange,
    onPageChange,
    rows,
    getActions,
    columns,
    page,
    itemsPerPage,
    totalCount,
    error,
    loading,
    customStatusMap
  } = props;

  statusMap = {
    ...statusMap,
    ...customStatusMap
  };

  const getValue = (item, column) => {
    if (column.getValue) {
      return column.getValue(item, column);
    }
    return item[column.code];
  };

  return (
    <Table className="table-0 bg-dbm-color-white">
      <TableHead>
        <TableRow>
          {columns.map((column) => <TableCell key={column.code}>{column.title}</TableCell>)}
          <TableCell>Actions</TableCell>
        </TableRow>
      </TableHead>

      <TableBody>
        {rows.length === 0 && (
        <TableRow>
          <TableCell
            align="center"
            colSpan={columns.length + 1}
          >
            {loading && <CircularProgress />}
            {error && (
            <Alert severity="error">
              Error!
              {error.message}
            </Alert>
            )}
            {!loading && !error && 'There are no records'}
          </TableCell>
        </TableRow>
        )}

        {rows.map((row) => (
          <TableRow
            hover
            key={row.id}
          >
            {columns.map((column) => {
              switch (column.type) {
                case 'date':
                  return (
                    <TableCell key={getValue(row, column)}>
                      {format(parseISO(getValue(row, column)), 'dd/MM/yyyy')}
                    </TableCell>
                  );
                case 'status':
                  return (
                    <TableCell key={getValue(row, column)}>
                      <SeverityPill color={statusMap[getValue(row, column)]}>
                        {getValue(row, column)}
                      </SeverityPill>
                    </TableCell>
                  );
                default:
                  return (
                    <TableCell key={`${column.code}_value`}>
                      {getValue(row, column)}
                    </TableCell>
                  );
              }
            })}

            <TableCell>{getActions(row)}</TableCell>
          </TableRow>
        ))}
      </TableBody>

      {rows && (
      <TableFooter>
        <TableRow>
          <TableCell colSpan={columns.length + 1}>
            <TablePagination
              component="div"
              count={totalCount}
              onPageChange={onPageChange}
              onRowsPerPageChange={onRowsPerPageChange}
              page={!totalCount || totalCount <= 0 ? 0 : page}
              rowsPerPage={itemsPerPage}
              rowsPerPageOptions={[5, 10, 20, 50]}
            />
          </TableCell>
        </TableRow>
      </TableFooter>
      )}
    </Table>
  );
}

DataGrid.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types,react/no-unused-prop-types
  query: PropTypes.object,
  // eslint-disable-next-line react/forbid-prop-types
  columns: PropTypes.array,
  getActions: PropTypes.func,
  itemsPerPage: PropTypes.number,
  totalCount: PropTypes.number,
  page: PropTypes.number,
  onPageChange: PropTypes.func,
  onRowsPerPageChange: PropTypes.func,
  // eslint-disable-next-line react/forbid-prop-types
  error: PropTypes.object,
  loading: PropTypes.bool,
};
