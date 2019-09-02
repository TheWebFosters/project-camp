<template>
    <div>
        <v-tabs v-model="tabs" fixed-tabs height="47" class="elevation-3">
            <v-tab :href="'#tab-1'" @click="getStatistics">
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
            <v-tab-item :value="'tab-1'">
                <v-card flat class="elevation-2">
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout row wrap>
                                <v-flex
                                    xs12
                                    sm4
                                    md4
                                    v-for="(statistics, index) in ticket_stats"
                                    :key="index"
                                >
                                    <span
                                        class="subheading font-weight-medium red--text text--accent-2"
                                        v-if="_.includes(['new'], statistics.status)"
                                    >
                                        {{ trans('messages.' + statistics.status) }}:
                                        {{ statistics.status_count }}
                                    </span>
                                    <span
                                        class="subheading font-weight-medium pink--text text--lighten-1"
                                        v-if="_.includes(['open'], statistics.status)"
                                    >
                                        {{ trans('messages.' + statistics.status) }}:
                                        {{ statistics.status_count }}
                                    </span>
                                    <span
                                        class="subheading font-weight-medium green--text text--lighten-1"
                                        v-if="_.includes(['closed'], statistics.status)"
                                    >
                                        {{ trans('messages.' + statistics.status) }}:
                                        {{ statistics.status_count }}
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
                                <v-flex xs12 md4 v-if="$can('superadmin')">
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="employees"
                                        v-model="filters.assigned_to"
                                        :label="trans('messages.assigned_to')"
                                        @change="getTicketFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4 v-if="$can('superadmin')">
                                    <v-autocomplete
                                        item-text="company"
                                        item-value="id"
                                        :items="customers"
                                        v-model="filters.customer_id"
                                        :label="trans('messages.customer')"
                                        @change="getTicketFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="ticket_types"
                                        v-model="filters.category_id"
                                        :label="trans('messages.ticket_type')"
                                        @change="getTicketFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="value"
                                        item-value="key"
                                        :items="statuses"
                                        v-model="filters.status"
                                        :label="trans('messages.status')"
                                        @change="getTicketFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="value"
                                        item-value="key"
                                        :items="priorities"
                                        v-model="filters.priority"
                                        :label="trans('messages.priority')"
                                        @change="getTicketFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <v-card class="mt-3">
            <ticket-create ref="ticketCreate"></ticket-create>
            <ticket-edit ref="ticketEdit"></ticket-edit>
            <v-card-title primary-title xs8 sm8>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_tickets') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn v-if="$can('tickets.create')" class="primary lighten-1" @click="create">
                    {{ trans('messages.new_ticket') }}
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
                    <span v-if="props.header.value == 'reference_no'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'customer'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'assigned_to'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'ticket_type'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'title'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'priority'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'status'">
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
                                    @click="
                                        $router.push({
                                            name: 'tickets.view',
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
                                    v-if="$can('tickets.edit')"
                                    @click="edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="$can('tickets.delete')"
                                    @click="deleteTicket(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.reference_no }}</td>
                    <td>{{ props.item.customer }}</td>
                    <td>{{ props.item.assigned_to }}</td>
                    <td>{{ props.item.ticket_type }}</td>
                    <td>{{ props.item.title }}</td>
                    <td>
                        <status-label
                            :status="props.item.priority"
                            v-if="!_.isEmpty(props.item.priority)"
                        >
                        </status-label>
                    </td>
                    <td>
                        <status-label :status="props.item.status"> </status-label>
                    </td>
                    <td>{{ props.item.last_updated | formatDateTime }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import TicketCreate from './Create';
import TicketEdit from './Edit';
import StatusLabel from '../../admin/status/StatusLabel';
export default {
    components: {
        TicketCreate,
        TicketEdit,
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
                    text: self.trans('messages.ref_no'),
                    value: 'reference_no',
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
                    text: self.trans('messages.assigned_to'),
                    value: 'assigned_to',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.ticket_type'),
                    value: 'ticket_type',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.title'),
                    value: 'title',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.priority'),
                    value: 'priority',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.status'),
                    value: 'status',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.last_updated'),
                    value: 'last_updated',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            employees: [],
            ticket_types: [],
            statuses: [],
            filters: [],
            customers: [],
            priorities: [],
            tabs: 'tab-1',
            ticket_stats: [],
        };
    },
    watch: {
        pagination: {
            handler() {
                this.getTicketFromApi();
            },
        },
    },
    mounted() {
        const self = this;
        self.getFilters();
        self.getStatistics();
        self.$eventBus.$on('updateTicketsTable', data => {
            self.getTicketFromApi();
            self.getStatistics();
            self.getFilters();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateTicketsTable');
    },
    methods: {
        create() {
            this.$refs.ticketCreate.create();
        },
        getTicketFromApi() {
            const self = this;
            self.loading = true;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;
            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };

            if (self.filters.assigned_to) {
                params['assigned_to'] = self.filters.assigned_to;
            }

            if (self.filters.category_id) {
                params['category_id'] = self.filters.category_id;
            }

            if (self.filters.priority) {
                params['priority'] = self.filters.priority;
            }

            if (self.filters.status) {
                params['status'] = self.filters.status;
            }

            if (self.filters.customer_id) {
                params['customer_id'] = self.filters.customer_id;
            }

            axios
                .get('/tickets', {
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
        edit(ticket_id) {
            this.$refs.ticketEdit.edit(ticket_id);
        },
        deleteTicket(ticket) {
            console.log(ticket);
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/tickets/' + ticket.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getTicketFromApi();
                                self.getStatistics();
                                self.getFilters();
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
                .get('/tickets/get-filters')
                .then(function(response) {
                    self.employees = response.data.employees;
                    self.ticket_types = response.data.ticket_types;
                    self.statuses = response.data.statuses;
                    self.customers = response.data.customers;
                    self.priorities = response.data.priorities;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getStatistics() {
            const self = this;
            axios
                .get('/tickets-statistics')
                .then(function(response) {
                    self.ticket_stats = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
