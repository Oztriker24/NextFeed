// eslint-disable-next-line import/no-extraneous-dependencies
import Cookies from "js-cookie";
import jwtDecode from "jwt-decode";

export const useLogin = () => {
  const apiUrl = import.meta.env.VITE_API_URL;
  const bearerToken = useCookie("token-session");
  const authUser: any = useAuthUser();
  const { newNotyf } = useNotyf();

  const decodeToken = (token: string) => jwtDecode(token);

  const setCookie = (cookieName: string, value: any) => Cookies.set(cookieName, value);

  const loginUser = async (user: any) => {
    await fetch(`${apiUrl}/user/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        username: user.email,
        password: user.password,
      }),
    })
      .then((res) => res.json())
      .then((json) => {
        setCookie("token-session", json.token);
        authUser.value = decodeToken(json.token);
        navigateTo({ path: "/" });
        newNotyf(true, "Vous êtes connecté !");
      })
      .catch((err) => {
        console.log(err);
        newNotyf(false, "Adresse mail ou mot de passe invalides !");
      });
  };
  const userInfos = async () => {
    if (bearerToken.value) {
      try {
        const { data } = await useFetch(`${apiUrl}/user/profile`, {
          headers: {
            Authorization: `Bearer ${bearerToken.value}`,
          },
        });
        authUser.value = data.value;
      } catch (error) {
        console.log(error);
      }
    }
  };
  const disconnect = () => {
    Cookies.remove("token-session");
    authUser.value = null;
    newNotyf(true, "Vous êtes deconnecté ");
    navigateTo("/connexion");
  };
  return { loginUser, userInfos, disconnect };
};