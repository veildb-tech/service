import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { TextField } from '@mui/material';

export function ColumnFakeRandomNumber(props) {
  const { row, onUpdate } = props;

  const [options, setOptions] = useState(
    row.options ?? {
      int1: '',
      int2: '',
    },
  );

  const updateOptions = (field, value) => {
    setOptions((oldState) => ({
      ...oldState,
      [field]: value,
    }));
  };

  return (
    <div className="flex items-start gap-4 mt-3">
      <TextField
        className="input-0 w-full"
        label="From"
        placeholder="From"
        value={options.int1}
        onChange={(event) => updateOptions('int1', event.target.value)}
        onBlur={() => onUpdate('options', options, row)}
      />

      <TextField
        className="input-0 w-full"
        label="To"
        placeholder="To"
        value={options.int2}
        onChange={(event) => updateOptions('int2', event.target.value)}
        onBlur={() => onUpdate('options', options, row)}
      />
    </div>
  );
}

ColumnFakeRandomNumber.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  row: PropTypes.object,
  onUpdate: PropTypes.func,
};
