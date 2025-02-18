import { defineStore } from 'pinia';

import { fetchWrapper } from '@/helpers';
import { router } from '@/router';
import { useAlertStore } from '@/stores';

const baseUrl = `${import.meta.env.VITE_API_URL}/users`;

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        user: JSON.parse(localStorage.getItem('user')),
        returnUrl: null,
        token: JSON.parse(localStorage.getItem('token'))
    }),
    actions: {
        async login(email, password) {
            try {
                const user = await fetchWrapper.post(
                    `${baseUrl}/logIn`,
                    { email: email, password: password }
                );

                // update pinia state
                this.user = user.user;
                this.token = user.token;

                // store user details and jwt in local storage to keep user logged in between page refreshes
                localStorage.setItem('user', JSON.stringify(user.user));
                localStorage.setItem('token', JSON.stringify(user.token));

                // redirect to previous url or default to home page
                router.push(this.returnUrl || '/');
            } catch (error) {
                const alertStore = useAlertStore();
                alertStore.error(error);                
            }
        },
        logout() {
            this.user = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            router.push('/account/login');
        }
    }
});
