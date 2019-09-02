<template>
    <div class="component-wrap">
        <!-- create Lead -->
        <LeadFormAdd ref="leadAdd"></LeadFormAdd>
        <LeadFormEdit ref="leadEdit"></LeadFormEdit>
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
                                <v-flex xs12 ms6 md6>
                                    <p class="subheading primary--text text-md-center">
                                        {{ trans('messages.status') }}
                                    </p>
                                    <v-divider></v-divider>
                                    <v-container grid-list-md>
                                        <v-layout row wrap>
                                            <v-flex
                                                xs12
                                                sm4
                                                md4
                                                v-for="(status, index) in statistics_statuses"
                                                :key="index"
                                            >
                                                <span
                                                    class="subheading font-weight-medium"
                                                    v-if="!_.isNull(status.name)"
                                                >
                                                    {{ status.name }}: {{ status.status_count }}
                                                </span>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-flex>
                                <v-flex xs12 ms6 md6>
                                    <p class="subheading cyan--text text-md-center">
                                        {{ trans('messages.source') }}
                                    </p>
                                    <v-divider></v-divider>
                                    <v-container grid-list-md>
                                        <v-layout row wrap>
                                            <v-flex
                                                xs12
                                                sm4
                                                md4
                                                v-for="(source, index) in statistics_sources"
                                                :key="index"
                                            >
                                                <span
                                                    class="subheading font-weight-medium"
                                                    v-if="!_.isNull(source.name)"
                                                >
                                                    {{ source.name }}: {{ source.source_count }}
                                                </span>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md12>
                                    <span class="subheading font-weight-medium success--text">
                                        {{ trans('messages.total') }}:
                                        {{ getTotalLead(statistics_statuses) }}
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
                            <v-flex xs12 ms12 md12>
                                <v-container grid-list-md>
                                    <v-layout row wrap>
                                        <v-flex xs12 sm4>
                                            <v-text-field
                                                prepend-icon="search"
                                                :label="trans('messages.search')"
                                                single-line
                                                v-model="search"
                                                @keyup="searchLead"
                                            ></v-text-field>
                                        </v-flex>
                                        <!-- filter -->
                                        <v-flex xs12 sm4>
                                            <v-autocomplete
                                                prepend-icon="filter_list"
                                                item-text="name"
                                                item-value="id"
                                                :items="statuses"
                                                v-model="filters.status_id"
                                                :label="trans('messages.status')"
                                                @change="getDataFromApi"
                                            ></v-autocomplete>
                                        </v-flex>
                                        <v-flex xs12 sm4>
                                            <v-autocomplete
                                                prepend-icon="filter_list"
                                                item-text="name"
                                                item-value="id"
                                                :items="sources"
                                                v-model="filters.source_id"
                                                :label="trans('messages.source')"
                                                @change="getDataFromApi"
                                            ></v-autocomplete>
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
            <v-card-title primary-title xs8 sm8>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_leads') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('customer.create')"
                    class="primary lighten-1"
                    dark
                    @click="create"
                >
                    {{ trans('messages.new_lead') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
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
                    <span v-else-if="props.header.value == 'status'">
                        <v-icon>check</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'mobile'">
                        <v-icon>phone</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'source'">
                        <v-icon>pageview</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'assigned_to'">
                        <v-icon>account_circle</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'contacted_date'">
                        <v-icon>calendar_today</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <!-- status -->
                                <v-menu transition="slide-x-transition" offset-x open-on-hover>
                                    <v-list-tile slot="activator" v-if="$can('customer.create')">
                                        <v-list-tile-title>
                                            <v-icon small> check </v-icon>
                                            {{ trans('messages.status') }}
                                        </v-list-tile-title>
                                    </v-list-tile>
                                    <v-list dense>
                                        <v-list-tile
                                            v-for="status in statuses"
                                            :key="status.id"
                                            :disabled="props.item.status_id === status.id"
                                            v-if="!_.includes([0], status.id)"
                                            @click="updateStatus(status.id, props.item)"
                                        >
                                            <v-list-tile-title>
                                                {{ status.name }}
                                            </v-list-tile-title>
                                        </v-list-tile>
                                    </v-list>
                                </v-menu>
                                <!-- /status -->
                                <v-list-tile
                                    v-if="$can('customer.view')"
                                    @click="
                                        $router.push({
                                            name: 'lead.show',
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
                                    @click="editLead(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('customer.delete')"
                                    @click="deleteLead(props.item)"
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
                    <td>{{ props.item.status }}</td>
                    <td>{{ props.item.mobile }}</td>
                    <td>{{ props.item.source }}</td>
                    <td>{{ props.item.assigned_to }}</td>
                    <td>
                        {{ props.item.contacted_date | formatDateTime }}
                    </td>
                </template>
            </v-data-table>
            <!-- /Data table-->
        </v-card>
    </div>
</template>
<script>
import LeadFormAdd from '../customers/components/Add';
import LeadFormEdit from './Edit';
export default {
    components: {
        LeadFormAdd,
        LeadFormEdit,
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
                    text: self.trans('messages.status'),
                    value: 'status',
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
                    text: self.trans('messages.source'),
                    value: 'source',
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
                    text: self.trans('messages.contacted_date'),
                    value: 'contacted_date',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            search: '',
            filters: [],
            statuses: [],
            sources: [],
            tabs: 'tab-1',
            statistics_sources: [],
            statistics_statuses: [],
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
        self.getStatistics();
        self.$eventBus.$on('updateLeadsTable', data => {
            self.getDataFromApi();
            self.getStatistics();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateLeadsTable');
    },
    methods: {
        create() {
            var templateType = { template: 'lead' };
            this.$refs.leadAdd.create(templateType);
        },
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

            if (self.search) {
                params['term'] = self.search;
            }

            if (self.filters.status_id) {
                params['status_id'] = self.filters.status_id;
            }

            if (self.filters.source_id) {
                params['source_id'] = self.filters.source_id;
            }

            axios
                .get('/admin/leads', {
                    params: params,
                })
                .then(function(response) {
                    self.statuses = response.data.statuses;
                    self.sources = response.data.sources;
                    self.total_items = response.data.lead.total;
                    self.items = response.data.lead.data;
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        searchLead() {
            const self = this;
            self.getDataFromApi();
        },
        deleteLead(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/leads/' + item.id)
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
        editLead(item) {
            var data = { id: item.id };
            this.$refs.leadEdit.create(data);
        },
        updateStatus(status_id, lead) {
            const self = this;
            axios
                .get('/admin/leads-update-status', {
                    params: { status_id: status_id, id: lead.id },
                })
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
        getStatistics() {
            const self = this;
            axios
                .get('/admin/lead-statistics')
                .then(function(response) {
                    self.statistics_sources = response.data.sources;
                    self.statistics_statuses = response.data.statuses;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getTotalLead(statuses) {
            var total = 0;

            _.forEach(statuses, function(status) {
                total = _.add(total, parseInt(status.status_count));
            });

            return total;
        },
    },
};
</script>
