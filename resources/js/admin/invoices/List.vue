<template>
    <div class="component-wrap">
        <!-- view invoice -->
        <InvoiceShow ref="invoiceShow"></InvoiceShow>
        <!-- payment dialog -->
        <InvoicePayment ref="invoicePayment"></InvoicePayment>
        <!-- invoice reminder -->
        <InvoiceReminder ref="invoiceReminder"></InvoiceReminder>
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
                                        {{ totalTransactionCount(invoiceStats) }}
                                    </span>
                                </v-flex>

                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                    v-for="(payment, index) in invoiceStats"
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
                                        {{ paid_amount | formatMoney(currency.symbol) }}
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
                            <v-layout row wrap>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="projectList"
                                        v-model="filters.project_id"
                                        :label="trans('messages.project')"
                                        @change="filterChanged"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="company"
                                        item-value="id"
                                        :items="customers"
                                        v-model="filters.customer_id"
                                        :label="trans('messages.customer')"
                                        @change="filterChanged"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="label"
                                        item-value="value"
                                        :items="paymentStatuses"
                                        v-model="filters.payment_status"
                                        :label="trans('messages.payment_status')"
                                        @change="filterChanged"
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
                        {{ trans('messages.all_invoices') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('project.' + projectId + '.invoice.create')"
                    class="primary lighten-1"
                    @click="$router.push({ name: 'sales.invoices.create' })"
                >
                    {{ trans('messages.new_invoice') }}
                    <v-icon right>add</v-icon>
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
                    <span v-if="props.header.value == 'invoice_date'">
                        <v-icon>date_range</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'invoice_number'">
                        <v-icon>receipt</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'customer'">
                        <v-icon>business_center</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'due_date'">
                        <v-icon>date_range</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('project.' + projectId + '.invoice.view')"
                                    @click="view(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('project.' + projectId + '.invoice.edit')"
                                    @click="edit(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile :href="props.item.download_url">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> save_alt </v-icon>
                                        {{ trans('messages.download_invoice') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('project.' + projectId + '.invoice.delete')"
                                    @click="deleteInvoice(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="
                                        props.item.payment_status !== 'paid' &&
                                            props.item.type === 'final'
                                    "
                                    @click="payDueAmountForInvoice(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> credit_card </v-icon>
                                        {{ trans('messages.pay_due_amount') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="
                                        props.item.payment_status !== 'due' &&
                                            $can('project.' + projectId + '.invoice.create')
                                    "
                                    @click="viewPayment(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view_payment') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="
                                        $can('project.' + projectId + '.invoice.create') &&
                                            props.item.type !== 'final'
                                    "
                                    @click="convertToInvoice(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> redo </v-icon>
                                        {{ trans('messages.convert_to_invoice') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="
                                        props.item.payment_status !== 'paid' &&
                                            props.item.type === 'final'
                                    "
                                    @click="sendReminder(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> send </v-icon>
                                        {{ trans('messages.send_reminder') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.invoice_date | formatDate }}</td>
                    <td>{{ props.item.invoice_number }}</td>
                    <td>{{ props.item.project }}</td>
                    <td>{{ props.item.customer }}</td>
                    <td>
                        <status-label
                            :status="props.item.payment_status"
                            v-if="props.item.type !== 'draft'"
                        ></status-label>
                    </td>
                    <td>{{ props.item.due_date | formatDate }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import InvoiceShow from '../../common/projects/invoices/Show';
import InvoicePayment from '../../common/projects/invoices/payment/InvoicePayment';
import InvoiceReminder from '../invoices/InvoiceReminder';
import ViewPayment from '../../common/projects/invoices/payment/ViewPayment';
import StatusLabel from '../status/StatusLabel';
export default {
    components: {
        InvoiceShow,
        InvoicePayment,
        InvoiceReminder,
        ViewPayment,
        StatusLabel,
    },
    data() {
        const self = this;
        return {
            projectId: null,
            total_items: 0,
            loading: true,
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
                    value: 'invoice_date',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.invoice_number'),
                    value: 'invoice_number',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.project'),
                    value: 'project',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.customer'),
                    value: 'customer',
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
                    text: self.trans('messages.due_date'),
                    value: 'due_date',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            filters: [],
            projectList: [],
            customers: [],
            paymentStatuses: [],
            tabs: 'tab-1',
            invoiceStats: [],
            currency: [],
            paid_amount: 0,
        };
    },
    created() {
        const self = this;

        self.getFilterData();
        self.getStatistics();
        self.$eventBus.$on('updatePaymentTransaction', data => {
            self.getInvoiceFromApi();
            self.getStatistics();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updatePaymentTransaction');
    },
    watch: {
        pagination: {
            handler() {
                this.getInvoiceFromApi();
            },
        },
    },
    methods: {
        getInvoiceFromApi() {
            const self = this;
            self.loading = true;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;

            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
                project_id: self.projectId,
            };

            if (self.filters.project_id) {
                params['project_id'] = self.filters.project_id;
            }
            if (self.filters.payment_status) {
                params['payment_status'] = self.filters.payment_status;
            }
            if (self.filters.customer_id) {
                params['customer_id'] = self.filters.customer_id;
            }

            axios
                .get('/invoices', {
                    params: params,
                })
                .then(function(response) {
                    self.total_items = response.data.transactions.total;
                    self.items = response.data.transactions.data;
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        deleteInvoice(invoice) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/invoices/' + invoice.id, {
                            params: { project_id: invoice.project_id },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getInvoiceFromApi();
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
        view(invoice) {
            const self = this;

            var data = { transaction_id: invoice.id, project_id: invoice.project_id };
            self.$refs.invoiceShow.view(data);
        },
        edit(invoice) {
            const self = this;
            self.$router.push({
                name: 'invoices.edit',
                params: { id: invoice.id, project_id: invoice.project_id },
            });
        },
        payDueAmountForInvoice(item) {
            const self = this;
            var data = { transaction_id: item.id, type: 'invoice' };
            self.$refs.invoicePayment.create(data);
        },
        getFilterData() {
            const self = this;
            axios
                .get('invoices/get-filter-data')
                .then(function(response) {
                    self.projectList = response.data.projects;
                    self.customers = response.data.customers;
                    self.paymentStatuses = response.data.payment_statuses;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        filterChanged() {
            const self = this;
            self.getInvoiceFromApi();
        },
        convertToInvoice(item) {
            const self = this;
            axios
                .get('invoices/' + item.id + '/convert-to-invoice', {
                    params: {
                        project_id: item.project_id,
                    },
                })
                .then(function(response) {
                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });

                    if (response.data.success === true) {
                        self.getInvoiceFromApi();
                        self.getStatistics();
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        sendReminder(item) {
            var data = { transaction_id: item.id, project_id: item.project_id };
            this.$refs.invoiceReminder.create(data);
        },
        viewPayment(transaction_id) {
            var data = { transaction_id: transaction_id, type: 'invoice' };
            this.$refs.viewPayment.view(data);
        },
        getStatistics() {
            const self = this;
            if (self.$can('superadmin')) {
                axios
                    .get('/invoice-statistics')
                    .then(function(response) {
                        self.invoiceStats = response.data.payment_stats;
                        self.currency = response.data.currency;
                        self.paid_amount = response.data.paid_amount.paid_amount;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
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
