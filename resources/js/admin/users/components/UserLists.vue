<template>
    <div class="component-wrap">
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
                                <v-flex xs12 sm3 md3 v-if="statistics.users > 0">
                                    <span class="subheading font-weight-medium info--text">
                                        {{ trans('messages.total') }}: {{ statistics.users }}
                                    </span>
                                </v-flex>
                                <v-flex xs12 sm3 md3 v-if="statistics.active > 0">
                                    <span class="subheading font-weight-medium success--text">
                                        {{ trans('messages.active') }}: {{ statistics.active }}
                                    </span>
                                </v-flex>
                                <v-flex xs12 sm3 md3 v-if="statistics.in_active > 0">
                                    <span class="subheading font-weight-medium warning--text">
                                        {{ trans('messages.in_active') }}:
                                        {{ statistics.in_active }}
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
                        <v-layout>
                            <v-flex xs12 sm12>
                                <v-container grid-list-md>
                                    <v-layout row wrap>
                                        <v-flex xs12 sm6 md6>
                                            <v-text-field
                                                prepend-icon="search"
                                                :label="trans('messages.filter_by_name')"
                                                v-model="filters.name"
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6 md6>
                                            <v-text-field
                                                prepend-icon="search"
                                                :label="trans('messages.filter_by_email')"
                                                v-model="filters.email"
                                            ></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                </v-container>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <v-card class="mt-3">
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_employees') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('employee.create')"
                    @click="$router.push({ name: 'users.create' })"
                    class="primary lighten-1"
                    dark
                >
                    {{ trans('messages.new_employee') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- data table -->
            <v-data-table
                v-bind:headers="headers"
                v-bind:pagination.sync="pagination"
                :items="items"
                :total-items="totalItems"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'name'">
                        <v-icon small>person</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'email'">
                        <v-icon small>email</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'roles'">
                        <v-icon small>control_point</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'last_login'">
                        <v-icon small>av_timer</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>
                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('employee.view')"
                                    @click="
                                        $router.push({
                                            name: 'users.view',
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
                                    v-if="$can('employee.edit')"
                                    @click="
                                        $router.push({
                                            name: 'users.edit',
                                            params: { id: props.item.id },
                                        })
                                    "
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile
                                    v-if="$can('employee.delete')"
                                    @click="trash(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.email }}</td>
                    <td>
                        <span v-for="role in props.item.roles">
                            {{ roleName(role) }}
                        </span>
                    </td>
                    <td>{{ props.item.last_login | formatDateTime }}</td>
                    <td>
                        <v-avatar outline>
                            <v-icon v-if="props.item.active != null" class="green--text"
                                >check_circle</v-icon
                            >
                            <v-icon class="grey--text" v-else>error_outline</v-icon>
                        </v-avatar>
                    </td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
export default {
    data() {
        const self = this;
        return {
            headers: [
                {
                    text: self.trans('messages.action'),
                    value: false,
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.name'),
                    value: 'name',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.email'),
                    value: 'email',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.roles'),
                    value: 'roles',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.last_login'),
                    value: 'last_login',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.active'),
                    value: 'active',
                    align: 'left',
                    sortable: false,
                },
            ],
            items: [],
            totalItems: 0,
            pagination: {
                rowsPerPage: 10,
            },

            filters: {
                name: '',
                email: '',
            },
            tabs: 'tab-1',
            statistics: [],
        };
    },
    mounted() {
        const self = this;
        self.getStatistics();
        self.$eventBus.$on(['USER_ADDED', 'USER_UPDATED', 'USER_DELETED', 'GROUP_ADDED'], () => {
            self.loadUsers(() => {});
        });
    },
    watch: {
        'pagination.page': function() {
            this.loadUsers(() => {});
        },
        'pagination.rowsPerPage': function() {
            this.loadUsers(() => {});
        },
        'filters.name': _.debounce(function() {
            const self = this;
            self.loadUsers(() => {});
        }, 700),
        'filters.email': _.debounce(function() {
            const self = this;
            self.loadUsers(() => {});
        }, 700),
    },
    methods: {
        trash(user) {
            const self = this;

            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/users/' + user.id)
                        .then(function(response) {

                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            self.getStatistics();
                            self.$eventBus.$emit('USER_DELETED');
                        })
                        .catch(function(error) {
                            self.$store.commit('hideLoader');

                            if (error.response) {
                                self.$store.commit('showSnackbar', {
                                    message: error.response.data.msg,
                                    color: response.data.success,
                                });
                            } else if (error.request) {
                                console.log(error.request);
                            } else {
                                console.log('Error', error.message);
                            }
                        });
                },
                cancelCb: () => {
                    console.log('CANCEL');
                },
            });
        },
        loadUsers(cb) {
            const self = this;

            let params = {
                name: self.filters.name,
                email: self.filters.email,
                page: self.pagination.page,
                per_page: self.pagination.rowsPerPage,
            };

            axios.get('/admin/users', { params: params }).then(function(response) {
                self.items = response.data.data;
                self.totalItems = response.data.total;
                self.pagination.totalItems = response.data.total;
            });
        },
        roleName(role) {
            var name = [];

            if (role.type == 'employee' || role.type == null) {
                name.push(role.name[0].toUpperCase() + role.name.slice(1));
            }

            return name.join();
        },
        getStatistics() {
            const self = this;
            axios
                .get('/admin/user-statistics')
                .then(function(response) {
                    self.statistics['active'] = response.data.active;
                    self.statistics['in_active'] = response.data.in_active;
                    self.statistics['users'] = response.data.users;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
