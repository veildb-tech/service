import React, { createContext, useContext } from 'react';
import { gql, useQuery } from '@apollo/client';
import { SplashScreen } from 'src/layouts/splash-screen';

const ConfigContext = createContext(null);

const GET_CONFIGURATIONS = gql`
  query {
    configuration {
      databaseStatuses
      platforms
      engines
      dumpStatuses
      serverStatuses
      dumpLogsStatuses
      workspaceGroupRoles
      ruleOperators
      ruleFakers
      cleanUpRules
      webhookStatuses
      webhookOperations
      scheduleTypes
    }
  }
`;

export function ConfigProvider({ children }) {
  const { loading, data } = useQuery(GET_CONFIGURATIONS);

  if (loading) {
    return (
      <ConfigContext.Provider value={data}>
        <SplashScreen />
      </ConfigContext.Provider>
    );
  }

  return <ConfigContext.Provider value={data}>{children}</ConfigContext.Provider>;
}

export function useConfig(config) {
  const configurations = useContext(ConfigContext);

  if (!configurations) {
    return {};
  }

  if (config) {
    return configurations.configuration[config];
  }
  return configurations.configuration;
}
