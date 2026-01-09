import PropTypes from 'prop-types';
import React from 'react';
import {
  Typography,
  CircularProgress
} from '@mui/material';
import Multiselect1 from 'src/elements/multiselect1';

import { GET_DATABASES } from 'src/queries';
import { useQuery } from '@apollo/client';

export default function GroupDatabases(props) {
  const { data, loading } = useQuery(GET_DATABASES);
  const { updateSelectedDatabases, selectedDatabases } = props;
  const databases = data?.databases?.collection;

  if (loading) {
    return <CircularProgress />;
  }

  return (
    <div className="mb-9">
      <Typography className="!mb-9" variant="h4">Select databases</Typography>

      <Multiselect1
        options={databases}
        selectedOptions={selectedDatabases}
        setSelectedOptions={updateSelectedDatabases}
        valueKey="id"
        labelKey="name"
        className="w-full"
        topLabel="Databases"
        placeholder="Select Databases"
      />
    </div>
  );
}

GroupDatabases.propTypes = {
  updateSelectedDatabases: PropTypes.func,
  // eslint-disable-next-line react/forbid-prop-types
  selectedDatabases: PropTypes.array,
};
