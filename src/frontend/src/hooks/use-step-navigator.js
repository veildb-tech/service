import { useSearchParams } from 'next/navigation';

export const useStepNavigator = (urlParamName, stepNumber) => {
  const searchParams = useSearchParams();
  const currentStep = +searchParams.get(urlParamName);
  const stepPlus = stepNumber + 1;
  const isActive = currentStep === stepNumber;
  const isInactive = currentStep < stepNumber;
  const isCompleted = currentStep > stepNumber;

  return {
    currentStep,
    stepPlus,
    isActive,
    isInactive,
    isCompleted,
  };
};
