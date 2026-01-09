import React, { useState, useCallback, useEffect } from 'react';
import PropTypes from 'prop-types';
import {
  Button, Divider
} from '@mui/material';
import Switch1 from 'src/elements/switch1';
import { RuleCondition } from './condition';

export function RuleConditions(props) {
  const {
    columns,
    onConditionUpdate,
    onConditionOperatorUpdate,
    initial,
    selectedTable,
    conditionOperator,
    row
  } = props;
  const [rules, setRules] = useState(initial ?? []);
  const [ruleUpdated, setRuleUpdated] = useState(false);
  const switchOptions = [
    { title: 'and', value: 'and' },
    { title: 'or', value: 'or' }
  ];

  const [selectedSwitchOption, setSelectedSwitchOption] = useState(conditionOperator ?? switchOptions[0].value);

  const addRule = () => {
    setRules((prevRules) => [
      ...prevRules,
      {
        index: rules.length,
        operator: '',
        column: '',
        value: '',
      },
    ]);
  };

  const ruleUpdate = useCallback(
    (newRule, oldRule) => {
      setRules((rules) => rules.map((rule) => {
        if (rule.index === oldRule.index) {
          return {
            ...rule,
            ...newRule,
          };
        }
        return rule;
      }));
      setRuleUpdated(true);
    },
    [rules],
  );

  useEffect(() => {
    if (ruleUpdated) {
      onConditionUpdate(rules);
      setRuleUpdated(false);
    }
  }, [rules, setRuleUpdated]);

  const deleteRule = (deleteRule) => {
    setRules((rules) => rules.filter((rule) => rule.index !== deleteRule.index));
    setRuleUpdated(true);
  };

  useEffect(() => {
    onConditionOperatorUpdate(selectedSwitchOption, selectedTable, row?.name);
  }, [selectedSwitchOption, rules]);

  return (
    <>
      <Divider className="!my-6" />

      <Button
        className="button-9 !mb-4 self-start"
        onClick={addRule}
      >
        <span className="font-normal text-2xl leading-none">+</span>
        <span>Specify conditions</span>
      </Button>

      {!!rules?.length && (
        <div className="flex flex-col w-full">
          <Switch1
            className="self-start min-w-[210px]"
            selectedOption={selectedSwitchOption}
            setOption={setSelectedSwitchOption}
            options={switchOptions}
            name="searchtype"
          />

          <div className="flex flex-col gap-3 rounded-tr-lg rounded-b-lg border border-dbm-color-14 p-3">
            {rules.map((rule) => (
              <RuleCondition
                key={`rule_${rule.index}`}
                rule={rule}
                columns={columns}
                onUpdate={ruleUpdate}
                onDelete={deleteRule}
              />
            ))}
          </div>
        </div>
      )}
    </>
  );
}

RuleConditions.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  columns: PropTypes.array,
  // eslint-disable-next-line react/forbid-prop-types
  initial: PropTypes.array,
  conditionOperator: PropTypes.string,
  onConditionUpdate: PropTypes.func,
  onConditionOperatorUpdate: PropTypes.func,
};
