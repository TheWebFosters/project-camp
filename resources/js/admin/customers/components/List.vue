<template>
    <div class="component-wrap">
        <!-- edit customer -->
        <CustomerFormEdit ref="customerEdit"></CustomerFormEdit>
        <!-- create customer -->
        <CustomerFormAdd ref="customerAdd"></CustomerFormAdd>

        <v-card class="mb-3">
            <v-layout row wrap>
                <!-- search filter -->
                <v-flex xs12 sm6 md6>
                    <v-text-field
                        prepend-icon="search"
                        :label="trans('messages.search')"
                        single-line
                        v-model="search"
                        @keyup="searchCustomer"
                    ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6 class="text-xs-right pt-1">
                    <v-btn
                        v-if="$can('customer.create')"
                        class="primary lighten-1"
                        dark
                        @click="create"
                    >
                        {{ trans('messages.new_customer') }}
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-card>
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_customers') }}
                    </div>
                </div>
            </v-card-title>
            <v-divider></v-divider>
            <!-- Data table -->
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'company'">
                        <v-icon>business_center</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'tax_number'">
                        <v-icon>receipt</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'mobile'">
                        <v-icon>phone</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'website'">
                        <v-icon>web</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('customer.view')"
                                    @click="
                                        $router.push({
                                            name: 'customers.show',
                                            params: { id: props.item.id },
                                        })
                                    "
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('customer.edit')"
                                    @click="edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('customer.delete')"
                                    @click="deleteCustomer(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.company }}</td>
                    <td>{{ props.item.tax_number }}</td>
                    <td>{{ props.item.mobile }}</td>
                    <td>{{ props.item.website }}</td>
                </template>
            </v-data-table>
        </v-card>
        <!-- /Data table-->
    </div>
</template>
<script>
import CustomerFormEdit from '../components/Edit';
import CustomerFormAdd from '../components/Add';
export default {
    components: {
        CustomerFormEdit,
        CustomerFormAdd,
    },
    data() {
        const self = this;
        return {
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
                    text: self.trans('messages.company'),
                    value: 'company',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.tax_number'),
                    value: 'tax_number',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.mobile'),
                    value: 'mobile',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.website'),
                    value: 'website',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            search: '',
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
        self.$eventBus.$on('updateCustomerTable', data => {
            self.getDataFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateCustomerTable');
    },
    methods: {
        getDataFromApi() {
            this.loading = true;

            const { sortBy, descending, page, rowsPerPage } = this.pagination;
            const self = this;
            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };
            if (self.search) {
                params['term'] = self.search;
            }

            axios
                .get('/admin/customers', {
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
        create() {
            const self = this;
            var templateType = { template: 'customer' };
            self.$refs.customerAdd.create(templateType);
        },
        edit(id) {
            const self = this;
            self.$refs.customerEdit.edit(id);
        },
        deleteCustomer(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/customers/' + item.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getDataFromApi();
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
        customerContact(item) {
            const self = this;
            self.$router.push({ name: 'customers.contacts.list', params: { id: item.id } });
        },
        searchCustomer() {
            const self = this;
            self.getDataFromApi();
        },
    },
};
</script>
