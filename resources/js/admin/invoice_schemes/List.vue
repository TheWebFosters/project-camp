<template>
    <div class="component-wrap">
        <!-- Add invoice scheme-->
        <InvoiceSchemeAdd ref="invoiceSchemeAdd"></InvoiceSchemeAdd>
        <!-- Edit invoice scheme-->
        <InvoiceSchemeEdit ref="invoiceSchemeEdit"></InvoiceSchemeEdit>
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_invoice_schemes') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('invoice_scheme.create')"
                    class="primary lighten-1"
                    dark
                    @click="$refs.invoiceSchemeAdd.create()"
                >
                    {{ trans('messages.new_scheme') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- dataTable -->
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'name'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'prefix'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'start_number'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'invoice_count'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'total_digits'">
                        {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile @click="edit(props.item)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile @click="deleteInvoiceScheme(props.item)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>
                        {{ props.item.name }}
                        <v-chip
                            color="green"
                            text-color="white"
                            small
                            v-if="props.item.is_default == 1"
                            >{{ trans('messages.default') }}</v-chip
                        >
                    </td>
                    <td>
                        {{ props.item.scheme_type == 'year' ? year_prefix : props.item.prefix }}
                    </td>
                    <td>{{ props.item.start_number }}</td>
                    <td>{{ props.item.invoice_count }}</td>
                    <td>{{ props.item.total_digits }}</td>
                </template>
            </v-data-table>
            <!-- /dataTable -->
        </v-card>
    </div>
</template>
<script>
import InvoiceSchemeAdd from './Add';
import InvoiceSchemeEdit from './Edit';
export default {
    components: {
        InvoiceSchemeAdd,
        InvoiceSchemeEdit,
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
                { text: self.trans('messages.name'), value: 'name', align: 'left', sortable: true },
                {
                    text: self.trans('messages.prefix'),
                    value: 'prefix',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.start_from'),
                    value: 'start_number',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.invoice_count'),
                    value: 'invoice_count',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.no_of_digits'),
                    value: 'total_digits',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            year_prefix: '#' + moment().format('YYYY'),
        };
    },
    created() {
        const self = this;
        self.$eventBus.$on('updateInvoiceSchemeTable', data => {
            self.getInvoiceSchemeFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateInvoiceSchemeTable');
    },
    watch: {
        pagination: {
            handler() {
                this.getInvoiceSchemeFromApi();
            },
        },
    },
    methods: {
        getInvoiceSchemeFromApi() {
            const self = this;
            self.loading = true;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;

            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };

            axios
                .get('/admin/invoice-scheme', {
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
        deleteInvoiceScheme(invoice_scheme) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('admin/invoice-scheme/' + invoice_scheme.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getInvoiceSchemeFromApi();
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
        edit(invoice_scheme) {
            var data = { id: invoice_scheme.id };
            this.$refs.invoiceSchemeEdit.edit(data);
        },
    },
};
</script>
