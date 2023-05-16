import { Notyf } from "notyf";
import "notyf/notyf.min.css";

export const useNotyf = () => {
  const newNotyf = (result: boolean, message: string) => {
    if (process.client) {
      const notyf = new Notyf();
      if (result) {
        notyf.success(message);
      } else {
        notyf.error(message);
      }
    }
  };
  return { newNotyf };
};
