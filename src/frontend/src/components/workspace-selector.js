import * as React from 'react';
import { CircularProgress, SvgIcon } from '@mui/material';
import ChevronUpDownIcon from '@heroicons/react/24/solid/ChevronUpDownIcon';
import { useAuth } from 'src/hooks/use-auth';
import { useWorkspace } from 'src/hooks/use-workspace';
import { CURRENT_USER_QUERY } from 'src/queries';
import { useLazyQuery } from '@apollo/client';
import Select from 'react-select';

export function WorkspaceSelector() {
  const auth = useAuth();
  const workspaceHook = useWorkspace();
  const options = auth.user.workspaces;

  /* TODO: Change it to get dynamically workspace */
  const currentWorkspace = options.find(
    (userWorkspace) => userWorkspace.code === workspaceHook.getCurrentWorkspaceCode()
  );
  const [, { loading }] = useLazyQuery(CURRENT_USER_QUERY);

  const handleSelectWorkspace = (workspaceCode) => {
    auth.changeWorkspace(workspaceCode);
  };

  // eslint-disable-next-line react/no-unstable-nested-components
  function DropdownIndicator() {
    return (
      <SvgIcon sx={{ color: 'white', fontSize: 26 }}>
        <ChevronUpDownIcon />
      </SvgIcon>
    );
  }

  return (
    loading
      ? <CircularProgress />
      : (
        <Select
          defaultValue={currentWorkspace}
          options={options}
          isSearchable={false}
          getOptionLabel={(option) => option.name}
          getOptionValue={(option) => option.code}
          components={{ DropdownIndicator }}
          onChange={({ code }) => handleSelectWorkspace(code)}
          classNames={{
            control: () => '!bg-dbm-color-primary-dark p-3 '
              + '!rounded-lg !cursor-pointer !shadow-none',
            option: ({ isSelected }) => `font-semibold 
            ${isSelected ? '!bg-dbm-color-primary-light' : '!text-dbm-color-white '
              + '!bg-inherit hover:!text-dbm-color-secondary !cursor-pointer'}`,
            singleValue: () => '!text-dbm-color-secondary text-base font-semibold !m-0',
            valueContainer: () => '!p-0',
            indicatorSeparator: () => '!hidden',
            menu: () => '!m-0  !-mt-1 !rounded-lg !rounded-t-none border-t '
              + '!border-dbm-color-6 !bg-dbm-color-primary-dark',
            menuList: () => '!p-0 !pt-2 !pb-2',
          }}
          styles={{
            control: (provided) => ({
              ...provided,
              border: 'none'
            })
          }}
        />
      )
  );
}
