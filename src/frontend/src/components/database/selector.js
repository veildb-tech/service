import React from 'react';
import PropTypes from 'prop-types';

import {
  FormControl,
  InputLabel,
  MenuItem,
  Select
} from '@mui/material';
import { GET_DATABASES } from 'src/queries';
import { useQuery } from '@apollo/client';

export function DatabaseSelector(props) {
  const { value, onChange } = props;

  const {
    loading: loadingDatabases,
    error: errorDatabases,
    data: databasesData,
  } = useQuery(GET_DATABASES);

  return (
    <FormControl
      variant="standard"
      className="select-0 w-full"
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
              >
                {database.name}
              </MenuItem>
            ))}
        </Select>
      )}
    </FormControl>
  );
}

DatabaseSelector.propTypes = {
  value: PropTypes.string,
  onChange: PropTypes.func,
};
