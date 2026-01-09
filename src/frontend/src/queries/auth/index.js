import { gql } from '@apollo/client';

export const FORGOT_PASSWORD_MUTATION = gql`
  mutation sendEmailWithHashUserRestore($email: String!) {
    sendEmailWithHashUserRestore(
      input: {email: $email}
    ) {
    userRestore {
        email,
        createdAt,
        expiredAt,
        status
      }
    }
  }
`;

export const CHECK_RESTORE_PASSWORD_HASH_MUTATION = gql`
mutation checkHashUserRestore($hash: String!) {
checkHashUserRestore(
  input: {
    hash: $hash
  }
  ) {
    userRestore {
      email,
      createdAt,
      expiredAt,
      status
    }
  }
}
`;

export const RESTORE_PASSWORD_MUTATION = gql`
  mutation restorePasswordUserRestore(
    $newPassword: String!,
    $confirmPassword: String!,
    $hash: String!
  ) {
    restorePasswordUserRestore(
      input: {
        hash: $hash,
        newPassword: $newPassword,
        confirmPassword: $confirmPassword
      }
    ) {
      userRestore {
        email,
        createdAt,
        expiredAt,
        status
      }
    }
  }
`;

export const CREATE_USER_FROM_INVITATION_MUTATION = gql`
  mutation createUser(
    $email: String!
    $firstname: String!
    $lastname: String!
    $password: String!
    $invitation: String
  ) {
    createUser(
      input: {
        email: $email
        firstname: $firstname
        lastname: $lastname
        password: $password
        invitation: $invitation
      }
    ) {
      user {
        id
      }
    }
  }
`;

export const CREATE_USER_MUTATION = gql`
  mutation createUser(
    $email: String!
    $firstname: String!
    $lastname: String!
    $password: String!
    $workspaceName: String
  ) {
    createUser(
      input: {
        email: $email
        firstname: $firstname
        lastname: $lastname
        password: $password
        workspaces: { name: $workspaceName }
      }
    ) {
      user {
        id
      }
    }
  }
`;
