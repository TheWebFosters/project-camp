/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// vendor
require('../bootstrap');
window.Vue = require('vue');

// VeeValidate
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate, { validity: true, fieldsBagName: 'veeFields' });

//to use lodash in templates
Vue.prototype._ = _;

// 3rd party
import Vuetify from 'vuetify';
import Chart from 'chart.js';
moment.tz.setDefault(APP.TIMEZONE);

import 'vuetify/dist/vuetify.min.css';

//Quill Editor
import QuillEditor from 'vue-quill-editor';

// require styles
import 'quill/dist/quill.snow.css';

Vue.use(QuillEditor, {
    placeholder: 'Write here..',
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ script: 'sub' }, { script: 'super' }],
            [{ indent: '-1' }, { indent: '+1' }],
            [{ direction: 'rtl' }],
            [{ size: ['small', false, 'large', 'huge'] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ font: [] }],
            [{ color: [] }, { background: [] }],
            [{ align: [] }],
            ['clean'],
            ['link', 'image', 'video'],
        ],
    },
});

Vue.use(Vuetify, {
    rtl: APP.RTL,
    theme: {
        primary: '#1976D2',
        secondary: '#424242',
        accent: '#82B1FF',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107',
    },
});

window.accounting = require('accounting');

Vue.filter('formatDateTime', function(date) {
    if (APP.TIME_FORMAT == 12) {
        var format = APP.DATE_FORMAT.VALUE + ' h:mm A';
    } else {
        var format = APP.DATE_FORMAT.VALUE + ' h:mm';
    }

    if (date) {
        return moment(date).format(format);
    }
});

Vue.filter('formatDate', function(date) {
    if (date) {
        return moment(date).format(APP.DATE_FORMAT.VALUE);
    }
});

Vue.filter('formatMoney', function(value, symbol = null) {
    if (symbol === null) {
        symbol = ' ';
    }
    return accounting.formatMoney(value, symbol);
});

//date range picker
import Vue from 'vue';
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
Vue.use(VueFlatPickr);

flatpickr.setDefaults({
    // locale: 'Russian',
    //timezone: 'Asia/Kolkata
    locale: {
        firstDayOfWeek: APP.FIRST_DAY_OF_WEEK,
    },
});

Dropzone.autoDiscover = false;
Vue.component('moon-loader', require('vue-spinner/src/MoonLoader.vue'));
Vue.component('notification', require('../common/notification/Notification.vue'));
Vue.component('calendar', require('../common/calendar/Calendar.vue'));
Vue.component('avatar', require('../common/projects/components/Avatar.vue'));
Vue.component('quick-add-button', require('../admin/quick_add/Create.vue'));
import formatters from './Formatters';
import functions from './Functions';

Vue.use(formatters);
Vue.mixin(functions);
