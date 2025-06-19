export const ENDPOINTS = {
    /* ---------------------- AUTH ---------------------- */
    GOOGLE_SIGNUP: '/api/v1/auth/google/signup',
    GOOGLE_SIGNIN: '/api/v1/auth/google/signin',
    GOOGLE_REDIRECT: '/api/v1/auth/google/redirect',
    GOOGLE_CONNECT: '/api/v1/auth/google/connect',

    /* ---------------------- ADMIN ---------------------- */
    // USER MANAGEMENT
    UPDATE_ACCOUNT: '/api/v1/me/update',
    ADMIN_USERS: '/api/v1/admin/users',
    ADMIN_USER: (userId) => `/api/v1/admin/users/${userId}`,
    ADMIN_USER_RESTORE: (userId) => `/api/v1/admin/users/${userId}/restore`,

    // MUSIC
    ADMIN_MUSICS: '/api/v1/admin/musics',
    ADMIN_MUSIC: (musicId) => `/api/v1/admin/musics/${musicId}`,
    ADMIN_MUSIC_RESTORE: (musicId) => `/api/v1/admin/musics/${musicId}/restore`,

    // PRAYERS
    ADMIN_PRAYERS: '/api/v1/admin/prayers',
    ADMIN_PRAYER: (prayerId) => `/api/v1/admin/prayers/${prayerId}`,
    ADMIN_PRAYER_RESTORE: (prayerId) => `/api/v1/admin/prayers/${prayerId}/restore`,

    // FRAMES
    ADMIN_FRAMES: '/api/v1/admin/frames',
    ADMIN_FRAME: (frameId) => `/api/v1/admin/frames/${frameId}`,
    ADMIN_FRAME_RESTORE: (frameId) => `/api/v1/admin/frames/${frameId}/restore`,

    /* ---------------------- SUPER ADMIN ---------------------- */
    // FONTS
    SUPER_ADMIN_FONTS: '/api/v1/super-admin/fonts',
    SUPER_ADMIN_FONT: (fontId) => `/api/v1/super-admin/fonts/${fontId}`,
    SUPER_ADMIN_FONT_RESTORE: (fontId) => `/api/v1/super-admin/fonts/${fontId}/restore`,

    // ANIMATIONS
    SUPER_ADMIN_ANIMATIONS: '/api/v1/super-admin/animations',
    SUPER_ADMIN_ANIMATION: (animationId) => `/api/v1/super-admin/animations/${animationId}`,
    SUPER_ADMIN_ANIMATION_RESTORE: (animationId) => `/api/v1/super-admin/animations/${animationId}/restore`,
};
