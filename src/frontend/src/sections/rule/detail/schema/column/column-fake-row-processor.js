import React from 'react';
import PropTypes from 'prop-types';
import { ColumnFakeRandomNumber } from './fake-processors/random-number';

export function ColumnFakeRowProcessor(props) {
  const { row, onUpdate } = props;

  return (
    row.value === 'numberBetween' && (
    <ColumnFakeRandomNumber
      key={`${row.name + row.value}_processor`}
      row={row}
      onUpdate={onUpdate}
    />
    )
  );
}

ColumnFakeRowProcessor.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  row: PropTypes.object,
  onUpdate: PropTypes.func,
};
