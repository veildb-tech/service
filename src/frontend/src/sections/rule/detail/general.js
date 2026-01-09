import React from 'react';
import PropTypes from 'prop-types';
import {
  Divider,
  TextField,
  Typography
} from '@mui/material';
import { useStepNavigator } from 'src/hooks/use-step-navigator';
import StepNavigatorItem from 'src/sections/rule/step-navigator/item';
import constants from 'src/sections/rule/step-navigator/constants';
import ArrowForwardRoundedIcon from '@mui/icons-material/ArrowForwardRounded';
import { RuleDatabaseSelector } from './general/database-selector';
import { Schedule } from './general/schedule';

export function GeneralSettings(props) {
  const {
    changeDatabase,
    schedule,
    setSchedule,
    scheduleType,
    setScheduleType,
    selectedDb,
    ruleData,
    ruleName,
    setRuleName,
    urlParamName,
    stepNumber,
    steps
  } = props;

  const {
    isActive,
    isInactive,
    stepPlus,
    isCompleted
  } = useStepNavigator(urlParamName, stepNumber);

  return (
    <>
      <div className={`card ${isInactive ? 'hidden' : ''}`}>
        <div className="card-content flex justify-between gap-4">
          <div className="flex flex-col gap-1">
            <Typography
              className="mb-1"
              variant="h5"
            >
              {stepPlus}
              .
              {' '}
              {steps[stepNumber].title}
            </Typography>

            <div className="sub-heading-0 font-medium">Please specify general rule settings</div>
          </div>

          {isCompleted && (
            <StepNavigatorItem
              stepNumber={stepNumber}
              urlParamName={urlParamName}
              editLink
            >
              <span>Edit</span>
            </StepNavigatorItem>
          )}
        </div>

        <Divider className={`!my-5 ${isActive ? '' : 'hidden'}`} />

        <div className={`card-content ${isActive ? '' : 'hidden'}`}>
          <div className="flex w-full gap-5 mb-10">
            <TextField
              className="input-0 w-full"
              label="Name"
              placeholder="Type Name"
              name="name"
              onChange={(event) => setRuleName(event.target.value)}
              value={ruleName}
              required
            />

            <RuleDatabaseSelector
              ruleId={ruleData.id}
              value={selectedDb}
              onChange={changeDatabase}
            />
          </div>

          <Schedule
            schedule={schedule}
            setSchedule={setSchedule}
            scheduleType={scheduleType}
            setScheduleType={setScheduleType}
          />
        </div>
      </div>

      {isActive && stepPlus !== steps.length && (
        <StepNavigatorItem
          className="button-8 self-start"
          stepNumber={stepPlus}
          urlParamName={urlParamName}
        >
          <span>
            {`Next to ${constants.stepTitles[stepPlus]}`}
          </span>

          <ArrowForwardRoundedIcon style={{ fontSize: 21 }} />
        </StepNavigatorItem>
      )}
    </>
  );
}

GeneralSettings.propTypes = {
  selectedDb: PropTypes.string,
  // eslint-disable-next-line react/forbid-prop-types
  changeDatabase: PropTypes.func,
  // eslint-disable-next-line react/no-unused-prop-types
  changeTemplate: PropTypes.func,
  ruleName: PropTypes.string,
  setRuleName: PropTypes.func,
  schedule: PropTypes.string,
  setSchedule: PropTypes.func,
  scheduleType: PropTypes.number,
  setScheduleType: PropTypes.func,
  ruleData: PropTypes.object
};
