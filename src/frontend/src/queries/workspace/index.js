import { gql } from '@apollo/client';

const USER_FRAGMENT = gql`
  fragment UserFragment on User {
    id
    firstname
    lastname
    email
    groups {
      collection {
        name
        id
      }
    }
  }
`;

const CURRENT_USER_FRAGMENT = gql`
  fragment CurrentUserFragment on User {
    email
    id
    firstname
    lastname
    groups {
      collection {
        permission
      }
    }
    workspaces {
      id
      code
      name
    }
  }
`;

export const CURRENT_WORKSPACE_QUERY = gql`
  query {
    currentWorkspace {
      name
      code
      id
    }
  }
`;

export const WORKSPACE_GROUPS_QUERY = gql`
  query {
    groups {
      paginationInfo {
        totalCount
      }
      collection {
        id
        name
        permission
        databases(itemsPerPage: 100) {
          collection {
            id
          }
        }
      }
    }
  }
`;

export const CURRENT_WORKSPACE_USERS_QUERY = gql`
  query {
    currentWorkspace {
      workspace_groups {
        collection {
          name
          id
          permission
        }
      }
    }
  }
`;

export const WORKSPACE_USER_INVITATIONS = gql`
  query userInvitations($page: Int, $itemsPerPage: Int) {
    userInvitations(page: $page, itemsPerPage: $itemsPerPage, order: { created_at: "DESC" }, status_list: ["expired", "pending"]) {
      paginationInfo {
        totalCount
      }
      collection {
        id
        email
        status
        invitationGroups
        url
        expiration_date
        createdAt
      }
    }
  }
`;

export const REJECT_USER_INVITATION = gql`
  mutation updateUserInvitation($id: ID!) {
    updateUserInvitation(input: {id: $id, status: "canceled"}) {
      userInvitation {
        id
        status
      }
    }
  }
`;

export const WORKSPACE_USERS = gql`
  query users($page: Int, $itemsPerPage: Int) {
    users(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        ...UserFragment
      }
    }
  }
  ${USER_FRAGMENT}
`;

export const UPDATE_WORKSPACE = gql`
  mutation saveWorkspace($code: String!, $name: String) {
    updateWorkspace(input: { code: $code, name: $name }) {
      workspace {
        name
        code
      }
    }
  }
`;

export const USER_INVITATION_QUERY = gql`
  query userInvitation($id: ID!) {
    userInvitation(id: $id) {
      email
      id
      workspace {
        name
        code
      }
    }
  }
`;

export const CREATE_GROUP_MUTATION = gql`
  mutation createGroup($name: String!, $permission: Int!, $databases: [String]) {
    createGroup(input: { name: $name, permission: $permission, databases: $databases }) {
      group {
        name
        id
      }
    }
  }
`;

export const UPDATE_GROUP_MUTATION = gql`
  mutation updateGroup($id: ID!, $name: String, $permission: Int, $databases: [String]) {
    updateGroup(input: { id: $id, name: $name, permission: $permission, databases: $databases }) {
      group {
        id
      }
    }
  }
`;

export const UPDATE_USER_GROUPS = gql`
  mutation updateGroupUser($id: ID!, $groups: Iterable!) {
    updateGroupUser(input: { id: $id, updateGroups: $groups }) {
      user {
        groups {
          collection {
            name
          }
        }
      }
    }
  }
`;

export const DELETE_GROUP_MUTATION = gql`
  mutation deleteGroup($id: ID!) {
    deleteGroup(input: { id: $id }) {
      group {
        id
      }
    }
  }
`;

export const DELETE_USER_MUTATION = gql`
  mutation removeUser($id: ID!) {
    removeUser(input: { id: $id }) {
      user {
        id
      }
    }
  }
`;

export const LEAVE_WORKSPACE_MUTATION = gql`
  mutation($workspaceId: ID!) {
    leaveWorkspaceUser(input: {workspace: $workspaceId}) {
      user {
        id
      }
    }
  }
`;

export const DELETE_WORKSPACE_MUTATION = gql`
  mutation deleteWorkspace($id: ID!) {
    deleteWorkspace(input: {id: $id}) {
      workspace {
        id
      }
    }
  }
`;

