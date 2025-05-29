import axios from "axios";

export const actionAPI = {
  handle: async (action, params) => {
    return await axios
      .get(import.meta.env.VITE_ACTION_URL, {
        params: {
          action,
          ...params,
        },
      })
      .then(function (response) {
        return response.data;
      })
      .catch((error) => {
        console.error(error);
        return false;
      });
  },
};
