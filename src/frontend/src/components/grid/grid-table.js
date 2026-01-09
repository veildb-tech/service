import React from 'react';
import PropTypes from 'prop-types';

import {
  Box,
  Card,
  Checkbox,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TablePagination,
  TableRow,
} from '@mui/material';
import { Scrollbar } from 'src/components/scrollbar';
import { SeverityPill } from 'src/components/severity-pill';
import { format, parseISO } from 'date-fns';

export function GridTable(props) {
  const {
    count = 0,
    items = [],
    columns = [],
    getActions = () => {},
    withNode = false,
    onDeselectAll,
    onDeselectOne,
    onPageChange = () => {},
    onRowsPerPageChange,
    onSelectAll,
    onSelectOne,
    page = 0,
    rowsPerPage = 5,
    selected = [],
  } = props;

  const statusMap = {
    disabled: 'error',
    enabled: 'success',
  };

  const selectedSome = selected.length > 0 && selected.length < items.length;
  const selectedAll = items.length > 0 && selected.length === items.length;
  const getValue = (item, column) => {
    if (column.getValue) {
      return column.getValue(item, column);
    }
    return withNode ? item['node'][column.code] : item[column.code];
  };

  return (
    <Card>
      <Scrollbar>
        <Box sx={{ minWidth: 800 }}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell padding="checkbox">
                  <Checkbox
                    checked={selectedAll}
                    indeterminate={selectedSome}
                    onChange={(event) => {
                      if (event.target.checked) {
                        onSelectAll?.();
                      } else {
                        onDeselectAll?.();
                      }
                    }}
                  />
                </TableCell>
                {columns.map((column) => <TableCell key={column.code}>{column.title}</TableCell>)}
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {items.length === 0 && (
                <TableRow>
                  <TableCell
                    align="center"
                    colSpan={columns.length + 2}
                  >
                    There are no records
                  </TableCell>
                </TableRow>
              )}
              {items.map((item) => {
                const id = withNode ? item.node.id : item.id;
                const isSelected = selected.includes(id);
                return (
                  <TableRow
                    hover
                    key={id}
                    selected={isSelected}
                  >
                    <TableCell padding="checkbox">
                      <Checkbox
                        checked={isSelected}
                        onChange={(event) => {
                          if (event.target.checked) {
                            onSelectOne?.(id);
                          } else {
                            onDeselectOne?.(id);
                          }
                        }}
                      />
                    </TableCell>

                    {columns.map((column) => {
                      switch (column.type) {
                        case 'date':
                          return (
                            <TableCell key={getValue(item, column)}>
                              {format(parseISO(getValue(item, column)), 'dd/MM/yyyy')}
                            </TableCell>
                          );
                        case 'status':
                          return (
                            <TableCell key={getValue(item, column)}>
                              <SeverityPill color={statusMap[getValue(item, column)]}>
                                {getValue(item, column)}
                              </SeverityPill>
                            </TableCell>
                          );
                        default:
                          return (
                            <TableCell key={`${column.code}_value`}>
                              {getValue(item, column)}
                            </TableCell>
                          );
                      }
                    })}

                    <TableCell>{getActions(item)}</TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </Box>
      </Scrollbar>
      <TablePagination
        component="div"
        count={count}
        onPageChange={onPageChange}
        onRowsPerPageChange={onRowsPerPageChange}
        page={page}
        rowsPerPage={rowsPerPage}
        rowsPerPageOptions={[5, 10, 25]}
      />
    </Card>
  );
}

GridTable.propTypes = {
  count: PropTypes.number,
  // eslint-disable-next-line react/forbid-prop-types
  items: PropTypes.array,
  withNode: PropTypes.bool,
  getActions: PropTypes.func,
  // eslint-disable-next-line react/forbid-prop-types
  columns: PropTypes.array,
  onDeselectAll: PropTypes.func,
  onDeselectOne: PropTypes.func,
  onPageChange: PropTypes.func,
  onRowsPerPageChange: PropTypes.func,
  onSelectAll: PropTypes.func,
  onSelectOne: PropTypes.func,
  page: PropTypes.number,
  rowsPerPage: PropTypes.number,
  // eslint-disable-next-line react/forbid-prop-types
  selected: PropTypes.array,
};
