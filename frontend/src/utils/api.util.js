import { METHOD } from "@/constants/method.constant";

export const apiService = async (url, method = METHOD.GET, body = null, headers = {}, params = null, retryCount = 0) => {
    let token = localStorage.getItem('accessToken');

    const defaultHeaders = {
        Authorization: token ? `Bearer ${token}` : ''
    };

    const options = {
        method,
        headers: {
            ...defaultHeaders, // Preserve default headers
            ...headers // Merge additional headers
        }
    };

    if (body) {
        if (headers['Content-Type'] === 'multipart/form-data') {
            options.body = body;
            delete options.headers['Content-Type'];
        } else {
            options.body = JSON.stringify(body);
        }
    }

    if (params) {
        const queryString = new URLSearchParams(params).toString();
        url = `${url}?${queryString}`;
    }

    try {
        const response = await fetch(url, options);

        // If unauthorized and we haven't retried yet
        // if (response.status === 401 && retryCount < 2) {
        //     try {
        //         // Attempt to refresh the token
        //         const refreshResponse = await refreshToken();

        //         if (refreshResponse && refreshResponse.data.resData.accessToken) {
        //             // Update the access token
        //             localStorage.setItem('accessToken', refreshResponse.data.resData.accessToken);

        //             // Retry the original request with the new token
        //             return await apiService(url, method, body, headers, params, retryCount + 1);
        //         }
        //     } catch (error) {
        //         // If refresh fails, logout the user
        //         // await logout();
        //         return Promise.reject('Session expired. Please log in again.');
        //     }
        // }

        return response;
    } catch (error) {
        // console.error('API Error:', error.message);
        throw error;
    }
};