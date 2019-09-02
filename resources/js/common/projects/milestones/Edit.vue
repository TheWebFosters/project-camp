<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <v-icon medium>outlined_flag</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_milestone') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="dialog = false">clear</v-icon><br />
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="milestone.name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
                                    required
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
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
                                                    v-validate="'required'"
                                                    name="due_date"
                                                    required
                                                    :config="flatPickerDateTime()"
                                                    :data-vv-as="trans('messages.due_date')"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                        <div class="v-messages theme--light error--text">
                                            {{ errors.first('due_date') }}
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                {{ trans('messages.description') }}
                                <quill-editor v-model="milestone.description"> </quill-editor>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn color="success" @click="update" :loading="loading" :disabled="loading">
                        {{ trans('messages.update') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            due_date: null,
            milestone: [],
            milestoneId: null,
            project_id: null,
            loading: false,
        };
    },
    methods: {
        edit(data) {
            const self = this;
            self.milestoneId = data.id;
            self.project_id = data.project_id;
            self.due_date = null;
            self.milestone = [];
            self.$validator.reset();
            axios
                .get('/project-milestones/' + data.id + '/edit', {
                    params: { project_id: data.project_id },
                })
                .then(function(response) {
                    self.milestone = response.data;
                    self.due_date = response.data.due_date;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        update() {
            const self = this;

            let data = {
                name: self.milestone.name,
                description: self.milestone.description,
                due_date: self.due_date,
                project_id: self.project_id,
            };

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/project-milestones/' + self.milestoneId, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateMilestoneList', '');
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
    },
};
</script>
