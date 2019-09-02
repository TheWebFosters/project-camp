<template>
    <div class="component-wrap">
        <v-container grid-list-md>
            <v-layout row-wrap mb-2>
                <v-flex xs12 sm4 md4>
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
                <v-flex xs12 sm4 md4>
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
                <v-flex xs12 sm4 md4 v-if="$can('superadmin')">
                    <v-card elevation="4">
                        <v-card-title>
                            <v-icon>receipt</v-icon>
                            {{ trans('messages.invoices_awaiting_payment') }}
                            <v-spacer></v-spacer>
                            {{ invoice_totals.total_not_paid }}/{{ invoice_totals.total_counts }}
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
            <v-layout row-wrap v-if="$can('superadmin')" mb-2>
                <v-flex xs12 sm12 md12>
                    <v-card elevation="4">
                        <v-card-title>
                            <span class="headline">
                                <v-icon>receipt</v-icon>
                                {{ trans('messages.invoice_overview') }}
                            </span>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-container grid-list-lg>
                                <v-layout wrap>
                                    <v-flex xs12 sm12 md5>
                                        <v-flex xs12 sm12 md12>
                                            {{ _.get(invoice_totals, 'total_paid', 0) }}
                                            {{ trans('messages.paid') }}
                                            <v-progress-linear
                                                color="green lighten-1"
                                                height="8"
                                                :value="
                                                    percentage(
                                                        invoice_totals.total_paid,
                                                        invoice_totals.total_counts
                                                    )
                                                "
                                            ></v-progress-linear>
                                        </v-flex>
                                        <v-flex xs12 sm12 md12>
                                            {{ _.get(invoice_totals, 'total_partials', 0) }}
                                            {{ trans('messages.partial') }}
                                            <v-progress-linear
                                                color="cyan lighten-2"
                                                height="8"
                                                :value="
                                                    percentage(
                                                        invoice_totals.total_partials,
                                                        invoice_totals.total_counts
                                                    )
                                                "
                                            ></v-progress-linear>
                                        </v-flex>
                                        <v-flex xs12 sm12 md12>
                                            {{ _.get(invoice_totals, 'total_due', 0) }}
                                            {{ trans('messages.due') }}
                                            <v-progress-linear
                                                color="red accent-2"
                                                height="8"
                                                :value="
                                                    percentage(
                                                        invoice_totals.total_due,
                                                        invoice_totals.total_counts
                                                    )
                                                "
                                            ></v-progress-linear>
                                        </v-flex>
                                    </v-flex>
                                    <v-flex xs12 sm12 md7>
                                        <v-layout wrap>
                                            <v-flex xs12>
                                                <v-flex
                                                    xs12
                                                    sm7
                                                    d-flex
                                                    v-if="currencies.length > 1"
                                                >
                                                    <v-autocomplete
                                                        prepend-icon="money"
                                                        v-model="selected_currency"
                                                        :items="currencies"
                                                        item-text="name"
                                                        item-value="id"
                                                        @change="currencyChanged"
                                                    ></v-autocomplete>
                                                </v-flex>
                                            </v-flex>
                                            <v-flex xs12 sm4 md4>
                                                <v-card elevation="4" height="100%">
                                                    <v-card-title>
                                                        {{
                                                            total_amount
                                                                | formatMoney(
                                                                    selected_currency_symbol
                                                                )
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
                                                            invoice_paid_amount
                                                                | formatMoney(
                                                                    selected_currency_symbol
                                                                )
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
                                                                total_amount,
                                                                invoice_paid_amount
                                                            )
                                                                | formatMoney(
                                                                    selected_currency_symbol
                                                                )
                                                        }}
                                                        <br />
                                                        {{ trans('messages.due_invoices') }}
                                                    </v-card-title>
                                                </v-card>
                                            </v-flex>
                                            <v-flex
                                                xs12
                                                sm8
                                                md8
                                                v-if="selected_currency == business_currency.id"
                                            >
                                                <v-card elevation="4" height="100%">
                                                    <v-card-title>
                                                        {{
                                                            invoice_totals.business_currency_amount
                                                                | formatMoney(
                                                                    selected_currency_symbol
                                                                )
                                                        }}
                                                        <br />
                                                        {{
                                                            trans(
                                                                'messages.paid_invoices_in_business_currency'
                                                            )
                                                        }}
                                                    </v-card-title>
                                                </v-card>
                                            </v-flex>
                                        </v-layout>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
            <v-layout row wrap>
                <v-flex xs12 sm4 md4>
                    <v-card elevation="4">
                        <v-card-title primary-title>
                            <span class="headline">
                                {{ trans('messages.ticket_overview') }}
                            </span>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <canvas id="ticketPieChart" width="50px" height="50px"></canvas>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm4 md4 v-if="$can('superadmin')">
                    <v-card elevation="4">
                        <v-card-title primary-title>
                            <span class="headline">
                                {{ trans('messages.invoice_paid_versus_expense') }}
                            </span>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <canvas id="transactionPieChart" width="50px" height="50px"></canvas>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm4 md4>
                    <v-card color="yellow lighten-3" height="394">
                        <v-card-title primary-title>
                            <div>
                                <h3 class="title mb-2">{{ trans('messages.sticky_notes') }}</h3>
                                <div>
                                    <v-textarea
                                        solo
                                        clearable
                                        cols="70"
                                        rows="15"
                                        v-model="sticky_notes"
                                        @change="saveStickyNotes"
                                        @click:clear="saveStickyNotes"
                                    ></v-textarea>
                                </div>
                            </div>
                        </v-card-title>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>
