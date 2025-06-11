import { ENDPOINTS } from "@/constants/api.constant";

export const googleLogin = () => {
  const url = ENDPOINTS.GOOGLE_LOGIN;
  window.location.href = url;
}

export const googleRegister = () => {
  const url = ENDPOINTS.GOOGLE_REGISTER;
  window.location.href = url;
}

export const googleRedirect = () => {
  const url = ENDPOINTS.GOOGLE_REDIRECT;
  window.location.href = url;
}

export const googleConnect = () => {
  const url = ENDPOINTS.GOOGLE_CONNECT;
  window.location.href = url;
}