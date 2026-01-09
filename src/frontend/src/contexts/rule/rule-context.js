import React, { createContext, useContext, useReducer } from 'react';

const RulesContext = createContext(null);
const RulesDispatchContext = createContext(null);

export function RulesProvider({ initial, children }) {
  // eslint-disable-next-line no-use-before-define
  const [rules, dispatch] = useReducer(rulesReducer, initial ?? []);

  return (
    <RulesContext.Provider value={rules}>
      <RulesDispatchContext.Provider value={dispatch}>{children}</RulesDispatchContext.Provider>
    </RulesContext.Provider>
  );
}

export function useRules() {
  return useContext(RulesContext);
}

export function useRulesDispatch() {
  return useContext(RulesDispatchContext);
}

function rulesReducer(rules, action) {
  const updateSingleRule = (field, value) => {
    // Try to find rule for this table. If there are no such rule need to create new one
    const rule = rules.find((rule) => rule.table === action.table);
    if (!rule) {
      return [
        ...rules,
        {
          table: action.table,
          columns: [],
          method: '',
          [field]: value,
        },
      ];
    }

    return rules.map((rule) => {
      if (rule.table === action.table) {
        return {
          ...rule,
          [field]: value,
        };
      }
      return rule;
    });
  };

  const deleteRule = (table) => rules.filter((rule) => rule.table !== table);

  switch (action.type) {
    case 'setMethod': {
      if (action.method === '') {
        return deleteRule(action.table);
      }

      const rule = rules.find((rule) => rule.table === action.table);
      if (rule) {
        return rules.map((rule) => {
          if (rule.table === action.table) {
            return {
              columns: action.method !== 'truncate' ? [{
                index: `${rule.table}_row_0`,
              }] : [],
              table: action.table,
              method: action.method
            };
          }
          return rule;
        });
      }

      return updateSingleRule('method', action.method);
    }
    case 'updateStatus': {
      return updateSingleRule('status', action.status);
    }
    case 'updateConditions': {
      return updateSingleRule('conditions', action.conditions);
    }
    case 'updateConditionOperator': {
      return updateSingleRule('conditionOperator', action.conditionOperator);
    }
    case 'update': {
      return action.rules;
    }
    case 'updateColumn': {
      const editableRule = rules.find((rule) => rule.table === action.table);
      const updateValue = {
        name: action.columnName,
        index: action.index,
        method: action.method,
        ...action.value,
      };

      let columns = [updateValue];
      if (editableRule) {
        const columnToEdit = editableRule.columns.find((col) => col.index === action.index);

        if (columnToEdit) {
          columns = editableRule.columns.map((col) => {
            if (col.index === action.index) {
              return { ...col, ...updateValue };
            }
            return col;
          });
        } else {
          columns = [...editableRule.columns, ...columns];
        }
      }

      return updateSingleRule('columns', columns);
    }
    case 'deleteColumn': {
      return rules.map((rule) => {
        if (rule.table === action.table) {
          return {
            ...rule,
            columns: rule.columns.filter((column) => column.index !== action.index),
          };
        }
        return rule;
      });
    }
    default:
      throw Error(`Unknown action: ${action.type}`);
      // eslint-disable-next-line no-unreachable
      break;
  }
}
