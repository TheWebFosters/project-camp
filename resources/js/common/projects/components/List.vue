<template>
    <div class="component-wrap">
        <!-- create project -->
        <ProjectFormAdd ref="projectAdd"></ProjectFormAdd>
        <!-- Edit project -->
        <ProjectFormEdit ref="projectEdit"></ProjectFormEdit>

        <!-- tab -->
        <div v-if="$can('superadmin')">
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
                                <v-layout row-wrap>
                                    <v-flex xs12 sm3 md3 v-if="statistics.not_started > 0">
                                        <span class="subheading font-weight-medium primary--text">
                                            {{ trans('messages.not_started') }}:
                                            {{ statistics.not_started }}
                                        </span>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3 v-if="statistics.in_progress > 0">
                                        <span class="subheading font-weight-medium info--text">
                                            {{ trans('messages.in_progress') }}:
                                            {{ statistics.in_progress }}
                                        </span>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3 v-if="statistics.cancelled > 0">
                                        <span class="subheading font-weight-medium error--text">
                                            {{ trans('messages.cancelled') }}:
                                            {{ statistics.cancelled }}
                                        </span>
                                    </v-flex>

                                    <v-flex xs12 sm3 md3 v-if="statistics.on_hold > 0">
                                        <span class="subheading font-weight-medium warning--text">
                                            {{ trans('messages.on_hold') }}:
                                            {{ statistics.on_hold }}
                                        </span>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3 v-if="statistics.completed > 0">
                                        <span class="subheading font-weight-medium success--text">
                                            {{ trans('messages.completed') }}:
                                            {{ statistics.completed }}
                                        </span>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3>
                                        <span class="subheading font-weight-medium cyan--text">
                                            {{ trans('messages.total') }}: {{ statistics.total }}
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
                                            <v-flex xs12 md4>
                                                <v-autocomplete
                                                    item-text="value"
                                                    item-value="key"
                                                    :items="status"
                                                    v-model="filters.status"
                                                    :label="trans('messages.status')"
                                                    @change="filterChanged"
                                                ></v-autocomplete>
                                            </v-flex>
                                            <v-flex xs12 md4>
                                                <v-autocomplete
                                                    item-text="name"
                                                    item-value="id"
                                                    :items="categories"
                                                    v-model="filters.category_id"
                                                    :label="trans('messages.category')"
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
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </div>
        <!-- /tab -->
        <v-layout row pt-3>
            <v-flex xs12 sm12>
                <v-card class="elevation-3">
                    <v-card-title primary-title xs8 sm8>
                        <div>
                            <div class="headline">
                                {{ trans('messages.all_projects') }}
                            </div>
                        </div>
                        <v-spacer></v-spacer>
                        <v-btn
                            class="primary lighten-1"
                            v-if="$can('project.create')"
                            dark
                            @click="create"
                        >
                            {{ trans('messages.new_project') }}
                            <v-icon right dark>add</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout row wrap v-if="projectData.length">
                                <v-flex
                                    xs12
                                    sm6
                                    md4
                                    v-for="project in projectData"
                                    :key="project.id"
                                >
                                    <v-toolbar dense flat color="info">
                                        <v-toolbar-title>
                                            <div class="headline">
                                                <router-link
                                                    :to="{
                                                        name: 'projects.project-tasks.list',
                                                        params: { id: project.id },
                                                    }"
                                                >
                                                    <v-tooltip top>
                                                        <span class="white--text" slot="activator">
                                                            {{ project.name }}
                                                        </span>
                                                        <span>{{ project.name }}</span>
                                                    </v-tooltip>
                                                </router-link>
                                            </div>
                                        </v-toolbar-title>
                                        <v-spacer></v-spacer>
                                        <v-btn icon @click="markAsFavorite(project)">
                                            <v-icon :color="toggleFavorite(project)"> star </v-icon>
                                        </v-btn>
                                        <v-menu
                                            transition="slide-x-transition"
                                            right
                                            v-if="
                                                $can('project.' + project.id + '.status') ||
                                                    $can('project.' + project.id + '.edit') ||
                                                    $can('project.' + project.id + '.delete')
                                            "
                                        >
                                            <v-btn slot="activator" icon>
                                                <v-icon>more_vert</v-icon>
                                            </v-btn>

                                            <v-list>
                                                <v-list-tile
                                                    @click="
                                                        $router.push({
                                                            name: 'projects.project-tasks.list',
                                                            params: { id: project.id },
                                                        })
                                                    "
                                                >
                                                    <v-list-tile-title>
                                                        <v-icon small>
                                                            visibility
                                                        </v-icon>
                                                        {{ trans('messages.view') }}
                                                    </v-list-tile-title>
                                                </v-list-tile>
                                                <!-- status -->
                                                <v-menu
                                                    transition="slide-x-transition"
                                                    offset-x
                                                    open-on-hover
                                                >
                                                    <v-list-tile
                                                        slot="activator"
                                                        v-if="
                                                            $can(
                                                                'project.' + project.id + '.status'
                                                            )
                                                        "
                                                    >
                                                        <v-list-tile-title>
                                                            <v-icon small> check </v-icon>
                                                            {{ trans('messages.status') }}
                                                        </v-list-tile-title>
                                                    </v-list-tile>
                                                    <v-list dense>
                                                        <v-list-tile
                                                            :disabled="
                                                                project.status === status.key
                                                            "
                                                            @click="
                                                                updateStatus(status.key, project)
                                                            "
                                                            v-for="status in statuses"
                                                            :key="status.key"
                                                        >
                                                            <v-list-tile-title>
                                                                {{ status.value }}
                                                            </v-list-tile-title>
                                                        </v-list-tile>
                                                    </v-list>
                                                </v-menu>
                                                <!-- /status -->
                                                <v-list-tile
                                                    v-if="$can('project.' + project.id + '.edit')"
                                                    @click="edit(project.id)"
                                                >
                                                    <v-list-tile-title>
                                                        <v-icon small> edit </v-icon>
                                                        {{ trans('messages.edit') }}
                                                    </v-list-tile-title>
                                                </v-list-tile>
                                                <v-list-tile
                                                    v-if="$can('project.' + project.id + '.delete')"
                                                    @click="deleteProject(project.id)"
                                                >
                                                    <v-list-tile-title>
                                                        <v-icon small> delete_outline </v-icon>
                                                        {{ trans('messages.delete') }}
                                                    </v-list-tile-title>
                                                </v-list-tile>
                                            </v-list>
                                        </v-menu>
                                    </v-toolbar>
                                    <v-card elevation="6" height="120px">
                                        <v-card-text style="max-height:70px !important">
                                            <v-layout row wrap>
                                                <v-flex xs12 sm9 md9>
                                                    <div v-if="project.customer !== null">
                                                        <v-icon small>business_center</v-icon>
                                                        {{ project.customer.company }}
                                                    </div>
                                                    <div>
                                                        <v-badge right color="red lighten-2">
                                                            <template
                                                                slot="badge"
                                                                v-if="project.overdue_task != 0"
                                                            >
                                                                <v-tooltip top>
                                                                    <span slot="activator">
                                                                        <small>{{
                                                                            project.overdue_task
                                                                        }}</small>
                                                                    </span>
                                                                    <span>{{
                                                                        trans(
                                                                            'messages.overdue_task'
                                                                        )
                                                                    }}</span>
                                                                </v-tooltip>
                                                            </template>
                                                            <v-icon small> check </v-icon>
                                                            {{ trans('messages.status') }} :
                                                            {{
                                                                trans('messages.' + project.status)
                                                            }}
                                                        </v-badge>
                                                    </div>
                                                </v-flex>
                                                <v-flex xs12 sm3 md3>
                                                    <v-spacer></v-spacer>
                                                    <v-progress-circular
                                                        :rotate="180"
                                                        :size="60"
                                                        :width="7"
                                                        :value="
                                                            projectProgress(
                                                                project.tasks_count,
                                                                project.completed_task
                                                            )
                                                        "
                                                        color="teal"
                                                    >
                                                        {{
                                                            projectProgress(
                                                                project.tasks_count,
                                                                project.completed_task
                                                            )
                                                        }}
                                                    </v-progress-circular>
                                                </v-flex>
                                            </v-layout>
                                        </v-card-text>
                                        <v-card-actions>
                                            <avatar :members="project.members"></avatar>
                                        </v-card-actions>
                                    </v-card>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-else>
                                <v-flex xs12 sm12>
                                    <div>
                                        <v-alert
                                            v-if="loading"
                                            :value="true"
                                            color="warning"
                                            outline
                                        >
                                            {{ trans('messages.loading') }}
                                        </v-alert>

                                        <v-alert
                                            :value="true"
                                            color="info"
                                            outline
                                            v-if="projectData.length == 0 && loading == false"
                                        >
                                            {{ trans('messages.no_records_found') }}
                                        </v-alert>
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                        <v-flex
                            xs8
                            sm8
                            offset-xs2
                            offset-sm2
                            align-self-center
                            v-if="projectData.length > 0 && loading == false"
                        >
                            <v-btn
                                outline
                                small
                                color="info"
                                block
                                v-if="!_.isNull(url)"
                                v-on:click="getDataFromApi"
                            >
                                {{ trans('messages.load_more') }}
                            </v-btn>
                        </v-flex>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import ProjectFormAdd from '../components/Add';
