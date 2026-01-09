import React from 'react';
import PropTypes from 'prop-types';
import {
  Table, TableHead, TableRow, TableBody, TableCell
} from '@mui/material';
import { SeverityPill } from 'src/components/severity-pill';

export function SchemaColumns(props) {
  const {
    tableSchema, selectedMethod, onColumnSelect, selectedColumn
  } = props;

  return (
    <Table>
      <TableHead>
        <TableRow>
          <TableCell>Column name</TableCell>

          <TableCell>Rule</TableCell>
        </TableRow>
      </TableHead>
      <TableBody>
        {/* eslint-disable-next-line array-callback-return */}
        {Object.keys(tableSchema).map((column) => {
          if (column) {
            return (
              <TableRow
                hover
                sx={{ cursor: 'pointer' }}
                selected={selectedColumn === column}
                onClick={() => onColumnSelect(column)}
                key={column}
              >
                <TableCell>{column}</TableCell>

                <TableCell>
                  <SeverityPill>{selectedMethod}</SeverityPill>
                </TableCell>
              </TableRow>
            );
          }
        })}
      </TableBody>
    </Table>
  );
}

SchemaColumns.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  tableSchema: PropTypes.object,
  selectedMethod: PropTypes.string,
  selectedColumn: PropTypes.string,
  onColumnSelect: PropTypes.func,
};
