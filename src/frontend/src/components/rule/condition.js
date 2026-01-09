import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  FormControl,
  InputLabel,
  MenuItem,
  Select,
  TextField,
  Button
} from '@mui/material';
import { useConfig } from 'src/contexts/config-context';
import DeleteOutlinedIcon from '@mui/icons-material/DeleteOutlined';

export function RuleCondition(props) {
  const {
    columns, rule, onUpdate, onDelete
  } = props;

  const { ruleOperators: operators } = useConfig();
  const [ruleColumn, setRuleColumn] = useState(rule.column);
  const [ruleOperator, setRuleOperator] = useState(rule.operator);
  const [hideRuleValueField, setHideRuleValueField] = useState(false);
  const [ruleValue, setRuleValue] = useState(rule.value);

  if (ruleOperator === 'null' || ruleOperator === 'not-null') {
    if (!hideRuleValueField) {
      setHideRuleValueField(true);
      setRuleValue(ruleOperator);
    }
  }

  const update = () => {
    if (ruleColumn && ruleOperator && ruleValue) {
      onUpdate(
        {
          value: ruleValue,
          operator: ruleOperator,
          column: ruleColumn,
        },
        rule,
      );
    }
  };

  return (
    <div className="flex items-center gap-6">
      <FormControl
        variant="standard"
        className="select-0 w-full"
      >
        <InputLabel id="condition-column-label">Column</InputLabel>

        <Select
          displayEmpty
          labelId="condition-column-label"
          label="Method"
          onChange={(event) => setRuleColumn(event.target.value)}
          onBlur={update}
          value={ruleColumn}
        >
          <MenuItem
            value=""
          >
            Select column
          </MenuItem>

          {columns.map((column) => (
            <MenuItem
              value={column}
              key={column}
            >
              {column}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      <FormControl
        variant="standard"
        className="select-0 w-full"
      >
        <InputLabel>Operator</InputLabel>

        <Select
          displayEmpty
          labelId="condition-operator-label"
          label="Operator"
          onChange={(event) => setRuleOperator(event.target.value)}
          onBlur={update}
          value={ruleOperator}
        >
          <MenuItem
            value=""
          >
            Select operator
          </MenuItem>

          {operators.map((operator) => (
            <MenuItem
              value={operator.value}
              key={operator.value}
            >
              {operator.label}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      {!hideRuleValueField && (
        <TextField
          className="input-0 w-full"
          placeholder="Type value"
          label="Value"
          name="value"
          disabled={ruleOperator === 'null' || ruleOperator === 'not-null'}
          onChange={(event) => setRuleValue(event.target.value)}
          onBlur={update}
          value={ruleValue}
        />
      )}

      <Button
        onClick={() => onDelete(rule)}
        className="!p-0 !min-w-max"
      >
        <DeleteOutlinedIcon sx={{ fontSize: 25 }} />
      </Button>
    </div>
  );
}

RuleCondition.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  columns: PropTypes.array,
  // eslint-disable-next-line react/forbid-prop-types
  rule: PropTypes.object,
  onUpdate: PropTypes.func,
  onDelete: PropTypes.func,
};
