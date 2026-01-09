import React from 'react';
import PropTypes from 'prop-types';
import { Checkbox, FormControlLabel, } from '@mui/material';
import { useRules } from 'src/contexts/rule/rule-context';
import constants from 'src/sections/rule/detail/schema/legend/constants';

export function SchemaTables(props) {
  const {
    onChangeTable,
    selectedTable,
    schema,
    searchQuery,
    selectedSortOption
  } = props;
  const rules = useRules();

  const getRuleByTableName = (table) => (
    // eslint-disable-next-line array-callback-return
    rules.find((rule) => {
      if (rule.table === table) {
        return rule;
      }
    }) ?? {}
  );
  const sortedTables = () => {
    if (selectedSortOption === 'alphabet') {
      return Object.keys(schema).sort();
    }

    return Object.keys(schema).sort((b, a) => {
      const statusA = getRuleByTableName(a).status || '';
      const statusB = getRuleByTableName(b).status || '';

      if (statusA === statusB) {
        return 0;
      }

      if (statusB === 'action_required') return -1;
      if (statusA === 'action_required') return 1;

      return statusA.localeCompare(statusB);
    });
  };

  const content = sortedTables().map((table) => {
    if (searchQuery && !table.includes(searchQuery)) {
      return null;
    }

    const isChecked = selectedTable === table;
    const { status } = getRuleByTableName(table);
    const appliedIcon = constants.legendItems.APPLIED.icon({ sx: { fontSize: 14 } });
    const suggestedIcon = constants.legendItems.SUGGESTED_CONDITIONS.icon({ sx: { fontSize: 14 } });
    const actionRequiredIcon = constants.legendItems.ACTION_REQUIRED.icon({ sx: { fontSize: 14 } });

    return (
      <li
        key={table}
        className={`flex items-center justify-between w-full pr-3 gap-3 border-b border-b-dbm-color-16
            bg-db ${isChecked ? 'bg-dbm-color-white text-dbm-color-secondary border-l-dbm-color-secondary ' : 'bg-dbm-color-7'}`}
      >
        <FormControlLabel
          title={table}
          className={`!m-0 ${status ? 'w-[87%]' : 'w-full'}  py-2 pl-4`}
          classes={{
            label: `${isChecked ? '!font-semibold' : '!font-medium'} overflow-hidden text-ellipsis w-full !text-[14px]`
          }}
          control={(
            <Checkbox
              className="!hidden"
              value={table}
              onChange={onChangeTable}
              checked={isChecked}
            />
            )}
          label={table}
        />

        {status === 'ready' && appliedIcon}
        {status === 'suggested' && suggestedIcon}
        {status === 'action_required' && actionRequiredIcon}
      </li>
    );
  });

  const isEmpty = content.every((item) => !item);

  return (
    <ul className="w-[244px] min-w-[244px] flex flex-col max-h-[70vh] min-h-[300px] overflow-auto">
      {!isEmpty ? content : <div className="p-2">No items found</div>}
    </ul>
  );
}

SchemaTables.propTypes = {
  onChangeTable: PropTypes.func,
  selectedTable: PropTypes.string,
  // eslint-disable-next-line react/forbid-prop-types
  schema: PropTypes.object,
};
