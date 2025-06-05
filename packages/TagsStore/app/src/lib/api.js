import axios from "axios";

const actionAPI = {
  handle: async (action, params) => {
    return await axios
      .get(import.meta.env.VITE_ACTION_URL, {
        params: {
          action,
          context: "mgr",
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

export default actionAPI;
