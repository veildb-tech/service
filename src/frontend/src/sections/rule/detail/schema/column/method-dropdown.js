import React from 'react';
import PropTypes from 'prop-types';
import {
  MenuItem,
  FormControl,
  Select,
  InputLabel
} from '@mui/material';

export function MethodDropdown(props) {
  const {
    row,
    onUpdate,
    rowMethod,
    setRowMethod
  } = props;

  const methods = [
    {
      label: 'Update',
      value: 'update'
    },
    {
      label: 'Fake',
      value: 'fake'
    }
  ];

  const updateMethod = (event) => {
    onUpdate('method', event.target.value, row);
    setRowMethod(event.target.value);
  };

  return (
    <FormControl
      variant="standard"
      className="select-0 w-full max-w-[33%]"
    >
      <InputLabel>Method</InputLabel>

      <Select
        label="Method"
        displayEmpty
        value={rowMethod}
        onChange={updateMethod}
      >
        <MenuItem
          value=""
          key="empty_column"
        >
          Select method
        </MenuItem>
        {methods.map((method) => (
          <MenuItem
            value={method.value}
            key={method.value}
          >
            {method.label}
          </MenuItem>
        ))}
      </Select>
    </FormControl>
  );
}

MethodDropdown.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  row: PropTypes.object,
  onUpdate: PropTypes.func,
  rowMethod: PropTypes.string,
  setRowMethod: PropTypes.func
};
