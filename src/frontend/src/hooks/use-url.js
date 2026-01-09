export const useUrl = () => {
  const getUrl = (path) => `/${path.replace(/^\/|\/$/g, '')}`;

  return {
    getUrl,
  };
};
