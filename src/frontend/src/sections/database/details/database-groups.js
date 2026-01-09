import React, { useState } from 'react';
import PropTypes from 'prop-types';
import Multiselect1 from 'src/elements/multiselect1';
import { WORKSPACE_GROUPS_QUERY } from 'src/queries';
import { useQuery } from '@apollo/client';

export function DatabaseGroups(props) {
  const { database, formik } = props;
  const groups = database.groups.collection.map((group) => group.id);
  const [selectedGroups, setSelectedGroups] = useState(groups);

  const {
    loading: loadingGroups,
    data: loadedGroups
  } = useQuery(WORKSPACE_GROUPS_QUERY);

  if (loadingGroups) {
    return ('');
  }

  const allGroups = loadedGroups.groups.collection.map((group) => ({
    value: group.id,
    label: group.name
  }));

  const handleChangeGroups = (groups) => {
    setSelectedGroups(groups);
    formik.setFieldValue('groups', groups);
  };

  return (
    <>
      <div className="w-full">
        <Multiselect1
          options={allGroups}
          selectedOptions={selectedGroups}
          setSelectedOptions={handleChangeGroups}
          valueKey="value"
          labelKey="label"
          className="w-full"
          topLabel="Groups"
          placeholder="Select Groups"
        />
      </div>
      <div className="w-full" />
    </>
  );
}

DatabaseGroups.prototype = {
  database: PropTypes.object,
  formik: PropTypes.object
};
