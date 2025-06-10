export const ENDPOINTS = {
  UPDATE_ACCOUNT: '/api/v1/me/update',
  ADMIN_USERS: '/api/v1/admin/users',
  ADMIN_USER: (userId) => `/api/v1/admin/users/${userId}`,
}