<template>
    <div class="component-wrap">
        <v-layout>
            <v-flex xs12 sm8 md8 mt-3>
                <span class="headline">
                    {{
                        trans('messages.customer_statement', {
                            customer: customer.company,
                        })
                    }}
                </span>
            </v-flex>
            <v-flex xs12 sm4 md4>
                <v-daterange
                    v-model="range"
                    :display-format="format"
                    :highlight-color="highlightColor"
                    :input-props="inputStyle"
                    :preset-label="trans('messages.date_range')"
                    :presets="defaultRange"
                    @input="getDateRangevalue"
                >
                </v-daterange>
            </v-flex>
        </v-layout>
        <v-card elevation="4">
            <v-layout wrap>
                <v-flex xs12 sm12 md12>
                    <v-card-title>
                        <v-spacer></v-spacer>
                        {{ business.name }} <br />
                        {{ business.tax_number }} <br />
                        {{ business.email }} <br />
                        {{ business.mobile }} <br />
                        {{ business.city }}
                        {{ business.state }} <br />
                        {{ business.country }}
                        {{ business.zip_code }}
                    </v-card-title>
                    <v-divider></v-divider>
                </v-flex>
            </v-layout>
            <v-container grid-list-md>
                <v-layout>
                    <v-flex xs12 sm6 md6>
                        {{ trans('messages.to') }}: <br />
                        {{ customer.company }} <br />
                        {{ customer.email }} <br />
                        {{ customer.mobile }} <br />
                        <span v-html="customer.billing_address"></span>
                    </v-flex>
                    <v-flex xs12 sm6 md6 class="text-xs-right">
                        {{ trans('messages.account_summary') }} <br />
                        {{ range.start | formatDate }} {{ trans('messages.to') }}
                        {{ range.end | formatDate }}
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs12 sm12 md12 class="text-md-center">
                        {{
                            trans('messages.showing_payments_and_invoices', {
                                start_date: range.start,
                                end_date: range.end,
                            })
                        }}
                    </v-flex>
                </v-layout>
                <v-layout wrap>
                    <v-flex xs12 sm12 md12>
                        <br /><br />
                        <v-layout row wrap text-xs-center>
                            <v-flex md2>
                                <h4>{{ trans('messages.date') }}</h4>
                            </v-flex>
                            <v-flex md4>
                                <h4>{{ trans('messages.details') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.amount') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.payments') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.balance') }}</h4>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap text-xs-center>
                            <template v-for="transaction in transactions">
                                <v-flex md2>
                                    <span>
                                        {{ transaction.transaction_date | formatDate }}
                                    </span>
                                </v-flex>
                                <v-flex md4>
                                    <span v-if="transaction.type == 'invoice'">
                                        {{
                                            trans('messages.invoice_on', {
                                                ref_no: transaction.ref_no,
                                                transaction_date: transaction.transaction_date,
                                            })
                                        }}
                                    </span>
                                    <span v-if="transaction.type == 'payment'">
                                        {{
                                            trans('messages.payment_to_invoice', {
                                                ref_no: transaction.ref_no,
                                            })
                                        }}
                                    </span>
                                </v-flex>
                                <v-flex md2>
                                    <span v-if="transaction.type == 'invoice'">
                                        {{ transaction.total | formatMoney(currency.symbol) }}
                                    </span>
                                </v-flex>
                                <v-flex md2>
                                    <span v-if="transaction.type == 'payment'">
                                        {{ transaction.amount | formatMoney(currency.symbol) }}
                                    </span>
                                </v-flex>
                                <v-flex md2>
                                    <span v-if="transaction.type == 'invoice'">
                                        {{ transaction.total | formatMoney(currency.symbol) }}
                                    </span>
                                    <span v-if="transaction.type == 'payment'">
                                        {{ transaction.balance | formatMoney(currency.symbol) }}
                                    </span>
                                </v-flex>
                            </template>
                        </v-layout>
                    </v-flex>
                    <br />
                </v-layout>
                <v-layout v-if="transactions.length == 0">
                    <v-flex xs12 text-xs-center mt-4>
                        <span class="headline">
                            {{ trans('messages.no_data_for_selected_date') }}
                        </span>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs12 sm6 md6 mt-4> </v-flex>
                    <v-flex xs12 sm2 md2 text-xs-center mt-4>
                        {{ trans('messages.balance_due') }}
                    </v-flex>
                    <v-flex xs12 sm2 md2 mt-4> </v-flex>
                    <v-flex xs12 sm2 md2 text-xs-center mt-4>
                        {{ dueBalance | formatMoney(currency.symbol) }}
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </div>
</template>
<script>
import { VDaterange } from 'vuetify-daterange-picker';
import 'vuetify-daterange-picker/dist/vuetify-daterange-picker.css';
export default {
    components: { VDaterange },
    data() {
        const self = this;
        return {
            customer_id: null,
            customer: [],
            business: [],
            transactions: [],
            payments: [],
            currency: [],
            dueBalance: null,
            range: {
                start: moment()
                    .startOf('month')
                    .format('YYYY-MM-DD'),
                end: moment()
                    .endOf('month')
                    .format('YYYY-MM-DD'),
            },
            format: APP.DATE_FORMAT.VALUE,
            highlightColor: 'blue lighten-1',
            inputStyle: {
                'prepend-inner-icon': 'date_range',
                label: self.trans('messages.date_range'),
                id: 'v_date_range',
            },
            defaultRange: self.defaultDateRange(),
        };
    },
    mounted() {
        this.customer_id = this.$route.params.id;
        this.getLedgerDataFromApi(this.range.start, this.range.end);
    },
    methods: {
        getDateRangevalue() {
            this.getLedgerDataFromApi(this.range.start, this.range.end);
        },
        getLedgerDataFromApi(start_date, end_date) {
            const self = this;
            axios
                .get('/admin/customer-account-ledger', {
                    params: {
                        customer_id: self.customer_id,
                        start_date: start_date,
                        end_date: end_date,
                    },
                })
                .then(function(response) {
                    self.customer = response.data.customer;
                    self.currency = response.data.customer.currency;
                    self.business = response.data.business_info;
                    self.transactions = response.data.transactions;
                    self.dueBalance = self.getDueBlance(response.data.transactions);
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getDueBlance(transactions) {
            var total = 0;
            var paid = 0;
            var due = 0;

            _.forEach(transactions, function(transaction) {
                if (transaction.type == 'invoice') {
                    total = _.add(total, parseFloat(transaction.total));
                } else if (transaction.type == 'payment') {
                    paid = _.add(paid, parseFloat(transaction.amount));
                }
            });

            due = _.subtract(total, paid);

            return _.floor(due, 2);
        },
    },
};
</script>
