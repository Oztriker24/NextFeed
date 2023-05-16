/* eslint-disable no-useless-escape */
export const useValidator = () => {
  const urlRegex = (url: string) => {
    const regex = /https?:\/\/(?:www\.)?([-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b)*(\/[\/\d\w\.-]*)*(?:[\?])*(.+)*/gi;
    if (url.match(regex)) {
      return true;
    }
    return false;
  };
  const emailRegex = (email: string) => {
    const regex = /^([a-z0-9]+(?:[._-][a-z0-9]+)*)@([a-z0-9]+(?:[.-][a-z0-9]+)*\.[a-z]{2,})$/i;
    if (email.match(regex)) {
      return true;
    }
    return false;
  };
  const passwordRegex = (password: string) => {
    const regex = /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/;
    if (password.match(regex)) {
      return true;
    }
    return false;
  };
  const escapeHtml = (html: string) => {
    const htmlRegexG = /<(?:"[^"]*"['"]*|'[^']*'['"]*|[^'">])+>/g;
    return html.replace(htmlRegexG, "");
  };
  return {
    urlRegex, emailRegex, passwordRegex, escapeHtml,
  };
};