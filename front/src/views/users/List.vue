<script setup>
import { storeToRefs } from 'pinia';
import { useUsersStore } from '@/stores';

const usersStore = useUsersStore();
const { users } = storeToRefs(usersStore);

usersStore.getAll();
</script>

<template>
    <h1>Users</h1>
    <router-link to="/users/add" class="btn btn-sm btn-success mb-2">Create User</router-link>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 30%">Name</th>
                <th style="width: 30%">Email</th>
                <th style="width: 30%"></th>
            </tr>
        </thead>
        <tbody>
            <template v-if="users.length">
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                  <td style="white-space: nowrap">
                    <router-link :to="`/users/update/${user.id}`" class="btn btn-sm btn-warning mr-1">Edit</router-link>

                    <button @click="usersStore.delete(user.id)" class="btn btn-sm btn-danger btn-delete-user" :disabled="user.isDeleting">
                      <span v-if="user.isDeleting" class="spinner-border spinner-border-sm"></span>
                      <span v-else>Delete</span>
                    </button>
                  </td>
                </tr>
            </template>
            <tr v-if="users.loading">
                <td colspan="3" class="text-center">
                    <span class="spinner-border spinner-border-lg align-center"></span>
                </td>
            </tr>
            <tr v-if="users.error">
                <td colspan="3">
                    <div class="text-danger">Error to load Users: {{users.error}}</div>
                </td>
            </tr>            
        </tbody>
    </table>
</template>
