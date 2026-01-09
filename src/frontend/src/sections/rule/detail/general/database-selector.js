import React from 'react';
import PropTypes from 'prop-types';

import {
  FormControl,
  InputLabel,
  MenuItem,
  FormHelperText,
  Select
} from '@mui/material';
import { GET_RULE_DATABASES } from 'src/queries';
import { useQuery } from '@apollo/client';

export function RuleDatabaseSelector(props) {
  const { value, onChange, ruleId } = props;

  const {
    loading: loadingDatabases,
    error: errorDatabases,
    data: databasesData,
  } = useQuery(GET_RULE_DATABASES);

  return (
    <div className="w-full">
      <FormControl
        variant="standard"
        className="select-0  w-full"
        error={!!errorDatabases}
      >
        <InputLabel id="database-select-label">Select Database</InputLabel>

        {!loadingDatabases && (
          <Select
            labelId="database-select-label"
            disabled={loadingDatabases || !!errorDatabases}
            onChange={onChange}
            value={value}
            label="Database"
          >
            {!errorDatabases
              && databasesData.databases.collection.map((database) => (
                <MenuItem
                  key={database.id}
                  value={database.id}
                  disabled={database.databaseRule !== null && database.databaseRule.id !== ruleId}
                >
                  {database.name}
                </MenuItem>
              ))}
          </Select>
        )}
      </FormControl>
      <FormHelperText>Currently, it is possible to assign one rule to one database</FormHelperText>
    </div>
  );
}

RuleDatabaseSelector.propTypes = {
  value: PropTypes.string,
  onChange: PropTypes.func,
  ruleId: PropTypes.string
};
