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
                        <v-layout row wrap>
                            <v-flex xs12 sm3 md3>
                                <v-checkbox v-model="followUp" :label="trans('messages.follow_up')">
                                </v-checkbox>
                            </v-flex>
                            <v-flex xs12 sm4 md4 v-show="followUp">
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.contacted_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="contacted_date"
                                                    name="contacted_date"
                                                    :config="flatPickerDateTime()"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        const self = this;
        return {
            dialog: false,
            note: [],
            dropzone: null,
            lead_id: null,
            followUp: false,
            contacted_date: null,
            loading: false,
        };
    },
    methods: {
        edit(data) {
            const self = this;
            self.lead_id = data.lead_id;
            self.followUp = false;
            self.contacted_date = null;
            axios
                .get('/admin/lead-notes/' + data.id + '/edit', {
                    params: { lead_id: data.lead_id },
                })
                .then(function(response) {
                    self.contacted_date = response.data.contacted_date;
                    self.note = response.data.lead_note;
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
            data.lead_id = self.lead_id;
            data.contacted_date = self.contacted_date;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/admin/lead-notes/' + self.note.id, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateLeadNotesTable');
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
