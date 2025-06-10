import { METHOD } from "@/constants/method.constant";

export const apiService = async (url, method = METHOD.GET, body = null, headers = {}, params = null, retryCount = 0) => {
    let token = localStorage.getItem('accessToken');

    const defaultHeaders = {
        Authorization: token ? `Bearer ${token}` : ''
    };

    const options = {
        method,
        headers: {
            'Content-Type': 'application/json', // FIXME: check if this need to configure if multipart/form-data
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

        return response;
    } catch (error) {
        // console.error('API Error:', error.message);
        throw error;
    }
};