export const UPDATE_CURRENT_USER_PASSWORD = gql`
  mutation updatePasswordUser(
    $newPassword: String!
    $confirmPassword: String!
    $oldPassword: String!
  ) {
    updatePasswordUser(
      input: {
        newPassword: $newPassword
        confirmPassword: $confirmPassword
        oldPassword: $oldPassword
      }
    ) {
      user {
        id
      }
    }
  }
`;

export const UPDATE_CURRENT_USER = gql`
  mutation updateCurrentUser($firstname: String, $lastname: String, $email: String) {
    updateCurrentUser(input: { firstname: $firstname, lastname: $lastname, email: $email }) {
      user {
        ...CurrentUserFragment
      }
    }
  }
  ${CURRENT_USER_FRAGMENT}
`;

export const DELETE_CURRENT_USER = gql`
  mutation($id: ID!) {
    removeCurrentUser(input: {id: $id}) {
      user {
        id
      }
    }
  }
`;

export const CREATE_USER_INVITATION_MUTATION = gql`
  mutation createUserInvitation($email: String!, $groups: Iterable) {
    createUserInvitation(input: { email: $email, invitationGroups: $groups }) {
      userInvitation {
        email
      }
    }
  }
`;

export const CURRENT_USER_QUERY = gql`
  query {
    currentUser {
      ...CurrentUserFragment
    }
  }
  ${CURRENT_USER_FRAGMENT}
`;

export const CURRENT_WORKSPACE_NOTIFICATIONS_QUERY = gql`
  query notifications($itemsPerPage: Int) {
    notifications(itemsPerPage: $itemsPerPage, status_list: [1]) {
      collection {
        id
        message
        level
        status
        externalUrl
      }
    }
  }
`;

export const UPDATE_NOTIFICATION = gql`
  mutation updateNotification($id: ID!, $status: Int) {
    updateNotification(input: { id: $id, status: $status}) {
      notification {
        id
      }
    }
  }
`;

export const MARK_ALL_NOTIFICATIONS_AS_READ = gql`
  mutation {
    allReadNotification(input:{}) {
      notification {
        id
      }
    }
  }
`;

const WEBHOOK_DETAILS_FRAGMENT = gql`
  fragment WebhookFragment on Webhook {
    id
    title
    domains
    operation
    status
    url
    database {
      name
      id
    }
  }
`;

export const CURRENT_WORKSPACE_WEBHOOKS = gql`
  query webhooks($page: Int, $itemsPerPage: Int) {
    webhooks(page: $page, itemsPerPage: $itemsPerPage) {
      paginationInfo {
        totalCount
      }
      collection {
        ...WebhookFragment
      }
    }
  }
  ${WEBHOOK_DETAILS_FRAGMENT}
`;

export const UPDATE_WEBHOOK = gql`
  mutation updateWebhook(
    $id: ID!
    $title: String
    $status: String
    $operation: String
    $domains: String
    $database: String
  ) {
    updateWebhook(
      input: {
        id: $id
        title: $title
        status: $status
        operation: $operation
        domains: $domains
        database: $database
      }
    ) {
      webhook {
        ...WebhookFragment
      }
    }
  }
  ${WEBHOOK_DETAILS_FRAGMENT}
`;

export const CREATE_WEBHOOK = gql`
  mutation createWebhook(
    $title: String!
    $status: String!
    $operation: String!
    $domains: String
    $database: String!
  ) {
    createWebhook(
      input: {
        title: $title
        status: $status
        operation: $operation
        domains: $domains
        database: $database
      }
    ) {
      webhook {
        ...WebhookFragment
      }
    }
  }
  ${WEBHOOK_DETAILS_FRAGMENT}
`;

export const DELETE_WEBHOOK = gql`
  mutation deleteWebhook($id: ID!) {
    deleteWebhook(input: { id: $id }) {
      webhook {
        id
      }
    }
  }
`;

export const WORKSPACE_AGGREGATION = gql`
  query {
    servers {
      paginationInfo {
        totalCount
      }
    }
    databases {
      paginationInfo {
        totalCount
      }
    }
    users {
      paginationInfo {
        totalCount
      }
    }
    notifications(status_list: [1]) {
      paginationInfo {
        totalCount
      }
    }
  }
`;

export const SEND_SUPPORT_EMAIL = gql`
  mutation sendContactEmail(
    $subject: String!,
    $message: String!
  ) {
    sendContactEmailSendContactEmail(input:{
      subject: $subject,
      message: $message
    }) {
      sendContactEmail {
        id
      }
    }
  }
`;
