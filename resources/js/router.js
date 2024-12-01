import Vue from 'vue';
import Router from 'vue-router';
import Index from './components/Index.vue';
import AddContact from './components/AddContact.vue';
import History from "./components/History.vue";
import NotFound from "./components/NotFound.vue";

Vue.use(Router);

const routes = [
    {
        path: '/',
        name: 'home',
        component: Index,
    },
    {
        path: '/add-contact',
        name: 'add_contact',
        component: AddContact,
    },
    {
        path: '/history',
        name: 'history',
        component: History,
    },
    {
        path: '/:catchAll(.*)',
        name: 404,
        component: NotFound
    }
];

const router = new Router({
    mode: 'history',
    routes,
});

export default router;
