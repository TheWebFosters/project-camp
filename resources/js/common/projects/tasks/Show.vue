<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
                <v-card-title>
                    <span class="headline">
                        <v-icon>assignment</v-icon>
                        {{ task.subject }} <small>({{ task.task_id }})</small>
                    </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="closeDialog">clear</v-icon><br />
                </v-card-title>
                <v-subheader>
                    <v-chip color="indigo" text-color="white" small>
                        <v-avatar> <v-icon>assessment</v-icon> </v-avatar>
                        {{ project.name }}
                    </v-chip>
                    <v-chip v-if="task.due_date" :color="dueDateColor" text-color="white" small>
                        <v-avatar> <v-icon>date_range</v-icon> </v-avatar>
                        {{ task.due_date | formatDateTime }}
                    </v-chip>
                    <v-chip color="green" text-color="white" v-if="task.show_to_customer" small>
                        {{ trans('messages.visible_to_customer') }}
                    </v-chip>
                    <v-chip v-if="task.priority" :color="taskPriority" text-color="white" small>
                        {{ task.priority }}
                    </v-chip>
                    <span v-for="member in task_members" :key="member.id">
                        <v-chip color="accent" text-color="white" small> {{ member.name }} </v-chip>
                    </span>
                </v-subheader>
                <v-divider></v-divider>
                <v-card-text>
                    <!-- task description -->
                    <v-layout row wrap>
                        <v-flex xs12 md12 v-show="viewable">
                            <h3>
                                <v-icon small>dehaze</v-icon>
                                {{ trans('messages.description') }}
                                <v-btn
                                    v-if="$can('project.' + project.id + '.task.edit')"
                                    flat
                                    icon
                                >
                                    <v-icon small @click="editDescription()"> edit </v-icon>
                                </v-btn>
                            </h3>
                            <p v-html="task.description" id="task_description"></p>
                        </v-flex>

                        <v-flex xs12 md12 v-show="edit">
                            <v-icon small>dehaze</v-icon>
                            {{ trans('messages.description') }}
                            <div>
                                <quill-editor v-model="task.description"> </quill-editor>
                                <v-btn color="success" flat @click="closeEdit">
                                    {{ trans('messages.close') }}
                                </v-btn>
                                <v-btn color="success" @click="updateDescription">
                                    {{ trans('messages.save') }}
                                </v-btn>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <!-- task comment -->
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 md12>
                                <h3>
                                    <v-icon small>comment</v-icon>
                                    {{ trans('messages.add_comment') }}
                                </h3>
                                <v-flex xs12 md12>
                                    <v-textarea
                                        :label="trans('messages.comment')"
                                        v-model="note.description"
                                        auto-grow
                                        rows="5"
                                        v-validate="'required'"
                                        data-vv-name="comment"
                                        :data-vv-as="trans('messages.comment')"
                                        :error-messages="errors.collect('comment')"
                                        required
                                    ></v-textarea>
                                </v-flex>
                            </v-flex>
                            <v-flex xs12 md12 v-show="can_file_upload">
                                <h3>
                                    <v-icon small>attachment</v-icon>
                                    {{ trans('messages.file_upload') }}
                                </h3>
                                <v-flex xs12 md12>
                                    <div class="dropzone" id="fileupload"></div>
                                </v-flex>
                            </v-flex>
                            <v-btn
                                color="blue lighten-1 white--text"
                                @click="toggleUploadDocumenet"
                            >
                                {{ trans('messages.upload_doc') }}
                            </v-btn>
                            <v-btn color="success" @click="saveComment">
                                {{ trans('messages.save') }}
                            </v-btn>
                        </v-layout>
                    </v-container>
                    <v-divider></v-divider>
                    <!-- activity list -->
                    <h3>
                        <v-icon small>notes</v-icon>
                        {{ trans('messages.activity') }}
                    </h3>
                    <v-container grid-list-md>
                        <v-layout v-for="comment in comments" :key="comment.id">
                            <v-flex xs12 md12>
                                <v-card light>
                                    <v-card-text>
                                        <p>
                                            <v-icon small>create</v-icon>
                                            {{ comment.user.name }}
                                            <v-icon small>date_range</v-icon>
                                            {{ comment.created_at | formatDateTime }}
                                            <v-btn icon small @click="deleteComment(comment)">
                                                <v-icon small>delete_outline</v-icon>
                                            </v-btn>
                                            <br />
                                            {{ comment.description }} <br />
                                            <v-icon
                                                small
                                                v-show="comment.media && comment.media.length"
                                            >
                                                attachment
                                            </v-icon>
                                            <span v-for="media in comment.media" :key="media.id">
                                                <a :href="media.download_url">
                                                    {{ media.display_name }}
                                                </a>
                                                &nbsp;
                                            </span>
                                        </p>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-layout>
                        <v-flex xs6 md6>
                            {{
                                trans('messages.added_this_task_on', {
                                    name: taskActivity.name,
                                })
                            }}
                            {{ task.created_at | formatDateTime }}
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            task: [],
            edit: false,
            viewable: true,
            comments: [],
            project: [],
            taskActivity: '',
            task_members: [],
            dropzone: null,
            medias: [],
            note: [],
            can_file_upload: false,
        };
    },
    computed: {
        taskPriority: function() {
            const self = this;
            if (self.task.priority == 'low') {
                return 'green';
            }

            if (self.task.priority == 'medium') {
                return 'lime';
            }

            if (self.task.priority == 'high') {
                return 'success';
            }

            if (self.task.priority == 'urgent') {
                return 'red';
            }
        },
        dueDateColor: function() {
            const self = this;

            var date_passed = moment().isAfter(self.task.due_date);

            if (date_passed && self.task.is_completed == 0) {
                return 'red';
            } else {
                return 'primary';
            }
        },
    },
    methods: {
        view(task) {
            const self = this;
            self.note.medias = [];
            self.getDataFromApi(task.id, task.project_id);
        },
        getDataFromApi(task_id, project_id) {
            const self = this;
            axios
                .get('/project-tasks/' + task_id, {
                    params: { project_id: project_id },
                })
                .then(function(response) {
                    self.task = response.data;
                    self.taskActivity = response.data.task_creator;
                    self.project = response.data.project;
                    self.task_members = response.data.task_members;
                    self.$validator.reset();
                    self.getComments();
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        editDescription() {
            const self = this;
            self.edit = true;
            self.viewable = false;
        },
        closeEdit() {
            const self = this;
            self.edit = false;
            self.viewable = true;
        },
        updateDescription() {
            const self = this;
            axios
                .get('/project-task-description/', {
                    params: {
                        id: self.task.id,
                        project_id: self.task.project_id,
                        description: self.task.description,
                    },
                })
                .then(function(response) {
                    self.task = response.data.task;
                    self.edit = false;
                    self.viewable = true;
                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        saveComment() {
            const self = this;
            let data = {
                project_task_id: self.task.id,
                description: self.note.description,
                medias: self.note.medias,
            };

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    axios
                        .post('/project-comments', data)
                        .then(function(response) {
                            if (self.dropzone) {
                                self.dropzone.destroy();
                            }
                            self.can_file_upload = false;
                            self.note.description = '';
                            self.$validator.reset();
                            if (response.data.success === true) {
                                self.getComments();
                                self.note.medias = [];
                            }
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateTaskTable');
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        getComments() {
            const self = this;
            axios
                .get('/project-comments', {
                    params: { project_task_id: self.task.id },
                })
                .then(function(response) {
                    self.comments = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        closeDialog() {
            const self = this;
            self.note = [];
            self.dialog = false;
        },
        initDropzone() {
            const self = this;
            if (self.dropzone) {
                self.dropzone.destroy();
            }
            self.dropzone = new Dropzone('div#fileupload', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                dictDefaultMessage: self.trans('messages.drop_document_here'),
                uploadMultiple: true,
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                success: function(file, response) {
                    if (response.success == true) {
                        self.note.medias.push(response.file_name);
                    }
                },
            });
        },
        toggleUploadDocumenet() {
            const self = this;
            self.initDropzone();
            self.can_file_upload = !self.can_file_upload;
        },
        deleteComment(comment) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/project-comments/' + comment.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getComments();
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
    },
};
</script>
