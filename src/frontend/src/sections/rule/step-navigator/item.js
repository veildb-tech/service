import React from 'react';
import {
  Button
} from '@mui/material';
import { useSearchParams, usePathname, useRouter } from 'next/navigation';
import { EditIcon } from 'src/elements/icons';

function StepNavigatorItem(props) {
  const {
    urlParamName,
    stepNumber,
    className,
    disabled,
    children,
    editLink
  } = props;
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const { push } = useRouter();

  const setStep = () => {
    const params = new URLSearchParams(searchParams);
    params.set(urlParamName, stepNumber);
    params.delete('uuid');

    push(`${pathname}?${params.toString()}`);
  };

  return (
    <Button
      onClick={setStep}
      className={editLink ? 'link-0 link-1' : className}
      disabled={disabled}
    >
      {editLink && <EditIcon />}
      {children}
    </Button>
  );
}

export default StepNavigatorItem;
