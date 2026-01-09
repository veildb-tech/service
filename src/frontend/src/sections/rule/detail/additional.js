import React from 'react';
import PropTypes from 'prop-types';
import {
  Divider,
  Typography,
} from '@mui/material';
import constants from 'src/sections/rule/step-navigator/constants';
import ArrowForwardRoundedIcon from '@mui/icons-material/ArrowForwardRounded';
import { useStepNavigator } from 'src/hooks/use-step-navigator';
import StepNavigatorItem from 'src/sections/rule/step-navigator/item';
import { MagentoAttributeSetting } from './additional/magento/attributes';

export function AdditionalSetting(props) {
  const {
    additionalData,
    database,
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

  const isMagento = database?.platform === 'magento';

  if (!isMagento) {
    return null;
  }

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

            <div className="sub-heading-0 font-medium">In this section you could configure platform specific settings</div>
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

        <div className="card-content">
          <MagentoAttributeSetting data={additionalData} />
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

AdditionalSetting.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  additionalData: PropTypes.object,
  // eslint-disable-next-line react/forbid-prop-types
  database: PropTypes.object,
};
