import React, {
  createContext, useContext, useEffect, useReducer, useRef,
} from 'react';
import PropTypes from 'prop-types';
import { useLazyQuery, useMutation } from '@apollo/client';
import { useRouter } from 'next/navigation';
import { useCookies } from 'react-cookie';
import {
  CREATE_USER_FROM_INVITATION_MUTATION,
  CREATE_USER_MUTATION,
  CURRENT_USER_QUERY,
} from 'src/queries';

const HANDLERS = {
  INITIALIZE: 'INITIALIZE',
  SIGN_IN: 'SIGN_IN',
  RELOAD: 'RELOAD',
  SIGN_OUT: 'SIGN_OUT',
};

const initialState = {
  isAuthenticated: false,
  isLoading: true,
  user: null,
};

const handlers = {
  [HANDLERS.INITIALIZE]: (state, action) => {
    const user = action.payload;

    return {
      ...state,
      ...(user
        ? {
          isAuthenticated: true,
          isLoading: false,
          user,
        }
        : {
          isLoading: false,
        }),
    };
  },
  [HANDLERS.SIGN_IN]: (state, action) => {
    const user = action.payload;
    localStorage.setItem('user', JSON.stringify(user));

    return {
      ...state,
      isAuthenticated: true,
      user,
    };
  },
  [HANDLERS.RELOAD]: (state, action) => {
    const user = action.payload;
    localStorage.setItem('user', JSON.stringify(user));

    return {
      ...state,
      isAuthenticated: true,
      user,
    };
  },
  [HANDLERS.SIGN_OUT]: (state) => ({
    ...state,
    isAuthenticated: false,
    user: null,
  }),
};

const reducer = (state, action) => (handlers[action.type] ? handlers[action.type](state, action) : state);

export const AuthContext = createContext({ undefined });

export function AuthProvider(props) {
  const router = useRouter();
  const [cookies, setCookie, removeCookie] = useCookies(['jwt', 'workspace', 'refresh_jwt']);

  const [loadCurrentUser] = useLazyQuery(CURRENT_USER_QUERY, {
    fetchPolicy: 'no-cache',
  });

  const [createUser] = useMutation(CREATE_USER_MUTATION);
  const [createUserFromInvitation] = useMutation(CREATE_USER_FROM_INVITATION_MUTATION);

  const { children } = props;
  const [state, dispatch] = useReducer(reducer, initialState);
  const initialized = useRef(false, [cookies]);

  const initialize = async () => {
    // Prevent from calling twice in development mode with React.StrictMode enabled
    if (initialized.current) {
      return;
    }

    initialized.current = true;

    let isAuthenticated = false;
    let user = {};

    try {
      isAuthenticated = cookies.jwt;
      user = JSON.parse(localStorage.getItem('user'));
    } catch (err) {
      console.error(err);
    }

    if (isAuthenticated) {
      dispatch({
        type: HANDLERS.INITIALIZE,
        payload: user,
      });
    } else {
      dispatch({
        type: HANDLERS.INITIALIZE,
      });
    }
  };

  useEffect(
    () => {
      initialize();
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [],
  );

  const reload = (user) => {
    dispatch({
      type: HANDLERS.RELOAD,
      payload: user,
    });
  };

  const signIn = async (email, password) => {
    const authUrl = process.env.NEXT_PUBLIC_LOGIN_URL;
    if (!authUrl) {
      throw new Error('Login url is missing. Set NEXT_PUBLIC_LOGIN_URL variable in .env');
    }

    localStorage.removeItem('user');

    await fetch(authUrl, {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify({
        username: email,
        password,
      }),
    })
      .then((result) => result.json())
      .then((response) => {
        if (response.code === 401) {
          throw new Error('Please check your email and password');
        } else {
          if (!response.token) {
            throw new Error('Something went wrong with authorization.');
          }

          try {
            setCookie('jwt', response.token, { path: '/' });
            setCookie('refresh_jwt', response.refresh_token, { path: '/' });
            setCookie('workspace', response.workspace, { path: '/' });
            loadCurrentUser({
              context: {
                headers: {
                  authorization: `Bearer ${response.token}`,
                },
              },
            }).then((result) => {
              dispatch({
                type: HANDLERS.SIGN_IN,
                payload: result.data.currentUser,
              });

              router.push('/');
            });
          } catch (err) {
            console.error(err);
          }
        }
      });
  };

  const changeWorkspace = async (workspace) => {
    const refreshUrl = process.env.NEXT_PUBLIC_REFRESH_URL;
    if (!refreshUrl) {
      throw new Error('Login url is missing. Set NEXT_PUBLIC_LOGIN_URL variable in .env');
    }

    const refreshToken = cookies.refresh_jwt;

    await fetch(refreshUrl, {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify({
        refresh_token: refreshToken,
        workspace
      }),
    })
      .then((result) => result.json())
      .then((response) => {
        if (!response.token) {
          throw new Error('Something went wrong with authorization.');
        }

        try {
          setCookie('jwt', response.token, { path: '/' });
          setCookie('refresh_jwt', response.refresh_token, { path: '/' });
          setCookie('workspace', workspace);

          loadCurrentUser({
            context: {
              headers: {
                authorization: `Bearer ${response.token}`,
              },
            },
          }).then((result) => {
            dispatch({
              type: HANDLERS.RELOAD,
              payload: result.data.currentUser,
            });

            router.push('/');
          });
        } catch (err) {
          console.error(err);
        }
      });
  };

  const signUp = async (
    firstname,
    lastname,
    password,
    email,
    workspaceName,
    invitationId = false,
  ) => {
    if (invitationId) {
      // eslint-disable-next-line no-param-reassign
      workspaceName = null;
    }

    if (invitationId) {
      return createUserFromInvitation({
        variables: {
          firstname,
          lastname,
          password,
          email,
          invitation: invitationId,
        },
      });
    }
    return createUser({
      variables: {
        firstname,
        lastname,
        password,
        email,
        workspaceName,
      },
    });
  };

  const signOut = () => {
    localStorage.removeItem('user');

    removeCookie('jwt', { path: '/' });
    removeCookie('workspace', { path: '/' });

    dispatch({
      type: HANDLERS.SIGN_OUT,
    });
  };

  return (
    <AuthContext.Provider
      value={{
        ...state,
        reload,
        signIn,
        signUp,
        signOut,
        changeWorkspace
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

AuthProvider.propTypes = {
  children: PropTypes.node,
};

export const AuthConsumer = AuthContext.Consumer;

export const useAuthContext = () => useContext(AuthContext);