import ProjectFormEdit from '../components/Edit';
import avatar from '../components/Avatar';
export default {
    components: {
        ProjectFormAdd,
        ProjectFormEdit,
        avatar,
    },
    data() {
        const self = this;
        return {
            projectData: [],
            statuses: [],
            url: null,
            customers: [{ id: 0, company: self.trans('messages.all') }],
            status: [{ key: '', value: self.trans('messages.all') }],
            categories: [{ id: 0, name: self.trans('messages.all') }],
            filters: [],
            tabs: 'tab-1',
            statistics: [],
            loading: false,
        };
    },
    created() {
        const self = this;
        self.url = '/projects';
        self.getDataFromApi();
        self.getFilterData();
        self.$eventBus.$on('updateProjectTable', data => {
            self.url = '/projects';
            self.projectData = [];
            self.getDataFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateProjectTable');
    },
    methods: {
        create() {
            const self = this;
            self.$refs.projectAdd.create();
        },
        getDataFromApi() {
            const self = this;
            self.loading = true;
            var params = {};
            if (self.filters.status) {
                params['status'] = self.filters.status;
            }
            if (self.filters.category_id) {
                params['category_id'] = self.filters.category_id;
            }
            if (self.filters.customer_id) {
                params['customer_id'] = self.filters.customer_id;
            }

            axios
                .get(self.url, {
                    params: params,
                })
                .then(function(response) {
                    self.loading = false;
                    self.projectData = _.concat(self.projectData, response.data.projects.data);
                    self.statuses = response.data.status;
                    self.url = _.get(response, 'data.projects.next_page_url', null);
                    self.getStatistics();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        edit(id) {
            const self = this;
            self.$refs.projectEdit.edit(id);
        },
        deleteProject(id) {
            const self = this;

            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/projects/' + id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.url = '/projects';
                                self.projectData = [];
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
        markAsFavorite(project) {
            const self = this;
            axios
                .get('/projects/mark-favorite', {
                    params: { id: project.id, favorite: project.is_favorited },
                })
                .then(function(response) {
                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });
                    project.is_favorited = response.data.favorite;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        updateStatus(status, project) {
            const self = this;
            axios
                .get('/projects/update-status', {
                    params: { id: project.id, status: status },
                })
                .then(function(response) {
                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });

                    if (response.data.success === true) {
                        project.status = status;
                        self.getStatistics();
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        toggleFavorite(project) {
            if (project.is_favorited) {
                return 'yellow darken-2';
            } else {
                return 'grey lighten-1';
            }
        },
        getFilterData() {
            const self = this;
            if (self.$can('superadmin')) {
                axios
                    .get('/projects/create')
                    .then(function(response) {
                        self.customers = _.concat(self.customers, response.data.customers);
                        self.status = _.concat(self.status, response.data.status);
                        self.categories = _.concat(self.categories, response.data.categories);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        },

        filterChanged() {
            const self = this;
            self.url = '/projects';
            self.projectData = [];
            self.getDataFromApi();
        },
        getStatistics() {
            const self = this;
            if (self.$can('superadmin')) {
                axios
                    .get('/projects-statistics')
                    .then(function(response) {
                        self.statistics = response.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        },
    },
};
</script>