<script>
//import Tab from '../dashboard/Tab';
export default {
    components: {
        // Tab,
    },
    data() {
        return {
            project_counts: [],
            task_counts: [],
            invoice_counts: [],
            invoice_paid_amount: null,
            invoice_totals: [],
            currencies: [],
            currency_symbols: [],
            selected_currency: null,
            total_amount: null,
            selected_currency_symbol: null,
            business_currency: [],
            sticky_notes: null,
        };
    },
    mounted() {
        const self = this;
        self.getDashboardDataFromApi();
        self.$eventBus.$on('updateDashboard', data => {
            self.getDashboardDataFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateDashboard');
    },
    methods: {
        getDashboardDataFromApi() {
            const self = this;
            axios
                .get('/admin/dashboards')
                .then(function(response) {
                    self.project_counts = response.data.project_counts;
                    self.task_counts = response.data.task_counts;
                    self.invoice_counts = response.data.invoice_counts;
                    self.invoice_totals = response.data.invoice_totals;
                    self.initTicketPieChart(
                        response.data.ticket_pie_chart_label,
                        response.data.ticket_pie_chart_dataset
                    );
                    self.paymentStatusCount(response.data.payment_status_count);
                    self.currenciesInArray(response.data.currencies);
                    self.selected_currency = response.data.business_currency.id;
                    self.business_currency = response.data.business_currency;
                    self.currencyChanged();
                    self.sticky_notes = response.data.sticky_notes;
                    if (self.$can('superadmin')) {
                        self.initTransactionPieChart(
                            response.data.transaction_pie_chart_label,
                            response.data.transaction_pie_chart_datasets
                        );
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        currencyChanged() {
            const self = this;
            for (var key in self.invoice_counts) {
                if (self.selected_currency == self.invoice_counts[key].currency_id) {
                    self.invoice_paid_amount = self.invoice_counts[key].paid_amount;
                    self.total_amount = self.invoice_counts[key].total_amount;
                    self.selected_currency_symbol = self.invoice_counts[key].currency_symbol;
                }
            }
        },
        currenciesInArray(currencies) {
            const self = this;

            var result = Object.keys(currencies).map(function(key) {
                return currencies[key];
            });

            self.currencies = result;
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
        saveStickyNotes() {
            axios.post('/admin/save-stick-notes', { sticky_notes: this.sticky_notes });
        },
        initTicketPieChart(labels, datasets) {
            var ctx = document.getElementById('ticketPieChart');

            var ticketPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: datasets,
                            backgroundColor: ['red', 'blue', 'green'],
                        },
                    ],
                },
            });
        },
        initTransactionPieChart(labels, datasets) {
            var ctx = document.getElementById('transactionPieChart');

            var ticketPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: datasets,
                            backgroundColor: ['green', 'red'],
                        },
                    ],
                },
            });
        },
    },
};
</script>
