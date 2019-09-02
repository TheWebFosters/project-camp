<template>
    <div class="component-wrap">
        <v-layout row justify-center>
            <v-dialog v-model="dialog" max-width="700">
                <v-card>
                    <v-card-title>
                        <v-icon medium>money</v-icon>
                        <span class="headline"> {{ trans('messages.view_payment') }} </span>
                        <v-spacer></v-spacer>
                        <v-btn @click="dialog = false" flat small icon>
                            <v-icon>clear</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout wrap>
                                <v-flex xs12 sm4 md4>
                                    <div v-if="transaction.ref_no">
                                        <strong> {{ trans('messages.ref_no') }}: </strong>
                                        <span> {{ transaction.ref_no }}</span> <br />
                                    </div>
                                    <strong> {{ trans('messages.payment_status') }}: </strong>
                                    {{ trans('messages.' + transaction.payment_status) }}
                                    <br />

                                    <strong> {{ trans('messages.date') }} : </strong>
                                    <span>
                                        {{ transaction.transaction_date | formatDate }}
                                    </span>
                                    <br />

                                    <div v-if="transaction.type == 'invoice'">
                                        <strong> {{ trans('messages.total_amount') }} : </strong>
                                        <span>
                                            {{
                                                transaction.total
                                                    | formatMoney(customer_currency.symbol)
                                            }}
                                        </span>
                                        <br />
                                    </div>
                                    <div v-if="transaction.type == 'expense'">
                                        <strong> {{ trans('messages.total_amount') }} : </strong>
                                        <span>
                                            {{
                                                transaction.total
                                                    | formatMoney(business_currency.symbol)
                                            }}
                                        </span>
                                        <br />
                                    </div>

                                    <div v-if="!_.isNull(transaction.due_date)">
                                        <strong> {{ trans('messages.due_date') }}: </strong>
                                        <span>
                                            {{ transaction.due_date | formatDate }}
                                        </span>
                                        <br />
                                    </div>

                                    <div v-if="transaction.type == 'invoice'">
                                        <strong> {{ trans('messages.discount_type') }} : </strong>
                                        {{ trans('messages.' + transaction.discount_type) }} <br />
                                    </div>
                                </v-flex>
                                <v-flex xs12 sm4 md4 v-if="employee">
                                    <strong> {{ trans('messages.expense_for') }} : </strong>
                                    {{ employee.name }} <br />
                                    <strong> {{ trans('messages.email') }} : </strong>
                                    {{ employee.email }} <br />
                                    <div v-if="employee.mobile">
                                        <strong> {{ trans('messages.mobile') }} : </strong>
                                        {{ employee.mobile }} <br />
                                    </div>
                                </v-flex>

                                <v-flex xs12 sm4 md4 v-if="project">
                                    <strong> {{ trans('messages.project') }} : </strong>
                                    {{ project.name }} <br />
                                    <strong> {{ trans('messages.status') }} : </strong>
                                    {{ trans('messages.' + project.status) }} <br />
                                    <strong> {{ trans('messages.end_date') }} : </strong>
                                    {{ project.end_date | formatDate }} <br />
                                </v-flex>
                                <v-flex xs12 sm4 md4 v-if="customer">
                                    <strong> {{ trans('messages.customer') }} : </strong>
                                    {{ customer.company }} <br />
                                    <strong> {{ trans('messages.tax_number') }} : </strong>
                                    {{ customer.tax_number }} <br />
                                    <strong> {{ trans('messages.email') }} : </strong>
                                    {{ customer.email }} <br />
                                    <strong> {{ trans('messages.mobile') }} : </strong>
                                    {{ customer.mobile }} <br />
                                </v-flex>
                            </v-layout>
                        </v-container>
                        <v-divider></v-divider>
                        <v-container grid-list-md text-xs-center>
                            <v-layout row wrap>
                                <v-flex md3>
                                    <h4>{{ trans('messages.date') }}</h4>
                                </v-flex>
                                <v-flex md3>
                                    <h4>{{ trans('messages.amount') }}</h4>
                                </v-flex>
                                <v-flex md6>
                                    <h4>{{ trans('messages.notes') }}</h4>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <template v-for="payment in payments">
                                    <v-flex md3>
                                        {{ payment.paid_on | formatDate }}
                                    </v-flex>
                                    <v-flex md3>
                                        <span v-if="transaction.type == 'invoice'">
                                            {{
                                                payment.amount
                                                    | formatMoney(customer_currency.symbol)
                                            }}
                                            <br />
                                            <small>
                                                ( 1{{ business_currency.symbol }} =
                                                {{ payment.conversion_rate
                                                }}{{ customer_currency.symbol }},
                                                {{
                                                    payment.final_amount
                                                        | formatMoney(business_currency.symbol)
                                                }})
                                            </small>
                                        </span>
                                        <span v-if="transaction.type == 'expense'">
                                            {{
                                                payment.amount
                                                    | formatMoney(business_currency.symbol)
                                            }}
                                            <br />
                                        </span>
                                    </v-flex>
                                    <v-flex md6>
                                        <span v-html="payment.payment_details"></span>
                                    </v-flex>
                                </template>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green darken-1" flat @click="dialog = false">
                            {{ trans('messages.close') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            transaction: [],
            payments: [],
            employee: [],
            project: [],
            customer: [],
            customer_currency: [],
            business_currency: [],
        };
    },
    methods: {
        view(invoice_params) {
            const self = this;
            axios
                .get('/transaction-payments/' + invoice_params.transaction_id, {
                    params: {
                        type: invoice_params.type,
                    },
                })
                .then(function(response) {
                    self.transaction = response.data.transaction;
                    self.payments = response.data.transaction.payments;
                    self.project = response.data.transaction.project;
                    self.employee = response.data.transaction.expense_for;
                    self.customer = response.data.transaction.customer;
                    self.customer_currency = response.data.currency.customer;
                    self.business_currency = response.data.currency.business;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
