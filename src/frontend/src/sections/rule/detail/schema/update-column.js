import React, { useCallback, useState } from 'react';
import PropTypes from 'prop-types';
import { Button } from '@mui/material';
import { useRules, useRulesDispatch } from 'src/contexts/rule/rule-context';
import Row from './row';

export function UpdateColumn(props) {
  const { selectedTable, tableSchema, method } = props;

  const rules = useRules();

  const getColumns = () => {
    const columns = [];
    // eslint-disable-next-line array-callback-return
    rules.map((rule) => {
      if (rule.table === selectedTable) {
        // eslint-disable-next-line array-callback-return
        rule.columns.map((ruleColumn) => {
          columns.push({
            ...ruleColumn,
            conditions: ruleColumn.conditions ?? [],
          });
        });
      }
    });

    return columns;
  };

  const columns = getColumns();
  const [rows, setRows] = useState(columns);
  const dispatch = useRulesDispatch();

  const updateRow = useCallback(
    (field, value, updateRow) => {
      setRows((rows) => rows.map((row) => {
        if (row.index === updateRow.index) {
          return {
            ...row,
            [field]: value,
          };
        }
        return row;
      }));
      // eslint-disable-next-line no-param-reassign
      updateRow[field] = value;

      if (updateRow.name && updateRow.value && updateRow.method) {
        dispatch({
          type: 'updateColumn',
          index: updateRow.index,
          columnName: updateRow.name,
          method: updateRow.method,
          value: {
            [field]: value,
          },
          table: selectedTable,
        });
      }
    },
    [rows],
  );

  const deleteRow = (deleteRow) => {
    dispatch({
      type: 'deleteColumn',
      index: deleteRow.index,
      columnName: deleteRow.name,
      table: selectedTable,
    });
    return setRows((rows) => rows.filter((row) => row.index !== deleteRow.index));
  };

  const addRow = () => {
    setRows((prevRows) => [
      ...prevRows,
      {
        index: `${selectedTable}_row_${columns.length + 1}`,
        name: '',
        method: '',
        value: '',
        conditions: [],
      },
    ]);
  };

  return (
    <>
      <Button
        className="button-10 self-center !mb-4"
        onClick={addRow}
      >
        <span className="font-normal text-2xl leading-none">+</span>
        <span>Add rule</span>
      </Button>

      {!!rows?.length
        && (
        <div className="flex flex-col gap-4">
          {rows.map((row) => (
            <Row
              row={row}
              tableSchema={tableSchema}
              method={method}
              key={`${row.index}_value`}
              updateRow={updateRow}
              deleteRow={deleteRow}
              selectedTable={selectedTable}
            />
          )).reverse()}
        </div>
        )}
    </>
  );
}

UpdateColumn.propTypes = {
  selectedTable: PropTypes.string,
  // eslint-disable-next-line react/forbid-prop-types
  tableSchema: PropTypes.object,
  method: PropTypes.string,
};
