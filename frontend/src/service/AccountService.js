import { ENDPOINTS } from "@/constants/api.constant";
import { METHOD } from "@/constants/method.constant";
import { apiService } from "@/utils/api.util";

export const updateAccount = async (formData) => {
  return await apiService(ENDPOINTS.UPDATE_ACCOUNT, METHOD.POST, formData,
      { 'Content-Type': 'multipart/form-data' }
    );
};
