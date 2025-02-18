<script setup>
import { Form, Field } from 'vee-validate';
import * as Yup from 'yup';
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';
import { useUsersStore, useAlertStore } from '@/stores';
import { router } from '@/router';

const route = useRoute();
const id = route.params.id;
const usersStore = useUsersStore();
const alertStore = useAlertStore();

let title = 'Create User';
let user = null

if (id) {
  title = 'Edit User';
  ({ user } = storeToRefs(usersStore));
  usersStore.getById(id);
}

const schema = Yup.object().shape({
    name: Yup.string()
        .required('Name is required'),
    email: Yup.string()
        .required('Email is required')
        .email(),
    document: Yup.string()
      .required('Document is required')
      .min(11, 'Document must be at least 11 characters')
      .max(14, 'Document must be maximum 14 characters'),
    password: Yup.string()
        .transform(x => x === '' ? undefined : x)
        // password optional in edit mode
        .concat(user ? null : Yup.string().required('Password is required'))
        .min(6, 'Password must be at least 6 characters')
});

async function onSubmit(values) {
    try {
        let message;
        if (values.name && values.email && values.document && id) {
          await usersStore.update(id, values);
          message = 'User updated successfully';
        } else {
          await usersStore.register(values);
          message = 'User added';
        }

        await router.push('/users');
        alertStore.success(message);
    } catch (error) {
        alertStore.error(error);
    }
}
</script>

<template>
    <h1>{{title}}</h1>
    <template v-if="!(user?.loading || user?.error)">
        <Form @submit="onSubmit" :validation-schema="schema" :initial-values="user" v-slot="{ errors, isSubmitting }">
            <div class="form-row">
                <div class="form-group col">
                    <label>Name</label>
                    <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" />
                    <div class="invalid-feedback">{{ errors.name }}</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label>Email</label>
                    <Field name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }" />
                    <div class="invalid-feedback">{{ errors.email }}</div>
                </div>
                <div class="form-group col">
                    <label>
                        Password
                        <em v-if="user">(Leave in blank to keep the same!)</em>
                    </label>
                    <Field name="password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }" />
                    <div class="invalid-feedback">{{ errors.password }}</div>
                </div>
            </div>
          <div class="form-row">
            <div class="form-group col">
              <label>Document(cpf/cnpj)</label>
              <Field name="document" type="text" class="form-control" :class="{ 'is-invalid': errors.document }" />
              <div class="invalid-feedback">{{ errors.document }}</div>
            </div>
          </div>
            <div class="form-group">
                <button class="btn btn-primary" :disabled="isSubmitting">
                    <span v-show="isSubmitting" class="spinner-border spinner-border-sm mr-1"></span>
                    Save
                </button>
                <router-link to="/users" class="btn btn-link">Cancel</router-link>
            </div>
        </Form>
    </template>
    <template v-if="user?.loading">
        <div class="text-center m-5">
            <span class="spinner-border spinner-border-lg align-center"></span>
        </div>
    </template>
    <template v-if="user?.error">
        <div class="text-center m-5">
            <div class="text-danger">Error to load Users: {{user.error}}</div>
        </div>
    </template>
</template>
