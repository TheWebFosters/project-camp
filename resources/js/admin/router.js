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
            component: require('./dashboard/Home'),
            children: [
                {
                    path: '/',
                    name: 'dashboard.list',
                    component: require('./dashboard/List'),
                },
            ],
        },
        {
            path: '/employees',
            component: require('./users/Users'),
            children: [
                {
                    path: '/',
                    name: 'users.list',
                    component: require('./users/components/UserLists'),
                },
                {
                    path: 'create',
                    name: 'users.create',
                    component: require('./users/components/UserFormAdd'),
                },
                {
                    path: 'edit/:id',
                    name: 'users.edit',
                    component: require('./users/components/UserFormEdit'),
                    props: route => ({ propUserId: route.params.id }),
                },
                {
                    path: ':id/show',
                    name: 'users.view',
                    component: require('./users/components/View'),
                    props: route => ({ propUserId: route.params.id }),
                },
                {
                    path: '/roles',
                    name: 'roles.list',
                    component: require('./users/components/RoleLists'),
                },
                {
                    path: '/roles/create',
                    name: 'roles.create',
                    component: require('./users/components/RoleCreate'),
                },
                {
                    path: '/roles/edit/:id',
                    name: 'roles.edit',
                    component: require('./users/components/RoleEdit'),
                    props: route => ({ propRoleId: route.params.id }),
                },
            ],
        },
        {
            path: '/customers',
            component: require('./customers/Customers'),
            children: [
                {
                    path: '/',
                    name: 'customers.list',
                    component: require('./customers/components/List'),
                },
                {
                    path: ':id/show',
                    name: 'customers.show',
                    component: require('./customers/components/Show'),
                },
                {
                    path: ':id/contacts',
                    name: 'customers.contacts.list',
                    component: require('./customers/components/contacts/List'),
                    props: route => ({ propCustomerId: route.params.id }),
                },
                {
                    path: ':id/notes',
                    name: 'customers.notes.list',
                    component: require('./customers/components/notes/List'),
                    props: route => ({ propCustomerId: route.params.id }),
                },
            ],
        },
        {
            path: '/project',
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
                    props: route => ({
                        propProjectId: route.params.id,
                    }),
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
            path: '/tasks',
            component: require('../common/tasks/Tasks'),
            children: [
                {
                    path: '/',
                    name: 'tasks.list',
                    component: require('../common/tasks/List'),
                },
            ],
        },

        {
            path: '/settings',
            component: require('./settings/Settings'),
            children: [
                {
                    path: '/',
                    name: 'settings.create',
                    component: require('./settings/Create'),
                },
            ],
        },

        {
            path: '/backups',
            component: require('./backup/Backup'),
            children: [
                {
                    path: '/',
                    name: 'backups.list',
                    component: require('./backup/List'),
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
            path: '/projects',
            component: require('./invoices/Invoices'),
            children: [
                {
                    path: '/invoice',
                    name: 'invoices.list',
                    component: require('./invoices/List'),
                },
                {
                    path: '/invoices/create',
                    name: 'sales.invoices.create',
                    component: require('../common/projects/invoices/Add'),
                },
                {
                    path: '/estimates',
                    name: 'estimates.list',
                    component: require('./invoices/estimates/List'),
                },
                {
                    path: '/estimates/create',
                    name: 'estimates.create',
                    component: require('../common/projects/invoices/Add'),
                },
                {
                    path: '/invoice-scheme',
                    name: 'invoice_scheme.list',
                    component: require('../admin/invoice_schemes/List'),
                },
            ],
        },
        {
            path: '/knowledge-base',
            component: require('./knowledgebase/KnowledgeBase'),
            children: [
                {
                    path: '/knowledge-base',
                    name: 'Knowledgebase.list',
                    component: require('./knowledgebase/List'),
                },
                {
                    path: '/knowledge-base/create',
                    name: 'Knowledgebase.create',
                    component: require('./knowledgebase/Create'),
                },
                {
                    path: '/knowledge-base/:id/edit',
                    name: 'Knowledgebase.edit',
                    component: require('./knowledgebase/Edit'),
                    props: route => ({ propKnowladgebaseId: route.params.id }),
                },
                {
                    path: '/knowledge-base/:id/view',
                    name: 'Knowledgebase.view',
                    component: require('./knowledgebase/View'),
                    props: route => ({ propKnowladgebaseId: route.params.id }),
                },
            ],
        },
        {
            path: '/expenses',
            component: require('./expenses/Expenses'),
            children: [
                {
                    path: '/expenses',
                    name: 'expenses.list',
                    component: require('./expenses/List'),
                },
            ],
        },
        {
            path: '/leaves',
            component: require('./leaves/Leaves'),
            children: [
                {
                    path: '/leaves',
                    name: 'leaves.list',
                    component: require('./leaves/List'),
                },
            ],
        },
        {
            path: '/leads',
            component: require('./leads/Leads'),
            children: [
                {
                    path: '/leads',
                    name: 'leads.list',
                    component: require('./leads/List'),
                },
                {
                    path: ':id/view',
                    name: 'lead.show',
                    component: require('./leads/Tab'),
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
