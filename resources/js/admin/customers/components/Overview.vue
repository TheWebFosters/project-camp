<template>
    <v-container grid-list-md>
        <v-layout wrap>
            <v-flex xs12 sm5 md5>
                <v-card elevation="5">
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">{{ customer_data.company }}</h3>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ customer_data.mobile }}
                                    </template>

                                    <span>{{ trans('messages.mobile') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ customer_data.alternate_contact_no }}
                                    </template>

                                    <span>{{ trans('messages.alternate_num') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>email</v-icon>
                                        {{ customer_data.email }}
                                    </template>

                                    <span>{{ trans('messages.email') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>http</v-icon>
                                        {{ customer_data.website }}
                                    </template>

                                    <span>{{ trans('messages.website') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>pin_drop</v-icon>
                                        {{ customer_data.city }}, {{ customer_data.state }},
                                        {{ customer_data.country }}
                                    </template>

                                    <span
                                        >{{ trans('messages.city') }},
                                        {{ trans('messages.state') }},
                                        {{ trans('messages.country') }}</span
                                    >
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>money</v-icon>
                                        {{ currency.iso_name }}
                                        ({{ currency.symbol }})
                                    </template>

                                    <span>{{ trans('messages.currency') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>store_mall_directory</v-icon>
                                        {{ customer_data.billing_address }}
                                    </template>

                                    <span>{{ trans('messages.billing_address') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>local_shipping</v-icon>
                                        {{ customer_data.shipping_address }}
                                    </template>

                                    <span>{{ trans('messages.shipping_address') }}</span>
                                </v-tooltip>
                            </p>
                        </div>
                    </v-card-title>
                </v-card>
            </v-flex>
            <v-flex xs12 sm7 md7>
                <v-layout wrap>
                    <v-flex xs12 sm6 md6>
                        <v-card elevation="4">
                            <v-card-title>
                                <v-icon>assessment</v-icon>
                                {{ trans('messages.project_in_progress') }}
                                <v-spacer></v-spacer>
                                {{ project_counts.incompleted }}/{{ project_counts.total }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="blue-grey"
                                    height="8"
                                    :value="
                                        percentage(project_counts.incompleted, project_counts.total)
                                    "
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                    <v-flex xs12 sm6 md6>
                        <v-card elevation="4">
                            <v-card-title>
                                <v-icon>assignment</v-icon>
                                {{ trans('messages.tasks_not_finished') }}
                                <v-spacer></v-spacer>
                                {{ task_counts.incompleted }}/{{ task_counts.total }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="brown"
                                    height="8"
                                    :value="percentage(task_counts.incompleted, task_counts.total)"
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs12 sm7 md7 v-if="$can('superadmin')">
                        <v-card elevation="4">
                            <v-card-title>
                                <v-icon>receipt</v-icon>
                                {{ trans('messages.invoices_awaiting_payment') }}
                                <v-spacer></v-spacer>
                                {{ invoice_totals.total_not_paid }}/{{
                                    invoice_totals.total_counts
                                }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="deep-orange lighten-2"
                                    height="8"
                                    :value="
                                        percentage(
                                            invoice_totals.total_not_paid,
                                            invoice_totals.total_counts
                                        )
                                    "
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
                <v-layout row pt-5>
                    <v-flex xs12 sm4 md4>
                        <v-card elevation="4" height="100%">
                            <v-card-title>
                                {{
                                    _.get(invoice_counts, 'total_amount', 0)
                                        | formatMoney(selected_currency_symbol)
                                }}
                                <br />
                                {{ trans('messages.total_invoice_amount') }}
                            </v-card-title>
                        </v-card>
                    </v-flex>
                    <v-flex xs12 sm4 md4>
                        <v-card elevation="4" height="100%">
                            <v-card-title>
                                {{
                                    _.get(invoice_counts, 'paid_amount', 0)
                                        | formatMoney(selected_currency_symbol)
                                }}
                                <br />
                                {{ trans('messages.paid_invoices') }}
                            </v-card-title>
                        </v-card>
                    </v-flex>
                    <v-flex xs12 sm4 md4>
                        <v-card elevation="4" height="100%">
                            <v-card-title>
                                {{
                                    _.subtract(
                                        _.get(invoice_counts, 'total_amount', 0),
                                        _.get(invoice_counts, 'paid_amount', 0)
                                    ) | formatMoney(selected_currency_symbol)
                                }}
                                <br />
                                {{ trans('messages.due_invoices') }}
                            </v-card-title>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            customer_data: [],
            customer_id: null,
            currency: [],
            project_counts: [],
            task_counts: [],
            invoice_counts: [],
            invoice_totals: [],
            selected_currency_symbol: null,
        };
    },
    created() {
        this.customer_id = this.$route.params.id;
        this.show(this.customer_id);
        this.getDashboardDataFromApi(this.customer_id);
    },

    methods: {
        show(data) {
            const self = this;
            axios
                .get('/admin/customers/' + data)
                .then(function(response) {
                    self.customer_data = response.data;
                    self.currency = response.data.currency;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getDashboardDataFromApi(customer_id) {
            const self = this;
            axios
                .get('/admin/dashboards', { params: { customer_id: customer_id } })
                .then(function(response) {
                    self.project_counts = response.data.project_counts;
                    self.task_counts = response.data.task_counts;
                    self.invoice_counts = response.data.invoice_counts[0];
                    self.invoice_totals = response.data.invoice_totals;
                    self.paymentStatusCount(response.data.payment_status_count);
                    self.selected_currency_symbol = response.data.invoice_counts[0].currency_symbol;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        paymentStatusCount(paymentStatus) {
            var total_counts = 0;
            var total_not_paid = 0;
            var total_due = 0;
            var total_paid = 0;
            var total_partials = 0;

            _.forEach(paymentStatus, function(status) {
                total_counts = _.add(total_counts, parseInt(status.count));
                if (status.payment_status !== 'paid') {
                    total_not_paid = _.add(total_not_paid, parseInt(status.count));
                }
                if (status.payment_status == 'paid') {
                    total_paid = _.add(total_paid, parseInt(status.count));
                }
                if (status.payment_status == 'due') {
                    total_due = _.add(total_due, parseInt(status.count));
                }
                if (status.payment_status == 'partial') {
                    total_partials = _.add(total_partials, parseInt(status.count));
                }
            });
            this.invoice_totals.total_counts = total_counts;
            this.invoice_totals.total_not_paid = total_not_paid;
            this.invoice_totals.total_paid = total_paid;
            this.invoice_totals.total_due = total_due;
            this.invoice_totals.total_partials = total_partials;
        },
    },
};
</script>
