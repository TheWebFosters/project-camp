<template>
    <v-layout wrap>
        <v-flex xs12 sm12 md12>
            <v-card elevation="4">
                <v-card-title>
                    <v-icon medium>account_circle</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_profile') }}
                    </span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm6>
                                <v-text-field
                                    :label="trans('messages.name')"
                                    v-model="form_fields.name"
                                    v-validate="'required'"
                                    data-vv-name="form_fields.name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('form_fields.name')"
                                    required
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6>
                                <v-text-field
                                    :label="trans('messages.email')"
                                    v-model="form_fields.email"
                                    v-validate="'required|email'"
                                    data-vv-name="form_fields.email"
                                    :data-vv-as="trans('messages.email')"
                                    :error-messages="errors.collect('form_fields.email')"
                                    required
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                    :label="trans('messages.password')"
                                    :messages="trans('messages.password_edit_help')"
                                    type="password"
                                    v-model="password"
                                    v-validate="'min:6'"
                                    data-vv-name="password"
                                    :data-vv-as="trans('messages.password')"
                                    :error-messages="errors.collect('password')"
                                ></v-text-field>
                            </v-flex>
                            <!-- communication details -->
                            <v-flex xs12 sm12 md12>
                                <v-icon small>contact_mail</v-icon>
                                <span class="subheading">
                                    {{ trans('messages.communication_details') }}
                                </span>
                                <v-divider class="mb-2 mt-1"> </v-divider>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    type="number"
                                    v-model="form_fields.mobile"
                                    :label="trans('messages.mobile')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    type="number"
                                    v-model="form_fields.alternate_num"
                                    :label="trans('messages.alternate_num')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.skype"
                                    :label="trans('messages.skype')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.linkedin"
                                    :label="trans('messages.linkedin')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.facebook"
                                    :label="trans('messages.facebook')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.twitter"
                                    :label="trans('messages.twitter')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    v-model="form_fields.home_address"
                                    :label="trans('messages.home_address')"
                                    rows="3"
                                >
                                </v-textarea>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    v-model="form_fields.current_address"
                                    :label="trans('messages.current_address')"
                                    rows="3"
                                >
                                </v-textarea>
                            </v-flex>
                            <!-- personal information -->
                            <v-flex xs12 sm12 md12>
                                <v-icon small>contact_phone</v-icon>
                                <span class="subheading">
                                    {{ trans('messages.personal_details') }}
                                </span>
                                <v-divider class="mb-2 mt-1"> </v-divider>
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
                                                    {{ trans('messages.date_of_birth') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="birth_date"
                                                    name="date_of_birth"
                                                    :config="flatPickerDate()"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.guardian_name"
                                    :label="trans('messages.guardian_name')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-select
                                    :items="gender_types"
                                    v-model="form_fields.gender"
                                    :label="trans('messages.gender')"
                                ></v-select>
                            </v-flex>
                            <v-flex xs12 md12>
                                <div class="dropzone" id="fileUploadEdit"></div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                        {{ trans('messages.update') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            userId: null,
            form_fields: [],
            birth_date: null,
            gender_types: [],
            password: null,
            dropzone: null,
            loading: false,
        };
    },
    created() {
        const self = this;
        self.userId = self.$route.params.id;
        self.edit(self.userId);
    },
    methods: {
        edit(userId) {
            const self = this;
            self.$validator.reset();
            axios
                .get('/manage-profiles/' + userId + '/edit')
                .then(function(response) {
                    self.form_fields = response.data.user;
                    self.birth_date = response.data.user.birth_date;
                    self.gender_types = response.data.gender_types;
                    self.form_fields.media = '';
                    self.initDropzone();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        initDropzone() {
            const self = this;
            if (self.dropzone) {
                self.dropzone.destroy();
            }
            self.dropzone = new Dropzone('div#fileUploadEdit', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                uploadMultiple: true,
                maxFiles: 1,
                acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF',
                dictDefaultMessage: self.trans('messages.drop_image_here'),
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                init: function() {
                    var prevFile;
                    this.on('addedfile', function() {
                        if (typeof prevFile !== 'undefined') {
                            this.removeFile(prevFile);
                        }
                    });
                    this.on('success', function(file, response) {
                        prevFile = file;
                    });
                },
                success: function(file, response) {
                    if (response.success == true) {
                        self.form_fields.media = response.file_name;
                    }
                },
            });
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, [
                'name',
                'email',
                'mobile',
                'alternate_num',
                'skype',
                'linkedin',
                'facebook',
                'twitter',
                'gender',
                'home_address',
                'current_address',
                'guardian_name',
                'gender',
                'media',
            ]);

            data.birth_date = self.birth_date;
            data.password = self.password;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/manage-profiles/' + self.userId, data)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });
                            self.loading = false;
                            if (response.data.success === true) {
                                self.goBack();
                            }
                        })
                        .catch(function(error) {
                            if (error.response) {
                                self.$store.commit('showSnackbar', {
                                    message: error.response.data.message,
                                    color: 'error',
                                });
                            }
                        });
                }
            });
        },
    },
};
</script>
