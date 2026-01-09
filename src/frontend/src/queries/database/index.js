import { gql } from '@apollo/client';

const DATABASE_ITEM_FRAGMENT = gql`
  fragment DatabaseFragment on Database {
    name
    id
    engine
    status
    platform
    updated_at
    created_at
  }
`;

export const GET_DATABASES = gql`
  query databases($itemsPerPage: Int, $page: Int) {
    databases(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        ...DatabaseFragment
        server {
          name
        }
      }
    }
  }
  ${DATABASE_ITEM_FRAGMENT}
`;

export const GET_RULE_DATABASES = gql`
  query databases($itemsPerPage: Int, $page: Int) {
    databases(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        ...DatabaseFragment
        databaseRule{
          id
        }
      }
    }
  }
  ${DATABASE_ITEM_FRAGMENT}
`;

export const GET_DATABASE = gql`
  query Database($id: ID!) {
    database(id: $id) {
      ...DatabaseFragment
      databaseDumpDeleteRules {
        id
        rule
      }
      databaseRule {
        id
      }
      groups {
        collection {
          name
          id
        }
      }
      databaseDumps {
        edges {
          node {
            filename
            status
            created_at
            id
          }
        }
      }
      server {
        name
      }
    }
  }
  ${DATABASE_ITEM_FRAGMENT}
`;

export const DELETE_DATABASE = gql`
  mutation deleteDatabaseInput($id: ID!) {
    deleteDatabase(input: { id: $id }) {
      database {
        id
      }
    }
  }
`;

export const UPDATE_DATABASE = gql`
  mutation updateDatabase($id: ID!, $name: String, $status: String, $engine: String, $groups: [String]) {
    updateDatabase(input: { id: $id, name: $name, status: $status, engine: $engine, groups: $groups }) {
      database {
        name
        status
        engine
        id
        groups {
          collection {
            name
            id
          }
        }
      }
    }
  }
`;

export const GET_DUMP = gql`
  query DatabaseDump($id: ID!) {
    databaseDump(id: $id) {
      filename
      status
      id
      created_at
      databaseDumpLogs {
        id
        status
        message
        createdAt
      }
    }
  }
`;

export const CREATE_DATABASE_DUMP_DELETE_RULE = gql`
  mutation createDatabaseDumpDeleteRules($name: String!, $status: Boolean!, $db: String!, $rule: Iterable) {
    createDatabaseDumpDeleteRules(input: {
      name: $name,
      db: $db,
      status: $status,
      rule: $rule
    }) {
      databaseDumpDeleteRules {
        id
      }
    }
  }
`;

export const UPDATE_DATABASE_DUMP_DELETE_RULE = gql`
  mutation updateDatabaseDumpDeleteRules(
    $id: ID!,
    $rule: Iterable,
    $name: String!,
    $status: Boolean!,
    $db: String!
  ) {
    updateDatabaseDumpDeleteRules(input: {
      id: $id,
      name: $name,
      db: $db,
      status: $status,
      rule: $rule
    }) {
      databaseDumpDeleteRules {
        id
      }
    }
  }
`;
