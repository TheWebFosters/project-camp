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
                    <v-btn color="green darken-1" flat @click="dialog = false">
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
            dialog: false,
            form_fields: [],
            customer_id: '',
            dropzone: null,
            loading: false,
        };
    },
    methods: {
        add(data) {
            this.form_fields = [];
            this.form_fields.medias = [];
            this.$validator.reset();
            this.customer_id = data.customer_id;
            this.initDropzone();
            this.dialog = true;
        },
        initDropzone() {
            if (this.dropzone) {
                this.dropzone.destroy();
            }

            const self = this;
            self.dropzone = new Dropzone('div#fileUploadAdd', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                uploadMultiple: true,
                headers: { 'X-CSRF-TOKEN': _token },
                dictDefaultMessage: self.trans('messages.drop_document_here'),
                autoProcessQueue: true,
                success: function(file, response) {
                    if (response.success == true) {
                        self.form_fields.medias.push(response.file_name);
                    }
                },
            });
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, ['heading', 'description', 'medias']);
            data.customer_id = self.customer_id;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/customer-notes', data)
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
