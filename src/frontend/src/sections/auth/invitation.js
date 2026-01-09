import PropTypes from 'prop-types';
import React, { useState } from 'react';
import {
  Alert,
  Button,
  Typography,
} from '@mui/material';
import { useAuth } from 'src/hooks/use-auth';
import { useRouter } from 'next/router';
import { SplashScreen } from 'src/layouts/splash-screen';
import { gql, useMutation, useQuery } from '@apollo/client';
import { buildUrl } from 'src/utils/uuid';
import NextLink from 'next/link';

const USER_INVITATION_QUERY = gql`
  query userInvitation($id: ID!) {
    userInvitation(id: $id) {
      email
      id
      status
      workspace {
        name
      }
    }
  }
`;

const USER_ACCEPT_INVITATION_MUTATION = gql`
  mutation acceptUserInvitation($id: ID!) {
    acceptUserInvitation(input: { id: $id }) {
      userInvitation {
        workspace {
          code
        }
      }
    }
  }
`;

export function WorkspaceInvitation(props) {
  const { invitationId } = props;
  const { isAuthenticated, changeWorkspace } = useAuth();
  const [error, setError] = useState();
  const router = useRouter();

  const {
    loading,
    data: invitation,
  } = useQuery(USER_INVITATION_QUERY, {
    variables: {
      id: buildUrl('user_invitations', invitationId),
    },
  });

  const [acceptInvitation] = useMutation(USER_ACCEPT_INVITATION_MUTATION, {
    variables: {
      id: buildUrl('user_invitations', invitationId),
    },
  });

  const accept = () => {
    if (isAuthenticated) {
      acceptInvitation().then((result) => {
        if (result.data.acceptUserInvitation.userInvitation) {
          changeWorkspace(result.data.acceptUserInvitation.userInvitation.workspace.code).then(() => router.push('/'));
        }
      }).catch((error) => {
        setError(error.message);
      });
    } else {
      router.push(`/auth/register/?invitation=${invitationId}`);
    }
  };

  return (
    <>
      {error && <Alert color="error" severity="error">{error}</Alert>}
      {(invitation && invitation.userInvitation.status === 'expired') && <Alert color="error" severity="error">This invitations is expired</Alert>}
      {(invitation && invitation.userInvitation.status === 'canceled') && <Alert color="error" severity="error">This invitations is canceled</Alert>}
      {(invitation && invitation.userInvitation.status === 'accepted') && <Alert color="error" severity="error">This invitations is accepted</Alert>}
      {loading && <SplashScreen />}

      {(invitation && invitation.userInvitation.status === 'pending') && (
      <div>
        {!invitation.userInvitation && <Alert color="error">Invitation has expired.</Alert>}
        {invitation.userInvitation && (
        <>
          <Typography
            variant="h4"
            className="!mb-3"
          >
            You received invitation to join
            {' '}
            {invitation.userInvitation.workspace.name}
            {' '}
            workspace
          </Typography>

          <div className="text-dbm-color-3 text-sm mb-6">
            Please confirm you want to join this workspace
          </div>

          <div className="shadow-dbm-1 p-3 rounded-2xl flex gap-[22px]">
            <Button
              className="button-0 w-full"
              onClick={accept}
            >
              Accept
            </Button>

            <NextLink
              href="/auth/login"
              className="button-8 w-full"
            >
              Decline
            </NextLink>
          </div>
        </>
        )}
      </div>
      )}
    </>
  );
}

WorkspaceInvitation.propTypes = {
  invitationId: PropTypes.string,
};
