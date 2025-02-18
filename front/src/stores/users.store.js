import { defineStore } from 'pinia';

import { fetchWrapper } from '@/helpers';
import { router } from '@/router/index.js'

const baseUrl = `${import.meta.env.VITE_API_URL}/users`;

export const useUsersStore = defineStore({
    id: 'users',
    state: () => ({
        users: {},
        user: {},
        token: JSON.parse(localStorage.getItem('token'))
    }),
    actions: {
        async register(user) {
            this.user = { loading:true }
            try {
                await fetchWrapper.post(
                  `${baseUrl}/`,
                  { name: user.name, email: user.email, password: user.password, document: user.document }
                );
            } catch (error) {
                this.user = { error }
            }
        },
        async createInvestorAccount(user) {
            this.user = { loading:true }
            try {
                await fetchWrapper.post(
                  `${baseUrl}/createAccount`,
                  { name: user.name, email: user.email, password: user.password, document: user.document }
                );
            } catch (error) {
                this.user = { error }
            }
        },
        async getAll() {
            this.users = { loading: true };
            try {
                const response = await fetch(
                  `${baseUrl}`,
                {
                      headers: {
                          'Content-Type': 'application/json',
                          Authorization: `Bearer ${this.token}`
                      }
                    }
                );
                this.users = await response.json();
            } catch (error) {
                this.users = { error };
            }
        },
        async delete(value) {
            this.users = { loading: true };
            try {
                await fetchWrapper.delete(`${baseUrl}/${value}`, {});
                this.users = null;
                localStorage.removeItem('users');
                await router.push('/');
            } catch (error) {
                this.users = { error };
            }
        },
        async update(id, value) {
            this.users = { loading: true };
            try {
                await fetchWrapper.put(
                  `${baseUrl}/${id}`,
                  { name: value.name, email:value.email, document: value.document });

                this.users = null;
            } catch (error) {
                this.users = { error };
            }
        },
        async getById(id) {
            this.user = { loading: true };
            try {
                const response = await fetch(
                  `${baseUrl}/${id}`,
                {
                      headers: {
                          'Content-Type': 'application/json',
                          Authorization: `Bearer ${this.token}`
                      }
                    }
                  )
                this.user = await response.json();
            } catch (error) {
                this.user = { error };
            }
        }
    }
});
