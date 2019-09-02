<template>
    <div class="component-wrap">
        <!-- create task -->
        <TaskFormAdd ref="taskAdd"></TaskFormAdd>
        <!-- edit task -->
        <TaskFormEdit ref="taskEdit"></TaskFormEdit>
        <!-- view task -->
        <TaskShow ref="taskShow"></TaskShow>

        <!-- Filters -->
        <v-layout row mb-3>
            <v-flex xs12 sm8>
                <v-card>
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
                                <v-layout>
                                    <v-flex xs12 sm12>
                                        <v-container grid-list-md>
                                            <v-layout row wrap>
                                                <v-flex xs12 md4 v-if="ShowAllTask === true">
                                                    <v-autocomplete
                                                        item-text="name"
                                                        item-value="id"
                                                        :items="project_list"
                                                        v-model="filters.project_id"
                                                        :label="trans('messages.project')"
                                                        @change="filterChanged"
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex
                                                    xs12
                                                    md4
                                                    v-if="user_filter_type === 'checkbox'"
                                                >
                                                    <v-checkbox
                                                        :label="trans('messages.assigned_to_me')"
                                                        color="primary"
                                                        value="1"
                                                        v-model="filters.assigned_to_me"
                                                        @change="filterChanged"
                                                    ></v-checkbox>
                                                </v-flex>
                                                <v-flex
                                                    xs12
                                                    md4
                                                    v-else-if="user_filter_type === 'dropdown'"
                                                >
                                                    <v-autocomplete
                                                        item-text="name"
                                                        item-value="id"
                                                        :items="usersList"
                                                        v-model="filters.user_id"
                                                        :label="trans('messages.members')"
                                                        @change="filterChanged"
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-select
                                                        item-text="name"
                                                        item-value="value"
                                                        :items="statuses"
                                                        v-model="filters.status"
                                                        :label="trans('messages.status')"
                                                        @change="filterChanged"
                                                    ></v-select>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-flex>
                                </v-layout>
                            </v-list-tile-content>
                        </v-list-group>
                    </v-list>
                </v-card>
            </v-flex>
            <v-flex xs12 sm4 class="text-xs-right">
                <v-toolbar class="" floating>
                    <v-spacer></v-spacer>
                    <v-btn
                        v-if="$can('project.' + projectId + '.task.create')"
                        class="primary lighten-1 mr-3"
                        dark
                        @click="create"
                    >
                        {{ trans('messages.new_task') }}
                        <v-icon right dark>add</v-icon>
                    </v-btn>

                    <v-btn-toggle>
                        <v-btn flat @click="toggleView('grid')" :class="grid_class">
                            <v-icon>view_module</v-icon>
                        </v-btn>
                        <v-btn flat @click="toggleView('list')" :class="list_class">
                            <v-icon>view_list</v-icon>
                        </v-btn>
                    </v-btn-toggle>
                </v-toolbar>
            </v-flex>
        </v-layout>

        <!-- task List -->
        <v-layout row wrap v-if="taskLists.length">
            <v-flex xs12 sm12 v-if="view_style == 'list'">
                <v-list subheader three-line>
                    <v-subheader> {{ trans('messages.tasks_list') }} </v-subheader>
                    <template @click="" v-for="(task, index) in taskLists">
                        <v-list-tile
                            :key="task.id"
                            @click=""
                            :class="getListBg(task.is_completed)"
                            @mouseover="currentHover = task.id"
                            @mouseleave="currentHover = null"
                        >
                            <v-list-tile-action>
                                <v-layout wrap>
                                    <v-flex
                                        xs12
                                        md12
                                        v-if="$can('project.' + task.project_id + '.task.edit')"
                                        @click="markAsDone(task)"
                                    >
                                        <v-checkbox v-model="task.is_completed"></v-checkbox>
                                    </v-flex>
                                </v-layout>
                            </v-list-tile-action>
                            <v-list-tile-content
                                v-if="$can('project.' + task.project_id + '.task.view')"
                                @click="view(task)"
                            >
                                <v-list-tile-title>
                                    <strike v-if="task.is_completed"> {{ task.subject }} </strike>
                                    <span v-else>{{ task.subject }} </span>
                                    <small>({{ task.task_id }})</small>
                                </v-list-tile-title>
                                <v-list-tile-sub-title>
                                    <v-chip :color="dueDateColor(task)" small v-if="task.due_date">
                                        {{ task.due_date | formatDateTime }}
                                    </v-chip>
                                    <span v-for="member in task.task_members" :key="member.id">
                                        <v-chip color="accent" small>{{ member.name }}</v-chip>
                                    </span>
                                    <v-btn
                                        v-if="
                                            $can('project.' + task.project_id + '.task.edit') &&
                                                currentHover == task.id
                                        "
                                        flat
                                        icon
                                        @click.stop="editTask(task)"
                                        color="primary"
                                        small
                                    >
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <v-btn
                                        v-if="
                                            $can('project.' + task.project_id + '.task.delete') &&
                                                currentHover == task.id
                                        "
                                        flat
                                        icon
                                        @click.stop="deleteTask(task)"
                                        color="red"
                                        small
                                    >
                                        <v-icon small> delete </v-icon>
                                    </v-btn>
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-divider></v-divider>
                    </template>

                    <v-list-tile>
                        <v-btn
                            outline
                            small
                            color="info"
                            dark
                            block
                            v-if="!_.isNull(url)"
                            @click="getTaskList"
                        >
                            {{ trans('messages.load_more') }}
                        </v-btn>
                    </v-list-tile>
                </v-list>
            </v-flex>

            <v-flex xs12 sm12 v-if="view_style == 'grid'">
                <v-layout row style="overflow-x: auto;" class="scrollbar">
                    <v-flex v-for="(category, key) in projectCategories" :key="key">
                        <v-card color="grey darken-1" width="300" class="mr-2">
                            <v-card-title class="pb-0">
                                <div class="headline">
                                    {{
                                        category.name
                                            ? category.name
                                            : trans('messages.uncategorized')
                                    }}
                                </div>
                            </v-card-title>

                            <v-container
                                fluid
                                grid-list-md
                                style="height: 80vh;overflow-y: auto;"
                                class="scrollbar mb-3"
                            >
                                <v-layout
                                    row
                                    wrap
                                    v-if="
                                        taskLists[0][category.id] &&
                                            taskLists[0][category.id].length
                                    "
                                >
                                    <v-flex v-for="(task, k) in taskLists[0][category.id]" :key="k">
                                        <v-card
                                            v-if="$can('project.' + task.project_id + '.task.view')"
                                            :class="getListBg(task.is_completed, task.id)"
                                            class="mb-3"
                                            @mouseover="currentHover = task.id"
                                            @mouseleave="currentHover = null"
                                        >
                                            <v-card-title
                                                class="pt-2 pb-2 text-link"
                                                @click="view(task)"
                                            >
                                                <strike v-if="task.is_completed">
                                                    {{ task.subject }}
                                                </strike>
                                                <span v-else>{{ task.subject }} </span>
                                                <small>({{ task.task_id }})</small>
                                                <v-spacer></v-spacer>

                                                <v-tooltip top>
                                                    <v-btn
                                                        v-if="currentHover == task.id"
                                                        icon
                                                        slot="activator"
                                                    >
                                                        <v-icon small> remove_red_eye </v-icon>
                                                    </v-btn>
                                                    <span>{{ trans('messages.view') }}</span>
                                                </v-tooltip>
                                            </v-card-title>
                                            <v-card-text class="pt-0 pb-0" style="padding: 5px;">
                                                <v-layout row wrap>
                                                    <v-flex md5 xs12>
                                                        <v-tooltip top>
                                                            <v-chip
                                                                :color="dueDateColor(task)"
                                                                small
                                                                v-if="task.due_date"
                                                                slot="activator"
                                                            >
                                                                {{ task.due_date | formatDateTime }}
                                                            </v-chip>
                                                            <span>{{
                                                                trans('messages.due_date')
                                                            }}</span>
                                                        </v-tooltip>
                                                    </v-flex>
                                                    <v-flex
                                                        md7
                                                        xs12
                                                        style="padding-top: 12px;"
                                                        class="text-xs-right pr-2"
                                                    >
                                                        <v-tooltip top>
                                                            <small
                                                                v-if="task.notes.length"
                                                                slot="activator"
                                                            >
                                                                <v-icon small>chat</v-icon>
                                                                {{ task.notes.length }}
                                                            </small>
                                                            <span>
                                                                {{
                                                                    trans(
                                                                        'messages.task_has_comments',
                                                                        { count: task.notes.length }
                                                                    )
                                                                }}
                                                            </span>
                                                        </v-tooltip>
                                                        <v-tooltip top>
                                                            <small
                                                                v-if="task.description !== null"
                                                                slot="activator"
                                                            >
                                                                <v-icon small>subject</v-icon>
                                                            </small>
                                                            <span>
                                                                {{ trans('messages.task_has_des') }}
                                                            </span>
                                                        </v-tooltip>
                                                    </v-flex>
                                                    <v-flex xs12 class="pl-2">
                                                        <avatar
                                                            :members="task.task_members"
                                                            :maxCount="5"
                                                        ></avatar>
                                                    </v-flex>
                                                </v-layout>
                                            </v-card-text>
                                            <v-card-actions
                                                v-if="currentHover == task.id"
                                                class="pt-0 pb-0"
                                            >
                                                <v-spacer></v-spacer>
                                                <v-tooltip top>
                                                    <div
                                                        v-if="
                                                            $can(
                                                                'project.' +
                                                                    task.project_id +
                                                                    '.task.edit'
                                                            )
                                                        "
                                                        @click="markAsDone(task)"
                                                        slot="activator"
                                                    >
                                                        <v-checkbox
                                                            v-model="task.is_completed"
                                                            height="0"
                                                        >
                                                        </v-checkbox>
                                                    </div>
                                                    <span>
                                                        {{
                                                            task.is_completed
                                                                ? trans(
                                                                      'messages.mark_as_incompleted'
                                                                  )
                                                                : trans(
                                                                      'messages.mark_as_completed'
                                                                  )
                                                        }}
                                                    </span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <v-btn
                                                        v-if="
                                                            $can(
                                                                'project.' +
                                                                    task.project_id +
                                                                    '.task.edit'
                                                            )
                                                        "
                                                        flat
                                                        icon
                                                        @click="editTask(task)"
                                                        slot="activator"
                                                    >
                                                        <v-icon> edit </v-icon>
                                                    </v-btn>
                                                    <span>{{ trans('messages.edit') }}</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <v-btn
                                                        v-if="
                                                            $can(
                                                                'project.' +
                                                                    task.project_id +
                                                                    '.task.delete'
                                                            )
                                                        "
                                                        flat
                                                        icon
                                                        @click="deleteTask(task)"
                                                        slot="activator"
                                                    >
                                                        <v-icon> delete_outline </v-icon>
                                                    </v-btn>
                                                    <span>{{ trans('messages.delete') }}</span>
                                                </v-tooltip>
                                            </v-card-actions>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap v-else>
                                    <v-flex>
                                        {{ trans('messages.no_task_in_category') }}
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
        <!-- /task List -->
        <v-layout row wrap v-else>
            <v-flex xs12 sm12>
                <div>
                    <v-alert :value="true" color="info" outline>
                        {{ trans('messages.no_records_found') }}
                    </v-alert>
                </div>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import TaskFormAdd from './Add';
