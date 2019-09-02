//import the common file.
require('../common/common');

// app
import router from './router';
import eventBus from '../common/Event';
import store from '../common/Store';

Vue.use(eventBus);

const admin = new Vue({
    el: '#admin',
    eventBus,
    router,
    store,
    data: () => ({
        toggleFooter: false,
    }),
    computed: {
        getBreadcrumbs() {
            return [];
        },
        showLoader() {
            return store.getters.showLoader;
        },
        showSnackbar: {
            get() {
                return store.getters.showSnackbar;
            },
            set(val) {
                if (!val) store.commit('hideSnackbar');
            },
        },
        snackbarMessage() {
            return store.getters.snackbarMessage;
        },
        snackbarColor() {
            return store.getters.snackbarColor;
        },
        snackbarDuration() {
            return store.getters.snackbarDuration;
        },

        // dialog
        showDialog: {
            get() {
                return store.getters.showDialog;
            },
            set(val) {
                if (!val) store.commit('hideDialog');
            },
        },
        dialogType() {
            return store.getters.dialogType;
        },
        dialogTitle() {
            return store.getters.dialogTitle;
        },
        dialogMessage() {
            return store.getters.dialogMessage;
        },
        dialogIcon() {
            return store.getters.dialogIcon;
        },
        drawer: {
            get: function() {
                return store.getters.drawer;
            },
            set: function() {
                return store.getters.drawerToggle;
            },
        },
    },
    methods: {
        menuClick(routeName, routeType) {
            let rn = routeType || 'vue';

            if (rn === 'vue') {
                this.$router.push({ name: routeName });
            }
            if (rn === 'full_load') {
                window.location.href = routeName;
            }
        },
        clickLogout(logoutUrl, afterLogoutRedirectUrl) {
            axios.post(logoutUrl).then(r => {
                window.location.href = afterLogoutRedirectUrl;
            });
        },
        dialogOk() {
            store.commit('dialogOk');
        },
        dialogCancel() {
            store.commit('dialogCancel');
        },
        drawerToggle() {
            store.commit('drawerToggle');
        },
        onScroll(e) {
            if (window.pageYOffset > 100) {
                this.toggleFooter = true;
            } else {
                this.toggleFooter = false;
            }
        },
    },
});
