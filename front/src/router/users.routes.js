import { Layout, List, User } from '@/views/users';

export default {
    path: '/users',
    component: Layout,
    children: [
        { path: '', component: List },
        { path: 'add', component: User },
        { path: 'update/:id', component: User }
    ]
};
