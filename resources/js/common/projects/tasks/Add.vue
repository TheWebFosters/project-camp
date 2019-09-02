<template>
    <v-layout row justify-center>
        <!-- category Add -->
        <CategoryAdd ref="categoryAdd"></CategoryAdd>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <v-icon>assignment</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_task') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false"> <v-icon>clear</v-icon> </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 md12 v-if="create_task_from_dashboard">
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="project"
                                    v-model="projectId"
                                    :label="trans('messages.project')"
                                    v-validate="'required'"
                                    data-vv-name="project"
                                    :data-vv-as="trans('messages.project')"
                                    :error-messages="errors.collect('project')"
                                    required
                                    @change="getProjectMember"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 md12>
                                <v-text-field
                                    v-model="task.subject"
                                    :label="trans('messages.subject')"
                                    v-validate="'required'"
                                    data-vv-name="subject"
                                    :data-vv-as="trans('messages.subject')"
                                    :error-messages="errors.collect('subject')"
                                    required
                                >
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                {{ trans('messages.description') }}
                                <quill-editor v-model="task.description"> </quill-editor>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 md4>
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.start_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="start_date"
                                                    name="start_date"
                                                    required
                                                    :config="flatPickerDateTime()"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-flex>

                            <v-flex xs12 md4>
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.due_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="due_date"
                                                    name="due_date"
                                                    required
                                                    :config="flatPickerDateTime()"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-select
                                    item-text="value"
                                    item-value="key"
                                    :items="priority"
                                    v-model="task.priority"
                                    :label="trans('messages.priority')"
                                ></v-select>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 md4 v-if="billingType == 'task_hours'">
                                <v-text-field
                                    v-model="task.hourly_rate"
                                    :label="trans('messages.hourly_rate')"
                                >
                                </v-text-field>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="taskMembers"
                                    v-model="task.user_id"
                                    :label="trans('messages.assign_to')"
                                    multiple
                                    v-validate="'required'"
                                    data-vv-name="assign_to"
                                    :data-vv-as="trans('messages.assign_to')"
                                    :error-messages="errors.collect('assign_to')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 md5 v-if="projectId">
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="categories | filterCategories(projectId)"
                                    v-model="category_id"
                                    :label="trans('messages.category')"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex md1 v-if="projectId">
                                <v-btn @click="createCategory" small color="primary" fab dark>
                                    <v-icon>add</v-icon>
                                </v-btn>
                            </v-flex>
                            <v-flex xs12 md2 v-if="$hasRole('employee')">
                                <v-checkbox
                                    :label="trans('messages.show_to_customer')"
                                    color="primary"
                                    value="1"
                                    v-model="task.show_to_customer"
                                    hide-details
                                ></v-checkbox>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                        {{ trans('messages.save') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import CategoryAdd from '../category/Add';
export default {
    components: {
        CategoryAdd,
    },
    data() {
        return {
            dialog: false,
            task: [],
            start_date: null,
            due_date: null,
            projectId: null,
            priority: [],
            taskMembers: [],
            billingType: null,
            project: [],
            create_task_from_dashboard: false,
            categories: [],
            category_id: null,
            loading: false,
        };
    },
    mounted() {
        const self = this;
        self.$eventBus.$on('updateCategoryList', data => {
            self.categories.push(data);
            self.category_id = data.id;
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateCategoryList');
    },
    filters: {
        filterCategories: function(categories, project_id) {
            var project_id = project_id;
            var filteredCategories = [];

            _.forEach(categories, function(category) {
                if (category.project_id == project_id) {
                    filteredCategories.push(category);
                }
            });

            return filteredCategories;
        },
    },
    methods: {
        create(project_id) {
            const self = this;
            self.projectId = project_id;
            if (project_id === null) {
                self.create_task_from_dashboard = true;
            }
            axios
                .get('/project-tasks/create', { params: { project_id: self.projectId } })
                .then(function(response) {
                    self.task = [];
                    self.start_date = null;
                    self.category_id = null;
                    self.due_date = null;
                    self.$validator.reset();
                    self.priority = response.data.priority;
                    self.dialog = true;
                    self.taskMembers = response.data.project.members;
                    self.billingType = response.data.project.billing_type;
                    self.project = response.data.project;
                    self.categories = response.data.categories;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        store() {
            const self = this;
            let data = {
                project_id: self.projectId,
                subject: self.task.subject,
                hourly_rate: self.task.hourly_rate,
                priority: self.task.priority,
                description: self.task.description,
                show_to_customer: self.task.show_to_customer,
                user_id: self.task.user_id,
                start_date: self.start_date,
                due_date: self.due_date,
                category_id: self.category_id,
            };

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/project-tasks', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateTaskTable', response.data.task);
                            }

                            if (self.create_task_from_dashboard === true) {
                                self.$eventBus.$emit('updateDashboard', response.data.task);
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        getProjectMember() {
            const self = this;
            axios
                .get('/projects/' + self.projectId + '/members', { params: { type: 'task' } })
                .then(function(response) {
                    self.taskMembers = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        createCategory() {
            const self = this;
            var data = { type: 'tasks', project_id: self.projectId };
            self.$refs.categoryAdd.create(data);
        },
    },
};
</script>
