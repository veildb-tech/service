import React from 'react';
import { useSearchParams } from 'next/navigation';
import StepNavigatorItem from 'src/sections/rule/step-navigator/item';
import constants from 'src/sections/rule/step-navigator/constants';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';

function StepNavigator(props) {
  const { urlParamName, steps } = props;
  const searchParams = useSearchParams();
  const currentStep = +searchParams.get(urlParamName);
  const stepsLength = (steps && steps.length) || 0;

  const getStepType = (stepNumber) => {
    if (currentStep === stepNumber) {
      return constants.stepTypes.ACTIVE;
    }

    if (currentStep > stepNumber) {
      return constants.stepTypes.COMPLETED;
    }

    if (currentStep < stepNumber) {
      return constants.stepTypes.INACTIVE;
    }
  };

  const getIcon = (stepType, stepNumber) => {
    const stepNumberPlus = stepNumber + 1;
    const stepNumberText = stepNumberPlus >= 10 ? stepNumberPlus : `0${stepNumberPlus}`;

    switch (stepType) {
      case constants.stepTypes.COMPLETED:
        return (
          <span className="flex items-center justify-center">
            <CheckCircleIcon style={{ fontSize: 39 }} />
          </span>
        );
      case constants.stepTypes.ACTIVE:
        return (
          <span className="flex items-center justify-center w-8 h-8 rounded-full border-2 border-dbm-color-primary">
            <span className="text-dbm-color-primary">{stepNumberText}</span>
          </span>
        );
      default:
        return (
          <span className="flex items-center justify-center w-8 h-8 rounded-full border-2 border-dbm-color-6">
            <span className="text-dbm-color-3">{stepNumberText}</span>
          </span>
        );
    }
  };

  const getStepContent = (stepType, title, stepNumber) => (
    <span className="step-navigator-button-inner">
      {getIcon(stepType, stepNumber)}

      <span className={`${stepType === constants.stepTypes.INACTIVE ? 'text-dbm-color-3' : ''}`}>{title}</span>
    </span>
  );

  return (
    stepsLength && (
    <ul className="step-navigator mb-6">
      {steps.map((step, i) => {
        const stepType = getStepType(i);
        const isActive = stepType === constants.stepTypes.ACTIVE;
        const isInactive = stepType === constants.stepTypes.INACTIVE;

        return (
          <li
            key={i}
            className={`step-navigator-item ${stepType}`}
          >
            <StepNavigatorItem
              className={`!p-0 !bg-transparent ${stepType} ${isActive || isInactive ? 'pointer-events-none' : ''}`}
              stepNumber={i}
              urlParamName={urlParamName}
            >
              {getStepContent(stepType, step.title, i)}
            </StepNavigatorItem>
          </li>
        );
      })}
    </ul>
    )
  );
}

export default StepNavigator;
