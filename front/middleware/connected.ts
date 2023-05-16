export default defineNuxtRouteMiddleware(() => {
  const user = useAuthUser();

  if (!user.value) {
    return navigateTo("/connexion", { redirectCode: 301 });
  }
});