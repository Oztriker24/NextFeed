export default defineNuxtPlugin(async () => {
  const { userInfos } = useLogin();
  await userInfos();
});