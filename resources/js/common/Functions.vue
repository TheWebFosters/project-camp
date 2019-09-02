<script>
export default {
    methods: {
        $can(permissionName) {
            return _.get(
                APP.USER_PERMISSIONS,
                'superadmin',
                _.get(APP.USER_PERMISSIONS, permissionName, false)
            );
        },
        $hasRole(roleName) {
            return _.get(APP.USER_ROLES, 'superadmin', _.get(APP.USER_ROLES, roleName, false));
        },
        trans(string, params = []) {
            var str = _.get(window.i18n, string);

            _.forEach(params, function(value, key) {
                str = _.replace(str, ':' + key, value);
            });

            return str;
        },
        percentage(value, total) {
            return (value / total) * 100;
        },
        projectProgress(totalTask, completedtask) {
            var total_project_work = 0;
            if (totalTask > 0 && completedtask > 0) {
                total_project_work = (completedtask / totalTask) * 100;
            } else {
                total_project_work = 0;
            }

            return _.floor(total_project_work);
        },
        goBack(goBackByStep) {
            //(-ve  => go back/ +ve => go forward)
            var step = goBackByStep || -1;
            setTimeout(() => {
                this.$router.go(step);
            }, 3000);
        },
        defaultDateRange() {
            const self = this;
            var dateRange = [
                {
                    label: self.trans('messages.today'),
                    range: [moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD')],
                },
                {
                    label: self.trans('messages.yesterday'),
                    range: [
                        moment()
                            .subtract(1, 'days')
                            .format('YYYY-MM-DD'),
                        moment()
                            .subtract(1, 'days')
                            .format('YYYY-MM-DD'),
                    ],
                },
                {
                    label: self.trans('messages.last_7_days'),
                    range: [
                        moment()
                            .subtract(6, 'days')
                            .format('YYYY-MM-DD'),
                        moment().format('YYYY-MM-DD'),
                    ],
                },
                {
                    label: self.trans('messages.last_30_days'),
                    range: [
                        moment()
                            .subtract(29, 'days')
                            .format('YYYY-MM-DD'),
                        moment().format('YYYY-MM-DD'),
                    ],
                },
                {
                    label: self.trans('messages.this_month'),
                    range: [
                        moment()
                            .startOf('month')
                            .format('YYYY-MM-DD'),
                        moment()
                            .endOf('month')
                            .format('YYYY-MM-DD'),
                    ],
                },
                {
                    label: self.trans('messages.last_month'),
                    range: [
                        moment()
                            .subtract(1, 'month')
                            .startOf('month')
                            .format('YYYY-MM-DD'),
                        moment()
                            .subtract(1, 'month')
                            .endOf('month')
                            .format('YYYY-MM-DD'),
                    ],
                },
                {
                    label: self.trans('messages.this_year'),
                    range: [
                        moment()
                            .startOf('year')
                            .format('YYYY-MM-DD'),
                        moment()
                            .endOf('year')
                            .format('YYYY-MM-DD'),
                    ],
                },
            ];

            return dateRange;
        },
        flatPickerDate() {
            var config = {
                altInput: true,
                altFormat: APP.DATE_FORMAT.KEY,
            };

            return config;
        },
        flatPickerDateTime() {
            var format = '';
            var enable_24_hr = false;

            if (APP.TIME_FORMAT == 12) {
                format = APP.DATE_FORMAT.KEY + ' h:i K';
            } else {
                format = APP.DATE_FORMAT.KEY + ' H:i';
                enable_24_hr = true;
            }

            var config = {
                altInput: true,
                altFormat: format,
                enableTime: true,
                time_24hr: enable_24_hr,
            };

            return config;
        },
    },
};
</script>
