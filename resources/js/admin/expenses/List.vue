<template>
    <div class="component-wrap">
        <!-- Add expense-->
        <ExpenseAdd ref="expenseAdd"></ExpenseAdd>
        <!-- Edit expense -->
        <ExpenseEdit ref="expenseEdit"></ExpenseEdit>
        <!-- add payment for expense -->
        <ExpensePayment ref="expensePayment"></ExpensePayment>
        <!-- view payment -->
        <ViewPayment ref="viewPayment"></ViewPayment>

        <v-tabs v-model="tabs" fixed-tabs height="47" class="elevation-3">
            <v-tab :href="'#tab-1'" @click="getStatistics" v-if="$can('superadmin')">
                <v-icon>bar_chart</v-icon>
                {{ trans('messages.statistics') }}
            </v-tab>
            <v-tab :href="'#tab-2'">
                <v-icon>filter_list</v-icon>
                {{ trans('messages.filters') }}
            </v-tab>
        </v-tabs>
        <v-tabs-items v-model="tabs">
            <v-divider></v-divider>
            <v-tab-item :value="'tab-1'" v-if="$can('superadmin')">
                <v-card flat class="elevation-2">
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout row wrap>
                                <v-flex xs12 sm3 md3>
                                    <span class="subheading font-weight-medium primary--text">
                                        {{ trans('messages.total') }}:
                                        {{ totalTransactionCount(expense_stats) }}
                                    </span>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                    v-for="(payment, index) in expense_stats"
                                    :key="index"
                                >
                                    <span
                                        class="subheading font-weight-medium error--text"
                                        v-if="_.includes(['due'], payment.payment_status)"
                                    >
                                        {{ trans('messages.due') }}:
                                        {{ payment.payment_status_count }}
                                    </span>

                                    <span
                                        class="subheading font-weight-medium cyan--text"
                                        v-if="_.includes(['partial'], payment.payment_status)"
                                    >
                                        {{ trans('messages.partial') }}:
                                        {{ payment.payment_status_count }}
                                    </span>

                                    <span
                                        class="subheading font-weight-medium success--text"
                                        v-if="_.includes(['paid'], payment.payment_status)"
                                    >
                                        {{ trans('messages.paid') }}:
                                        {{ payment.payment_status_count }}
                                    </span>
                                </v-flex>
                                <v-flex xs12 sm3 md3>
                                    <span class="subheading font-weight-medium success--text">
                                        {{ trans('messages.paid_amount') }}:
                                        {{ paid_amount | formatMoney(business_currency.symbol) }}
                                    </span>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item :value="'tab-2'">
                <v-card flat class="elevation-2">
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout wrap>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="employees"
                                        v-model="expense.expense_for"
                                        :label="trans('messages.expense_for')"
                                        @change="getDataFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="categories"
                                        v-model="expense.category_id"
                                        :label="trans('messages.category')"
                                        @change="getDataFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="label"
                                        item-value="value"
                                        :items="paymentStatus"
                                        v-model="expense.payment_status"
                                        :label="trans('messages.payment_status')"
                                        @change="getDataFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <v-card class="mt-3">
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_expenses') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('expense.create')"
                    class="primary lighten-1"
                    dark
                    @click="$refs.expenseAdd.create()"
                >
                    {{ trans('messages.add_expense') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'transaction_date'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'ref_no'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'category'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'payment_status'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'total'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'notes'">
                        {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('expense.edit')"
                                    @click="$refs.expenseEdit.edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="$can('expense.delete')"
                                    @click="deleteExpense(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="
                                        props.item.payment_status !== 'paid' &&
                                            props.item.status === 'final' &&
                                            $can('expense.create')
                                    "
                                    @click="payDueAmountForExpense(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> credit_card </v-icon>
                                        {{ trans('messages.pay_due_amount') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="
                                        props.item.payment_status !== 'due' &&
                                            $can('expense.create')
                                    "
                                    @click="viewPayment(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view_payment') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.transaction_date | formatDate }}</td>
                    <td>{{ props.item.due_date | formatDate }}</td>
                    <td>{{ props.item.ref_no }}</td>
                    <td>
                        <status-label :status="props.item.payment_status"></status-label>
                    </td>
                    <td>{{ props.item.total | formatMoney(business_currency.symbol) }}</td>
                    <td>{{ props.item.payment_due | formatMoney(business_currency.symbol) }}</td>
                    <td>{{ props.item.category }}</td>
                    <td>{{ props.item.expense_for }}</td>
                    <td>{{ props.item.notes }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import ExpenseAdd from './Add';
import ExpenseEdit from './Edit';
import ExpensePayment from '../../common/projects/invoices/payment/InvoicePayment';
import ViewPayment from '../../common/projects/invoices/payment/ViewPayment';
import StatusLabel from '../status/StatusLabel';
export default {
    components: {
        ExpenseAdd,
        ExpenseEdit,
        ExpensePayment,
        ViewPayment,
        StatusLabel,
    },
    data() {
        const self = this;
        return {
            total_items: 0,
            loading: false,
            pagination: { totalItems: 0 },
            headers: [
                {
                    text: self.trans('messages.action'),
                    value: false,
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.date'),
                    value: 'transaction_date',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.due_date'),
                    value: 'due_date',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.ref_no'),
                    value: 'ref_no',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.payment_status'),
                    value: 'payment_status',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.total_amount'),
                    value: 'total',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.payment_due'),
                    value: 'payment_due',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.expense_category'),
                    value: 'category',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.expense_for'),
                    value: 'expense_for',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.notes'),
                    value: 'notes',
                    align: 'left',
                    sortable: false,
                },
            ],
            items: [],
            employees: [],
            expense: [],
            categories: [],
            paymentStatus: [],
            business_currency: [],
            tabs: 'tab-1',
            expense_stats: [],
            paid_amount: 0,
        };
    },
    watch: {
        pagination: {
            handler() {
                this.getDataFromApi();
            },
        },
    },
    mounted() {
        const self = this;
        self.getFilters();
        self.getStatistics();
        self.$eventBus.$on('updateExpenseTable', data => {
            self.getDataFromApi();
            self.getStatistics();
        });
        self.$eventBus.$on('updatePaymentTransaction', data => {
            self.getDataFromApi();
            self.getStatistics();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateExpenseTable');
        self.$eventBus.$off('updatePaymentTransaction');
    },
    methods: {
        getDataFromApi() {
            const self = this;
            self.loading = true;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;
            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };

            if (self.expense.expense_for) {
                params['expense_for'] = self.expense.expense_for;
            }

            if (self.expense.category_id) {
                params['category_id'] = self.expense.category_id;
            }

            if (self.expense.payment_status) {
                params['payment_status'] = self.expense.payment_status;
            }

            axios
                .get('/expenses', {
                    params: params,
                })
                .then(function(response) {
                    self.total_items = response.data.total;
                    self.items = response.data.data;
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        deleteExpense(expense) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/expenses/' + expense.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getDataFromApi();
                                self.getStatistics();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                cancelCb: () => {
                    console.log('CANCEL');
                },
            });
        },
        getFilters() {
            const self = this;
            axios
                .get('/expenses/get-filters')
                .then(function(response) {
                    self.employees = response.data.employees;
                    self.categories = response.data.categories;
                    self.paymentStatus = response.data.payment_statuses;
                    self.business_currency = response.data.business_currency;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        payDueAmountForExpense(item) {
            var data = { transaction_id: item.id, type: 'expense' };
            this.$refs.expensePayment.create(data);
        },
        viewPayment(transaction_id) {
            var data = { transaction_id: transaction_id, type: 'expense' };
            this.$refs.viewPayment.view(data);
        },
        getStatistics() {
            const self = this;
            axios
                .get('/expense-statistics')
                .then(function(response) {
                    self.expense_stats = response.data.payment_stats;
                    self.paid_amount = response.data.paid_amount.paid_amount;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        totalTransactionCount(transactions) {
            var total = 0;

            _.forEach(transactions, function(transaction) {
                total = _.add(total, parseInt(transaction.payment_status_count));
            });

            return total;
        },
    },
};
</script>
