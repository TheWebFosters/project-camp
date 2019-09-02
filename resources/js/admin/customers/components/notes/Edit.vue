<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900px">
            <!-- v-card -->
            <v-card>
                <v-card-title>
                    <v-icon medium>note_add</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_note') }}
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
                                    v-model="note.heading"
                                    :label="trans('messages.heading')"
                                    v-validate="'required'"
                                    data-vv-name="note.heading"
                                    :data-vv-as="trans('messages.heading')"
                                    :error-messages="errors.collect('note.heading')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm12 md12>
                                <quill-editor v-model="note.description"> </quill-editor>
                            </v-flex>

                            <v-flex xs12 md12>
                                <div class="dropzone" id="fileUploadEdit"></div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
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
            <!-- /v-card -->
        </v-dialog>
    </v-layout>
</template>

<script>
export default {
    data() {
        return {
            dialog: false,
            note: [],
            dropzone: null,
            customer_id: null,
            loading: false,
        };
    },
    methods: {
        edit(data) {
            const self = this;
            self.customer_id = data.customer_id;
            axios
                .get('/admin/customer-notes/' + data.id + '/edit', {
                    params: { customer_id: data.customer_id },
                })
                .then(function(response) {
                    self.note = response.data;
                    self.note.medias = [];
                    self.initDropzone();
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        initDropzone() {
            if (this.dropzone) {
                this.dropzone.destroy();
            }

            const self = this;
            self.dropzone = new Dropzone('div#fileUploadEdit', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                uploadMultiple: true,
                headers: { 'X-CSRF-TOKEN': _token },
                dictDefaultMessage: self.trans('messages.drop_document_here'),
                autoProcessQueue: true,
                success: function(file, response) {
                    if (response.success == true) {
                        self.note.medias.push(response.file_name);
                    }
                },
            });
        },
        update() {
            const self = this;
            let data = _.pick(self.note, ['heading', 'description', 'medias']);
            data.customer_id = self.customer_id;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/admin/customer-notes/' + self.note.id, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateContactNotesTable');
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
