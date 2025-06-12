export const ENDPOINTS = {
  /* ---------------------- AUTH ---------------------- */
  GOOGLE_SIGNUP: '/api/v1/auth/google/signup',
  GOOGLE_SIGNIN: '/api/v1/auth/google/signin',
  GOOGLE_REDIRECT: '/api/v1/auth/google/redirect',
  GOOGLE_CONNECT: '/api/v1/auth/google/connect',

  /* ---------------------- ADMIN ---------------------- */
  UPDATE_ACCOUNT: '/api/v1/me/update',
  ADMIN_USERS: '/api/v1/admin/users',
  ADMIN_USER: (userId) => `/api/v1/admin/users/${userId}`,
}