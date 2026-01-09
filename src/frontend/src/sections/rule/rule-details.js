import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  Typography,
  Alert
} from '@mui/material';
import { GeneralSettings } from 'src/sections/rule/detail/general';
import { SchemaSetting } from 'src/sections/rule/detail/schema';
import { gql, useLazyQuery, useMutation } from '@apollo/client';
import { useRouter } from 'next/router';
import { useUrl } from 'src/hooks/use-url';
import { CREATE_RULE, UPDATE_RULE } from 'src/queries';
import StepNavigator from 'src/sections/rule/step-navigator';
import constants from 'src/sections/rule/step-navigator/constants';
import { getUuidFromRelation } from 'src/utils/uuid';
import { ActionButtons } from './detail/action-buttons';
import { AdditionalSetting } from './detail/additional';

const GET_DATABASE = gql`
  query Database($id: ID!) {
    database(id: $id) {
      name
      id
      created_at
      updated_at
      engine
      status
      platform
      db_schema
      additional_data
      databaseRuleSuggestions {
        id
      }
    }
  }
`;

export function RuleDetails(props) {
  const { data } = props;
  const [selectedDb, setSelectedDb] = useState(data.db ? data.db.id : '');
  const router = useRouter();
  const url = useUrl();
  const urlParamName = 'step';
  const [ruleName, setRuleName] = useState(data.name ?? '');
  const [schedule, setSchedule] = useState(data.schedule ?? '');
  const [scheduleType, setScheduleType] = useState(data.schedule_type ?? 0);
  const [selectedTemplate, setSelectedTemplate] = useState(data?.template?.id ?? '');
  const [showSuccessMessage, setShowSuccessMessage] = useState(false);
  const [showTemplateSuccessMessage, setShowTemplateSuccessMessage] = useState(false);
  const [error, setError] = useState(false);

  const query = data.id ? UPDATE_RULE : CREATE_RULE;
  const [saveRule] = useMutation(query);

  const [loadDatabase, { loading: loadingDatabase, error: errorDatabase, data: databaseData }] = useLazyQuery(GET_DATABASE);

  let database = null;
  if (data.db) {
    database = data.db;
  } else if (databaseData) {
    database = databaseData.database;
  }

  const changeDatabase = (event) => {
    const dbuuid = event.target.value;
    setSelectedDb(dbuuid);
    loadDatabase({ variables: { id: dbuuid } });
  };

  const purifyRules = (rules) => rules.map((rule) => {
    const columns = rule.columns.map((column) => {
      const conditions = column.conditions?.filter((rule) => rule.operator && rule.column && rule.value);

      return { ...column, ...{ conditions } };
    });

    return {
      ...rule,
      ...{ columns }
    };
  });

  const handleSubmit = (rules, additions) => {
    const variables = {
      name: ruleName,
      db: selectedDb,
      schedule,
      scheduleType,
      rule: purifyRules(rules),
      addition: additions,
    };

    if (data.id) {
      variables.id = data.id;
    }

    if (selectedTemplate) {
      variables.template = selectedTemplate;
    }

    try {
      saveRule({ variables }).then((result) => {
        setShowSuccessMessage(true);
        if (!data.id) {
          router.push(
            url.getUrl(
              `rules/${getUuidFromRelation(result.data.createDatabaseRule.databaseRule.id)}`,
            ),
          );
        }
      });
    } catch (e) {
      setError(e.message);
    }
  };

  let schemaSettings = '';
  let additionalSetting = '';

  if (database && !database.db_schema) {
    schemaSettings = <Alert severity="error">Something went wrong. Database schema is missing. Please analyze database first.</Alert>;
  }

  if (database && database.db_schema) {
    const schema = JSON.parse(database.db_schema);
    const ordered = Object
      .keys(schema)
      .sort()
      .reduce((obj, key) => {
        // eslint-disable-next-line no-param-reassign
        obj[key] = schema[key];
        return obj;
      }, {});

    schemaSettings = (
      <SchemaSetting
        schema={ordered}
        error={errorDatabase}
        database={database}
        selectedTemplate={selectedTemplate}
        setSelectedTemplate={setSelectedTemplate}
        selectedDb={selectedDb}
        loading={loadingDatabase}
        stepNumber={1}
        urlParamName={urlParamName}
      />
    );

    if (database.additional_data) {
      const additonalData = JSON.parse(database.additional_data);

      if (Object.keys(additonalData).length > 0) {
        additionalSetting = (
          <AdditionalSetting
            additionalData={additonalData}
            database={database}
            stepNumber={2}
            urlParamName={urlParamName}
          />
        );
      }
    }
  }

  const getSteps = () => {
    const steps = [
      { title: constants.stepTitles[0] },
    ];

    !!schemaSettings && steps.push({ title: constants.stepTitles[schemaSettings.props.stepNumber] });
    !!additionalSetting && steps.push({ title: constants.stepTitles[additionalSetting.props.stepNumber] });

    return steps;
  };

  const steps = getSteps();

  return (
    <form
      onSubmit={(event) => event.preventDefault()}
    >
      {showSuccessMessage && <Alert className="my-6" severity="success">Rule has been saved successfully!</Alert>}
      {showTemplateSuccessMessage && <Alert className="my-6" severity="success">Template has been saved successfully!</Alert>}
      {error && <Alert className="mb-6" severity="error">{error}</Alert>}

      <div className="flex justify-between items-start mb-6">
        <Typography variant="h1">{data.id ? 'Edit rule' : 'Create new rule'}</Typography>

        <ActionButtons
          ruleId={data.id}
          ruleName={ruleName}
          handleError={setError}
          onSaveTemplateSuccess={setShowTemplateSuccessMessage}
          database={database}
          valid={!!(ruleName && database && scheduleType)}
          onSaveButtonClick={handleSubmit}
        />
      </div>

      <StepNavigator
        urlParamName={urlParamName}
        steps={steps}
      />

      <div className="flex flex-col gap-6">
        <GeneralSettings
          ruleData={data}
          ruleName={ruleName}
          setRuleName={setRuleName}
          selectedDb={selectedDb}
          setSchedule={setSchedule}
          schedule={schedule}
          scheduleType={scheduleType}
          setScheduleType={setScheduleType}
          changeDatabase={changeDatabase}
          stepNumber={0}
          urlParamName={urlParamName}
          steps={steps}
        />

        {!!schemaSettings && { ...schemaSettings, ...{ props: { ...schemaSettings.props, ...{ steps } } } }}
        {!!additionalSetting && { ...additionalSetting, ...{ props: { ...additionalSetting.props, ...{ steps } } } }}
      </div>
    </form>
  );
}

RuleDetails.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object,
};
