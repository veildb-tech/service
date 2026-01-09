import { gql } from '@apollo/client';

const SERVER_ITEM_FRAGMENT = gql`
  fragment ServerFragment on Server {
    name
    id
    url
    ipAddress
    status
  }
`;

export const GET_SERVER = gql`
  query Server($id: ID!) {
    server(id: $id) {
      ...ServerFragment
    }
  }
  ${SERVER_ITEM_FRAGMENT}
`;

export const GET_SERVERS = gql`
  query servers($itemsPerPage: Int, $page: Int) {
    servers(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        ...ServerFragment
      }
    }
  }
  ${SERVER_ITEM_FRAGMENT}
`;

export const UPDATE_SERVER = gql`
  mutation updateServer(
    $id: ID!
    $name: String
    $status: String
    $url: String
    $ipAddress: String
  ) {
    updateServer(
      input: { id: $id, name: $name, status: $status, url: $url, ipAddress: $ipAddress }
    ) {
      server {
        name
        status
        url
        ipAddress
        id
      }
    }
  }
`;

export const CREATE_SERVER = gql`
  mutation createServer($name: String!, $status: String!, $url: String, $ipAddress: String) {
    createServer(input: { name: $name, status: $status, url: $url, ipAddress: $ipAddress }) {
      server {
        name
        status
        url
        ipAddress
        id
      }
    }
  }
`;

export const DELETE_SERVER = gql`
  mutation deleteServer($id: ID!) {
    deleteServer(input: { id: $id }) {
      server {
        id
      }
    }
  }
`;
