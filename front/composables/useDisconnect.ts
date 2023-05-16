// eslint-disable-next-line import/no-extraneous-dependencies
import Cookies from "js-cookie";

export const useDisconnect = () => {
  Cookies.remove("token-session");
  navigateTo({ path: "/connexion" });
};