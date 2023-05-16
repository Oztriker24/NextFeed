// eslint-disable-next-line import/no-extraneous-dependencies
import dayjs from "dayjs";
// eslint-disable-next-line import/no-extraneous-dependencies
import localfr from "dayjs/locale/fr";

export const useDate = () => {
  const convertDateFormat = (date: string) => dayjs(date).locale(localfr).format("DD/MM/YYYY");

  return { convertDateFormat };
};