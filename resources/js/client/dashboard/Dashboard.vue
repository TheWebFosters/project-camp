<template>
    <div class="page_wrap_vue">
        <!-- <transition name="fade"> <router-view></router-view> </transition> -->
        <div class="component-wrap">
            <v-container grid-list-md>
                <v-layout row-wrap>
                    <v-flex xs12 sm12 md12>
                        <div class="title" v-if="user_name">
                            {{ trans('messages.welcome', { name: user_name }) }}
                        </div>
                    </v-flex>
                </v-layout>

                <v-layout row-wrap class="pt-2">
                    <v-flex xs12 sm4 md4>
                        <v-card elevation="4">
                            <v-card-title>
                                <v-icon>assessment</v-icon>
                                {{ trans('messages.project_in_progress') }}
                                <v-spacer></v-spacer>
                                {{ project.incompleted }}/{{ project.total }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="blue-grey"
                                    height="8"
                                    :value="percentage(project.incompleted, project.total)"
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
                                {{ task.incompleted }}/{{ task.total }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="brown"
                                    height="8"
                                    :value="percentage(task.incompleted, task.total)"
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                    <v-flex xs12 sm4 md4>
                        <v-card elevation="4">
                            <v-card-title>
                                <v-icon>receipt</v-icon>
                                {{ trans('messages.invoices_awaiting_payment') }}
                                <v-spacer></v-spacer>
                                {{ invoice.not_paid }}/{{ invoice.total_counts }}
                            </v-card-title>
                            <v-card-text>
                                <v-progress-linear
                                    color="deep-orange lighten-2"
                                    height="8"
                                    :value="percentage(invoice.not_paid, invoice.total_counts)"
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>

                <v-layout row-wrap>
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
                                        <v-flex xs12 sm12 md6>
                                            <v-flex xs12 sm12 md12>
                                                {{ _.get(invoice, 'paid', 0) }}
                                                {{ trans('messages.paid') }}
                                                <v-progress-linear
                                                    color="green lighten-1"
                                                    height="8"
                                                    :value="
                                                        percentage(
                                                            invoice.paid,
                                                            invoice.total_counts
                                                        )
                                                    "
                                                ></v-progress-linear>
                                            </v-flex>
                                            <v-flex xs12 sm12 md12>
                                                {{ _.get(invoice, 'partial', 0) }}
                                                {{ trans('messages.partial') }}
                                                <v-progress-linear
                                                    color="cyan lighten-2"
                                                    height="8"
                                                    :value="
                                                        percentage(
                                                            invoice.partial,
                                                            invoice.total_counts
                                                        )
                                                    "
                                                ></v-progress-linear>
                                            </v-flex>
                                            <v-flex xs12 sm12 md12>
                                                {{ _.get(invoice, 'due', 0) }}
                                                {{ trans('messages.due') }}
                                                <v-progress-linear
                                                    color="red accent-2"
                                                    height="8"
                                                    :value="
                                                        percentage(
                                                            invoice.due,
                                                            invoice.total_counts
                                                        )
                                                    "
                                                ></v-progress-linear>
                                            </v-flex>
                                        </v-flex>
                                        <v-flex xs12 sm12 md6>
                                            <v-layout wrap>
                                                <v-flex xs12 sm4 md6>
                                                    <v-card elevation="4" height="100%">
                                                        <v-card-title>
                                                            {{
                                                                invoice.total_amount
                                                                    | formatMoney(currency_symbol)
                                                            }}
                                                            <br />
                                                            {{
                                                                trans(
                                                                    'messages.total_invoice_amount'
                                                                )
                                                            }}
                                                        </v-card-title>
                                                    </v-card>
                                                </v-flex>
                                                <v-flex xs12 sm4 md6>
                                                    <v-card elevation="4" height="100%">
                                                        <v-card-title>
                                                            {{
                                                                invoice.paid_amount
                                                                    | formatMoney(currency_symbol)
                                                            }}
                                                            <br />
                                                            {{ trans('messages.paid_invoices') }}
                                                        </v-card-title>
                                                    </v-card>
                                                </v-flex>
                                                <v-flex xs12 sm4 md6>
                                                    <v-card elevation="4" height="100%">
                                                        <v-card-title>
                                                            {{
                                                                _.subtract(
                                                                    invoice.total_amount,
                                                                    invoice.paid_amount
                                                                ) | formatMoney(currency_symbol)
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
                </v-layout>
            </v-container>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            active: '',
            user_name: '',
            project: '',
            task: '',
            invoice: '',
            currency_symbol: null,
        };
    },
    mounted() {
        const self = this;
        self.getDashboardDataFromApi();
    },
    methods: {
        getDashboardDataFromApi() {
            const self = this;
            axios
                .get('/client/dashboards')
                .then(function(response) {
                    self.user_name = response.data.user_name;
                    self.project = response.data.project;
                    self.task = response.data.task;
                    self.invoice = response.data.invoice;
                    self.currency_symbol = response.data.currency_symbol;
                    self.initPieChart(
                        response.data.ticket_pie_chart_label,
                        response.data.ticket_pie_chart_dataset
                    );
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        initPieChart(labels, datasets) {
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
    },
};
</script>
