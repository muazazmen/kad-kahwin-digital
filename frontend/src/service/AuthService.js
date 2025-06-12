import { ENDPOINTS } from "@/constants/api.constant";

export const googleSignIn = () => {
  const url = `${import.meta.env.VITE_API_URL}${ENDPOINTS.GOOGLE_SIGNIN}`;
  window.location.href = url;
}

export const googleSignUp = () => {
  const url = `${import.meta.env.VITE_API_URL}${ENDPOINTS.GOOGLE_SIGNUP}`;
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