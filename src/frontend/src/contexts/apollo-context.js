import React, { useMemo } from 'react';
import {
  ApolloClient, createHttpLink, InMemoryCache, ApolloProvider, from
} from '@apollo/client';
import { setContext } from '@apollo/client/link/context';
import { onError } from '@apollo/client/link/error';
import { useRouter } from 'next/router';
import { useCookies } from 'react-cookie';

const createApolloClient = (token, graphqlUrl, router) => {
  const httpLink = createHttpLink({ uri: graphqlUrl });

  const authLink = setContext((_, { headers }) => ({
    headers: {
      authorization: token ? `Bearer ${token}` : '',
      ...headers,
    },
  }));

  const errorLink = onError(({ networkError }) => {
    if (
      networkError
      && networkError.response?.status === 401
      && router.pathname !== '/auth/login'
    ) {
      router.replace({
        pathname: '/auth/login',
        query: router.asPath !== '/' ? { continueUrl: router.asPath } : undefined,
      });
    }
  });

  return new ApolloClient({
    link: from([authLink, errorLink, httpLink]),
    cache: new InMemoryCache(),
  });
};

export function ApolloProviderWrapper({ children }) {
  const router = useRouter();
  const [cookies] = useCookies(['jwt', 'workspace']);
  const { jwt: token, workspace } = cookies;

  const graphqlUrl = useMemo(() => {
    let url = process.env.NEXT_PUBLIC_GRAPHQL_URL ?? 'http://localhost/api/graphql';
    if (workspace) {
      url += `?workspace=${workspace}`;
    }
    return url;
  }, [workspace]);

  const client = useMemo(() => createApolloClient(token, graphqlUrl, router), [token, graphqlUrl]);

  return <ApolloProvider client={client}>{children}</ApolloProvider>;
}
