import { gql } from '@apollo/client';

export const GET_RULE = gql`
  query DatabaseRule($id: ID!) {
    databaseRule(id: $id) {
      name
      id
      schedule
      schedule_type
      addition
      template {
        id
      }
      db {
        id
        name
        db_schema
        platform
        additional_data
        databaseRuleSuggestions {
          id
        }
      }
      rule
    }
  }
`;

export const GET_RULES = gql`
  query databaseRules($itemsPerPage: Int, $page: Int) {
    databaseRules(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        id
        name
        db {
          name
        }
      }
    }
  }
`;

export const CREATE_RULE = gql`
  mutation createDatabaseRule(
    $name: String!
    $db: String!
    $rule: Iterable!
    $addition: Iterable
    $schedule: String
    $scheduleType: Int
    $template: String   
  ) {
    createDatabaseRule(
      input: {
        name: $name,
        db: $db,
        rule: $rule,
        schedule: $schedule,
        scheduleType: $scheduleType,
        addition: $addition
        template: $template
      }
    ) {
      databaseRule {
        name
        rule
        schedule
        addition
        id
        template {
          id
        }
      }
    }
  }
`;

export const DELETE_RULE = gql`
  mutation deleteDatabaseRule($id: ID!) {
    deleteDatabaseRule(input: { id: $id }) {
      databaseRule {
        id
      }
    }
  }
`;

export const UPDATE_RULE = gql`
  mutation updateDatabaseRule(
    $id: ID!
    $name: String!
    $db: String!
    $rule: Iterable!
    $addition: Iterable
    $schedule: String
    $scheduleType: Int
    $template: String
  ) {
    updateDatabaseRule(
      input: {
        id: $id
        name: $name
        db: $db
        rule: $rule
        schedule: $schedule
        scheduleType: $scheduleType
        addition: $addition
        template: $template
      }
    ) {
      databaseRule {
        name
        rule
        addition
        schedule
        scheduleType
        id
        template {
          id
        }
      }
    }
  }
`;

export const CREATE_RULE_TEMPLATE = gql`
  mutation createDatabaseRuleTemplate($name: String!, $rule: Iterable!) {
    createDatabaseRuleTemplate(input: { name: $name, rule: $rule }) {
      databaseRuleTemplate {
        name
        rule
        id
      }
    }
  }
`;

export const UPDATE_RULE_TEMPLATE = gql`
  mutation updateDatabaseRuleTemplate(
    $id: ID!
    $name: String!
    $rule: Iterable!
  ) {
    updateDatabaseRuleTemplate(
      input: {
      id: $id
      name: $name
      rule: $rule
      }
    ) {
      databaseRuleTemplate {
        name
        rule
        id
      }
    }
  }
`;

export const SCHEDULE_BACKUP = gql`
  mutation createDatabaseDump($db: String!) {
    createDatabaseDump(input: { db: $db, status: "scheduled" }) {
      databaseDump {
        id
      }
    }
  }
`;

export const GET_TEMPLATES = gql`
  query {
    databaseRuleTemplates {
      name
      type
      id
      platform
    }
  }
`;

export const GET_TEMPLATE = gql`
  query DatabaseRuleTemplate($id: ID!) {
    databaseRuleTemplate(id: $id) {
      name
      rule
    }
  }
`;

export const GET_SUGGESTED_RULE = gql`
  query DatabaseRuleSuggestion($id: ID!) {
    databaseRuleSuggestion(id: $id) {
      rule
    }
  }
`;
