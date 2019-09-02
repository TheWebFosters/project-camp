<template>
    <div class="component-wrap">
        <!-- view invoice -->
        <InvoiceShow ref="invoiceShow"></InvoiceShow>
        <!-- filters -->
        <v-layout row class="mb-3">
            <v-flex xs12 sm12>
                <v-card>
                    <v-list>
                        <v-list-group prepend-icon="filter_list">
                            <v-list-tile slot="activator">
                                <v-list-tile-content>
                                    <v-list-tile-title>{{
                                        trans('messages.filters')
                                    }}</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile-content>
                                <v-layout>
                                    <v-flex xs12 sm12>
                                        <v-card>
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
                                                </v-layout>
                                            </v-container>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                            </v-list-tile-content>
                        </v-list-group>
                    </v-list>
                </v-card>
            </v-flex>
        </v-layout>
        <!-- /filters -->
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_estimates') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('project.' + projectId + '.invoice.create')"
                    class="primary lighten-1"
                    dark
                    @click="$router.push({ name: 'estimates.create' })"
                >
                    {{ trans('messages.new_invoice') }}
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
                                        {{ trans('messages.download_estimate') }}
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
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.invoice_date | formatDate }}</td>
                    <td>{{ props.item.invoice_number }}</td>
                    <td>{{ props.item.project }}</td>
                    <td>{{ props.item.customer }}</td>
                    <td>{{ props.item.due_date | formatDate }}</td>
                </template>
            </v-data-table>
            <!-- /dataTable -->
        </v-card>
    </div>
</template>
<script>
import InvoiceShow from '../../../common/projects/invoices/Show';
export default {
    components: {
        InvoiceShow,
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
        };
    },
    created() {
        const self = this;
        self.getFilterData();
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
                status: 'estimate',
            };

            if (self.filters.project_id) {
                params['project_id'] = self.filters.project_id;
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
        getFilterData() {
            const self = this;
            axios
                .get('invoices/get-filter-data')
                .then(function(response) {
                    self.projectList = response.data.projects;
                    self.customers = response.data.customers;
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
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
