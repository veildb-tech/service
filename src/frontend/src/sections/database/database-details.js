import PropTypes from 'prop-types';
import React, { useState } from 'react';
import {
  Alert,
  Button, Typography
} from '@mui/material';
import { useMutation } from '@apollo/client';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { DatabaseDumps } from 'src/sections/database/details/database-dumps';
import {
  UPDATE_DATABASE,
  CREATE_DATABASE_DUMP_DELETE_RULE,
  UPDATE_DATABASE_DUMP_DELETE_RULE
} from 'src/queries';
import { usePermission } from 'src/hooks/use-permission';
import { useRouter } from 'next/router';
import Breadcrumbs from 'src/components/breadcrumbs';
import { useUrl } from 'src/hooks/use-url';
import { DatabaseCleanRules } from './details/database-clean-rules';
import { DatabaseGeneral } from './details/database-general';
import { DeleteDatabase } from './delete-database';

export function DatabaseDetails(props) {
  const { data, reload } = props;
  const [success, setSuccess] = useState(false);
  const [updateDatabase, { updating }] = useMutation(UPDATE_DATABASE);
  const route = useRouter();
  const url = useUrl();

  const cleanUpInitial = {
    cleanUpCount: '',
    cleanUpPeriod: 'D'
  };

  const { canSee } = usePermission();
  const canEdit = canSee('database.edit');
  const canEditGroups = canSee('workspace.edit');

  const deleteRuleMutation = data.databaseDumpDeleteRules.length
    ? UPDATE_DATABASE_DUMP_DELETE_RULE
    : CREATE_DATABASE_DUMP_DELETE_RULE;

  if (data.databaseDumpDeleteRules.length) {
    const ruleValue = data.databaseDumpDeleteRules[0].rule[0].value;

    cleanUpInitial.cleanUpPeriod = ruleValue[ruleValue.length - 1]; // get last symbol
    cleanUpInitial.cleanUpCount = parseInt(
      ruleValue
        .replace('P', '')
        .replace(cleanUpInitial.cleanUpPeriod, ''),
      10
    );
  }

  const [updateDatabaseDumpDeleteRule] = useMutation(deleteRuleMutation);

  const initialGroups = data.groups.collection.map((group) => group.id);
  const formik = useFormik({
    initialValues: {
      name: data.name,
      status: data.status,
      engine: data.engine,
      platform: data.platform,
      groups: initialGroups,
      ...cleanUpInitial
    },

    validationSchema: Yup.object({
      name: Yup.string().max(255).required('Name is required'),
      status: Yup.string().max(255).required('Status is required'),
      groups: Yup.array()
    }),

    onSubmit: async (values) => {
      if (values.cleanUpCount && values.cleanUpPeriod) {
        const cleanUpRuleValue = `P${values.cleanUpCount}${values.cleanUpPeriod}`;

        const rule = [
          {
            rule: 'gt',
            value: cleanUpRuleValue
          }
        ];

        const cleanUpRule = {
          rule,
          status: true,
          name: `${values.name} Clean Up Rule`,
          db: data.id
        };

        if (data.databaseDumpDeleteRules.length) {
          cleanUpRule.id = data.databaseDumpDeleteRules[0].id;
        }

        updateDatabaseDumpDeleteRule({
          variables: cleanUpRule
        });
      }

      const variables = {
        id: data.id,
        name: values.name,
        status: values.status
      };

      if (values.groups && canEditGroups) {
        variables.groups = values.groups;
      }

      updateDatabase({
        variables
      }).then(() => {
        setSuccess(true);
        reload(Date.now());
      });
    },
  });

  const redirectToGrid = (time) => {
    route.push(`/databases?reload=${time}`);
  };

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <Breadcrumbs
            className={'mb-1'}
            collection={[
              { url: url.getUrl('/databases'), title: 'Databases' },
              { title: data.name }
            ]}
          />
          <Typography variant="h1">Edit database</Typography>
        </div>
        { canEdit && (
          <div className="flex items-center gap-4">
            <DeleteDatabase
              reload={redirectToGrid}
              uuid={data.id}
            />
            <Button
              className="button-7 min-w-[90px]"
              onClick={formik.handleSubmit}
              disabled={updating}
              variant="contained"
            >
              Save details
            </Button>
          </div>
        ) }
      </div>

      {success && (
        <Alert severity="success">Database successfully updated</Alert>
      )}

      <DatabaseGeneral
        formik={formik}
        database={data}
        updateDate={data.updated_at}
      />

      { canEdit && (
        <DatabaseCleanRules
          formik={formik}
        />
      ) }

      <DatabaseDumps
        reload={reload}
        database={data}
        dumps={data.databaseDumps.edges}
      />
    </div>
  );
}

DatabaseDetails.propTypes = {
  data: PropTypes.object,
  reload: PropTypes.func,
};
