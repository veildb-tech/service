export const getUuidFromRelation = function (relation) {
  const split = relation.split('/');
  return split[split.length - 1];
};

export const buildUrl = function (entity, uuid) {
  return `/api/${entity}/${uuid}`;
};
