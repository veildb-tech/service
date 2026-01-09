import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  TextField,
} from '@mui/material';

export function ColumnConfiguration(props) {
  const {
    row,
    onUpdate,
  } = props;
  const [value, setValue] = useState(row.value ?? '');
  return (
    <TextField
      className="input-0 w-full max-w-[33%]"
      helperText="Please specify the value"
      label="Value"
      placeholder="Select Value"
      name="value"
      value={value}
      onChange={(event) => setValue(event.target.value)}
      onBlur={() => onUpdate('value', value, row)}
    />
  );
}

ColumnConfiguration.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  row: PropTypes.object,
  onUpdate: PropTypes.func
};
