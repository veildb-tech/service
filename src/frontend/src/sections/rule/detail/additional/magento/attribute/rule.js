import PropTypes from 'prop-types';
import React, { useEffect, useState } from 'react';

import {
  Select, MenuItem, Button, TextField, InputLabel, FormControl
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';

const methods = [
  {
    label: 'Truncate',
    value: 'truncate',
  },
  {
    label: 'Fake',
    value: 'fake',
  },
  {
    label: 'Update',
    value: 'update',
  },
];

export function MagentoAttributeRule(props) {
  const {
    attribute, attributeRule, onUpdate, onReset
  } = props;
  const [method, setMethod] = useState(attributeRule?.method ?? '');
  const [value, setValue] = useState(attributeRule?.value ?? '');
  const { ruleFakers: patterns } = useConfig();

  useEffect(() => {
    // For truncate method update without value field
    if (method === 'truncate') {
      onUpdate(attribute, {
        method,
      });
    }

    // For fake and update method update only if value is filled
    if ((method === 'fake' || method === 'update') && value.length) {
      onUpdate(attribute, {
        method,
        value,
      });
    }
  }, [method, value]);

  const handleReset = (attribute) => {
    setValue('');
    setMethod('');
    onReset(attribute);
  };

  return (
    <div className="flex gap-3">
      <FormControl
        variant="standard"
        className="select-0 !min-w-[240px]"
      >
        <InputLabel>Method</InputLabel>

        <Select
          displayEmpty
          value={method}
          onChange={(event) => setMethod(event.target.value)}
        >
          <MenuItem
            value=""
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

      {method === 'fake' && (
        <FormControl
          variant="standard"
          className="select-0 !min-w-[240px]"
        >
          <InputLabel>Pattern</InputLabel>

          <Select
            displayEmpty
            value={value}
            onChange={(event) => setValue(event.target.value)}
          >
            <MenuItem
              value=""
            >
              Select Pattern
            </MenuItem>

            {patterns.map((pattern) => (
              <MenuItem
                value={pattern.value}
                key={pattern.value}
              >
                {pattern.label}
              </MenuItem>
            ))}
          </Select>
        </FormControl>
      )}

      {method === 'update' && (
        <TextField
          className="input-0"
          placeholder="Type Value"
          label="Value"
          value={value}
          onChange={(event) => setValue(event.target.value)}
          onBlur={(event) => setValue(event.target.value)}
        />
      )}

      {(method || value) && (
      <Button
        className="button-1"
        onClick={() => handleReset(attribute)}
      >
        reset
      </Button>
      )}
    </div>
  );
}

MagentoAttributeRule.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  attribute: PropTypes.object,
  // eslint-disable-next-line react/forbid-prop-types
  attributeRule: PropTypes.object,
  onReset: PropTypes.func,
  onUpdate: PropTypes.func,
};
