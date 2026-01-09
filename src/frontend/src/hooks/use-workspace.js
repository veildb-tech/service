import { useCookies } from 'react-cookie';
import { useRouter } from 'next/router';

export const useWorkspace = () => {
  const [cookies, setCookie] = useCookies(['workspace']);
  const router = useRouter();

  const setCurrentWorkspace = (workspaceCode) => {
    setCookie('workspace', workspaceCode, { path: '/' });
    router.push(`/${workspaceCode}`);
  };

  const getCurrentWorkspaceCode = () => cookies.workspace;

  return {
    setCurrentWorkspace,
    getCurrentWorkspaceCode,
  };
};
