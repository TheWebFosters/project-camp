import Vue from 'vue';
import Router from 'vue-router';
import store from '../common/Store';

Vue.use(Router);

const router = new Router({
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: require('./dashboard/Dashboard'),
        },
        {
            path: '/projects',
            component: require('../common/projects/Projects'),
            children: [
                {
                    path: '/',
                    name: 'projects.list',
                    component: require('../common/projects/components/List'),
                },
                {
                    path: ':id/show',
                    name: 'projects.project-tasks.list',
                    component: require('../common/projects/components/Show'),
                    props: route => ({ propProjectId: route.params.id }),
                },

                {
                    path: ':id/invoices/create',
                    name: 'invoices.create',
                    component: require('../common/projects/invoices/Add'),
                    props: route => ({ propProjectId: route.params.id }),
                },
                {
                    path: ':project_id/invoices/:id/edit',
                    name: 'invoices.edit',
                    component: require('../common/projects/invoices/Edit'),
                    props: route => ({
                        propInvoiceId: route.params.id,
                        propProjectId: route.params.project_id,
                    }),
                },
            ],
        },
        {
            path: '/profiles',
            component: require('../common/profile/Profile'),
            children: [
                {
                    path: '/',
                    name: 'profile.list',
                    component: require('../common/profile/List'),
                },
                {
                    path: 'edit/:id',
                    name: 'profile.edit',
                    component: require('../common/profile/Edit'),
                    props: route => ({ propUserId: route.params.id }),
                },
            ],
        },
        {
            path: '/tickets',
            component: require('../common/tickets/Ticket'),
            children: [
                {
                    path: '/tickets',
                    name: 'tickets.list',
                    component: require('../common/tickets/List'),
                },
                {
                    path: ':id/show',
                    name: 'tickets.view',
                    component: require('../common/tickets/View'),
                    props: route => ({ propTicketId: route.params.id }),
                },
            ],
        },
    ],
});

router.beforeEach((to, from, next) => {
    store.commit('showLoader');
    next();
});

router.afterEach((to, from) => {
    setTimeout(() => {
        store.commit('hideLoader');
    }, 1000);
});

export default router;
