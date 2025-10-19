import { ENDPOINTS } from "@/constants/api.constant";
import { METHOD } from "@/constants/method.constant";
import { apiService } from "@/utils/api.util";

export const getThemes = async (page = 1, perPage = 10) => { 
  const params = {
    page: page,
    per_page: perPage
  }
  return await apiService(ENDPOINTS.THEMES, METHOD.GET, null, {}, params);
}

export const getThemesWithTrashed = async (page = 1, perPage = 10) => {
  const params = {
    page: page,
    per_page: perPage
  };
  return await apiService(ENDPOINTS.ADMIN_THEMES, METHOD.GET, null, {}, params);
}

export const getThemeById = async (themeId) => {
  return await apiService(ENDPOINTS.ADMIN_THEME(themeId), METHOD.GET);
}

export const addTheme = async (data) => {
  return await apiService(ENDPOINTS.ADMIN_THEMES, METHOD.POST, data);
}

export const updateTheme = async (themeId, data) => {
  return await apiService(ENDPOINTS.ADMIN_THEME(themeId), METHOD.PUT, data);
}

export const deleteTheme = async (themeId) => {
  return await apiService(ENDPOINTS.ADMIN_THEME(themeId), METHOD.DELETE);
}

export const restoreTheme = async (themeId) => {
  return await apiService(ENDPOINTS.ADMIN_THEME_RESTORE(themeId), METHOD.PUT);
}