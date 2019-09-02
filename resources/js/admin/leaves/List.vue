<template>
    <div class="component-wrap">
        <!-- apply leave -->
        <LeaveAdd ref="applyLeave"></LeaveAdd>
        <!-- edit applied leave -->
        <LeaveEdit ref="editAppliedLeave"></LeaveEdit>
        <v-card class="mb-3">
            <v-list>
                <v-list-group prepend-icon="filter_list">
                    <v-list-tile slot="activator">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ trans('messages.filters') }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile-content>
                        <v-container grid-list-md>
                            <v-layout wrap>
                                <v-flex xs12 md4 v-if="$can('superadmin')">
                                    <v-autocomplete
                                        item-text="name"
                                        item-value="id"
                                        :items="employees"
                                        v-model="leaves.user_id"
                                        :label="trans('messages.employee')"
                                        @change="getLeavesFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="value"
                                        item-value="key"
                                        :items="filterStatus"
                                        v-model="leaves.status"
                                        :label="trans('messages.status')"
                                        @change="getLeavesFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-autocomplete
                                        item-text="value"
                                        item-value="key"
                                        :items="dates"
                                        v-model="leaves.date"
                                        :label="trans('messages.date')"
                                        @change="getLeavesFromApi"
                                    ></v-autocomplete>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-list-tile-content>
                </v-list-group>
            </v-list>
        </v-card>
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_leaves') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('leaves.create')"
                    class="primary lighten-1"
                    dark
                    @click="$refs.applyLeave.create()"
                >
                    {{ trans('messages.apply_leave') }}
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
                    <span v-if="props.header.value == 'start_date'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'end_date'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'status'">
                        {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'reason'">
                        {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu transition="slide-x-transition" right>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <!-- status -->
                                <v-menu transition="slide-x-transition" offset-x open-on-hover>
                                    <v-list-tile slot="activator" v-if="$can('superadmin')">
                                        <v-list-tile-title>
                                            <v-icon small> check </v-icon>
                                            {{ trans('messages.status') }}
                                        </v-list-tile-title>
                                    </v-list-tile>
                                    <v-list dense>
                                        <v-list-tile
                                            v-for="status in statuses"
                                            :key="status.key"
                                            :disabled="props.item.status === status.key"
                                            @click="updateStatus(status.key, props.item)"
                                        >
                                            <v-list-tile-title>
                                                {{ status.value }}
                                            </v-list-tile-title>
                                        </v-list-tile>
                                    </v-list>
                                </v-menu>
                                <!-- /status -->
                                <v-list-tile
                                    v-if="
                                        ($can('leaves.edit') && props.item.status === 'pending') ||
                                            $can('superadmin')
                                    "
                                    @click="$refs.editAppliedLeave.edit(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="$can('superadmin')"
                                    @click="deleteLeaves(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td v-if="$can('superadmin')">{{ props.item.employee }}</td>
                    <td>{{ props.item.applied_date | formatDate }}</td>
                    <td>{{ props.item.start_date | formatDate }}</td>
                    <td>{{ props.item.end_date | formatDate }}</td>
                    <td>
                        <status-label :status="props.item.status"></status-label>
                    </td>
                    <td>{{ props.item.reason }}</td>
                </template>
            </v-data-table>
            <!-- /dataTable -->
        </v-card>
    </div>
</template>
<script>
import LeaveAdd from './Add';
import LeaveEdit from './Edit';
import StatusLabel from '../status/StatusLabel';
export default {
    components: {
        LeaveAdd,
        LeaveEdit,
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
                    text: self.trans('messages.applied_date'),
                    value: 'applied_date',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.start_date'),
                    value: 'start_date',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.end_date'),
                    value: 'end_date',
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
                    text: self.trans('messages.reason'),
                    value: 'reason',
                    align: 'left',
                    sortable: false,
                },
            ],
            items: [],
            statuses: [],
            leaves: [],
            employees: [],
            filterStatus: [],
            dates: [],
        };
    },
    watch: {
        pagination: {
            handler() {
                this.getLeavesFromApi();
            },
        },
    },
    mounted() {
        const self = this;

        if (self.$can('superadmin')) {
            var header = {
                text: self.trans('messages.name'),
                value: 'employee',
                align: 'left',
                sortable: true,
            };
            self.headers.splice(1, 0, header);
        }

        self.getFilters();

        self.$eventBus.$on('updateLeaveTable', data => {
            self.getLeavesFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateLeaveTable');
    },
    methods: {
        getLeavesFromApi() {
            this.loading = true;

            const { sortBy, descending, page, rowsPerPage } = this.pagination;
            const self = this;
            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };

            if (self.leaves.user_id) {
                params['user_id'] = self.leaves.user_id;
            }

            if (self.leaves.status) {
                params['status'] = self.leaves.status;
            }

            if (self.leaves.date) {
                params['date'] = self.leaves.date;
            }

            axios
                .get('/leaves', {
                    params: params,
                })
                .then(function(response) {
                    self.total_items = response.data.leaves.total;
                    self.items = response.data.leaves.data;
                    self.statuses = response.data.status;
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        deleteLeaves(leave) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/leaves/' + leave.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getLeavesFromApi();
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
        updateStatus(status, leave) {
            const self = this;
            axios
                .get('/leaves/update-status', {
                    params: { id: leave.id, status: status },
                })
                .then(function(response) {
                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });

                    if (response.data.success === true) {
                        leave.status = status;
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getFilters() {
            const self = this;
            axios
                .get('/leaves/get-filters')
                .then(function(response) {
                    self.employees = response.data.employees;
                    self.filterStatus = response.data.status;
                    self.dates = response.data.date;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
