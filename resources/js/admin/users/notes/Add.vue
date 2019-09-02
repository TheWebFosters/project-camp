<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900px">
            <!-- v-card -->
            <v-card>
                <v-card-title>
                    <v-icon medium>note_add</v-icon>
                    <span class="headline">
                        {{ trans('messages.add_note') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false">
                        <v-icon>clear</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="form_fields.heading"
                                    :label="trans('messages.heading')"
                                    v-validate="'required'"
                                    data-vv-name="form_fields.heading"
                                    :data-vv-as="trans('messages.heading')"
                                    :error-messages="errors.collect('form_fields.heading')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm12 md12>
                                <quill-editor v-model="form_fields.description"> </quill-editor>
                            </v-flex>

                            <v-flex xs12 md12>
                                <div class="dropzone" id="fileUploadAdd"></div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="close">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                        {{ trans('messages.save') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
            <!-- /v-card -->
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            userId: null,
            form_fields: [],
            dialog: false,
            dropzone: null,
            loading: false,
        };
    },
    methods: {
        create(user_id) {
            const self = this;
            self.userId = user_id;
            self.form_fields = [];
            self.form_fields.medias = [];
            self.$validator.reset();
            self.initDropzone();
            self.dialog = true;
        },
        initDropzone() {
            const self = this;
            if (self.dropzone) {
                self.dropzone.destroy();
            }
            self.dropzone = new Dropzone('div#fileUploadAdd', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                dictDefaultMessage: self.trans('messages.drop_document_here'),
                uploadMultiple: true,
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                success: function(file, response) {
                    if (response.success == true) {
                        self.form_fields.medias.push(response.file_name);
                    }
                },
            });
        },
        close() {
            const self = this;
            self.dropzone.destroy();
            self.dialog = false;
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, ['heading', 'description', 'medias']);
            data.user_id = self.userId;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/employee-notes', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateEmployeeNotesTable');
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
