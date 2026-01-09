import React, { createContext, useContext, useReducer } from 'react';

const RuleAdditionsContext = createContext(null);
const RuleAdditionsDispatchContext = createContext(null);

export function RuleAdditionsProvider({ initial, children }) {
  // eslint-disable-next-line no-use-before-define
  const [additions, dispatch] = useReducer(ruleAdditionsReducer, initial ?? []);

  return (
    <RuleAdditionsContext.Provider value={additions}>
      <RuleAdditionsDispatchContext.Provider value={dispatch}>
        {children}
      </RuleAdditionsDispatchContext.Provider>
    </RuleAdditionsContext.Provider>
  );
}

export function useRuleAdditions() {
  return useContext(RuleAdditionsContext);
}

export function useRuleAdditionsDispatch() {
  return useContext(RuleAdditionsDispatchContext);
}

function ruleAdditionsReducer(additions, action) {
  // eslint-disable-next-line default-case
  switch (action.type) {
    case 'update':
      return action.payload;
  }
}
