import { actionAPI } from "./api";

export const ControlRules = async (action, categoryId, context_key, data) => {
  const response = await actionAPI.handle(action, {
    context: "mgr",
    category_id: categoryId,
    context_key: context_key,
    data,
  });

  return response;
};
