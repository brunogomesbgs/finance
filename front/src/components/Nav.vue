<script setup>
import { useAuthStore } from '@/stores';
import { storeToRefs } from 'pinia'

const authStore = useAuthStore();
const { user } = storeToRefs(authStore);
</script>

<template>
    <nav v-show="authStore.user" class="navbar navbar-expand navbar-dark bg-dark">
        <div class="navbar-nav" v-if="user">
            <router-link to="/" class="nav-item nav-link">Start</router-link>
            <router-link v-if="user.is_admin === 1" to="/users" class="nav-item nav-link">Users</router-link>
            <button @click="authStore.logout()" class="btn btn-link nav-item nav-link">Exit</button>
        </div>
    </nav>
</template>