import TaskFormEdit from './Edit';
import TaskShow from './Show';
import avatar from '../components/Avatar';

export default {
    components: {
        TaskFormAdd,
        TaskFormEdit,
        TaskShow,
        avatar,
    },
    props: {
        id: {
            required: false,
        },
        ShowAllTask: {
            required: false,
        },
    },
    data() {
        const self = this;
        return {
            projectId: null,
            taskLists: [],
            url: null,
            filters: [],
            user_filter_type: null,
            usersList: [{ id: 0, name: self.trans('messages.all') }],
            statuses: [
                {
                    name: self.trans('messages.all'),
                    value: '',
                },
                {
                    name: self.trans('messages.completed'),
                    value: 'completed',
                },
                {
                    name: self.trans('messages.incompleted'),
                    value: 'incompleted',
                },
                {
                    name: self.trans('messages.over_due'),
                    value: 'over_due',
                },
            ],
            view_style: 'grid',
            currentHover: null,
            projectCategories: [],
            grid_class: '',
            list_class: '',
            project_list: [{ id: 0, name: self.trans('messages.all') }],
        };
    },
    created() {
        const self = this;
        self.filters.status = 'incompleted';
        self.url = '/project-tasks';
        if (self.ShowAllTask === true) {
            self.projectId = self.id;
            self.view_style = 'list';
            self.getTaskList(self.projectId);
        } else {
            self.projectId = self.$route.params.id;
        }

        self.getFilterData();
        self.$eventBus.$on('updateTaskTable', data => {
            self.url = '/project-tasks';
            self.taskLists = [];
            self.getTaskList(self.projectId);
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateTaskTable');
    },
    methods: {
        create() {
            const self = this;
            self.$refs.taskAdd.create(self.projectId);
        },
        getTaskList(projectId) {
            const self = this;

            if (self.view_style === 'grid') {
                self.grid_class = 'v-btn--active';
                self.list_class = '';
            } else {
                self.grid_class = '';
                self.list_class = 'v-btn--active';
            }
            if (self.ShowAllTask === true || self.$can('project.' + projectId + '.task.view')) {
                if (self.ShowAllTask) {
                    if (self.filters.project_id) {
                        self.projectId = self.filters.project_id;
                    } else {
                        self.projectId = null;
                    }
                }

                var params = { project_id: self.projectId };

                if (self.filters.assigned_to_me) {
                    params['assigned_to_me'] = self.filters.assigned_to_me;
                }
                if (self.filters.user_id) {
                    params['user_id'] = self.filters.user_id;
                }
                if (self.filters.status) {
                    params['status'] = self.filters.status;
                }

                params['view_style'] = self.view_style;

                axios
                    .get(self.url, { params: params })
                    .then(function(response) {
                        var tasks = [];
                        if (self.view_style == 'grid') {
                            tasks = response.data.tasks;
                        } else {
                            tasks = response.data.tasks.data;
                        }
                        self.taskLists = _.concat(self.taskLists, tasks);
                        self.url = _.get(response, 'data.next_page_url', null);
                        self.projectCategories = response.data.project_categories;
                        self.projectCategories.push({ id: '' });
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        },
        markAsDone(task) {
            const self = this;
            axios
                .get('/project-tasks/mark-completed', {
                    params: {
                        project_id: task.project_id,
                        is_completed: task.is_completed,
                        taskId: task.id,
                    },
                })
                .then(function(response) {
                    if (self.ShowAllTask === true) {
                        self.$eventBus.$emit('updateDashboard');
                    }

                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        view(task) {
            const self = this;
            self.$refs.taskShow.view(task);
        },
        dueDateColor(task) {
            var date_passed = moment().isAfter(task.due_date);

            if (date_passed && task.is_completed == 0) {
                return 'red';
            } else {
                return 'primary grey--text text--lighten-1';
            }
        },
        deleteTask(task) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/project-tasks/' + task.id, {
                            params: { project_id: task.project_id },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                if (self.view_style == 'grid') {
                                    var cat_id = task.category_id ? task.category_id : '';
                                    self.taskLists[0][cat_id].splice(
                                        self.taskLists[0][cat_id].indexOf(task),
                                        1
                                    );
                                } else {
                                    self.taskLists.splice(self.taskLists.indexOf(task), 1);
                                }
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

        getFilterData() {
            const self = this;
            axios
                .get('/project-tasks/filter-data/' + self.projectId)
                .then(function(response) {
                    if (response.data.members) {
                        self.user_filter_type = 'dropdown';
                        self.usersList = _.concat(self.usersList, response.data.members);
                    } else {
                        self.user_filter_type = 'checkbox';
                    }

                    if (response.data.projects) {
                        self.project_list = _.concat(self.project_list, response.data.projects);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        filterChanged() {
            const self = this;
            self.url = '/project-tasks';
            self.taskLists = [];
            self.getTaskList(self.projectId);
        },

        getListBg(is_completed, task_id = null) {
            const self = this;
            var bg_class = '';
            if (task_id !== null && task_id == self.currentHover) {
                bg_class += 'elevation-15 ';
            }

            if (is_completed) {
                bg_class += 'list-faded';
            }

            return bg_class;
        },
        toggleView(view_style) {
            const self = this;
            self.view_style = view_style;
            self.url = '/project-tasks';
            self.taskLists = [];
            self.getTaskList(self.projectId);
        },
        editTask(task) {
            const self = this;
            var data = { task_id: task.id, project_id: task.project_id };
            self.$refs.taskEdit.edit(data);
        },
    },
};
</script>
<style type="text/css">
.list-faded {
    opacity: 0.5;
}
.text-link {
    cursor: pointer;
}
.v-input--selection-controls {
    padding-top: 10px !important;
}
</style>
