<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900px">
            <!-- v-card -->
            <v-card>
                <v-card-title>
                    <v-icon>contact_mail</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_contact') }}
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
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="form_fields.name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('form_fields.name')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.email"
                                    :label="trans('messages.email')"
                                    v-validate="'required|email'"
                                    data-vv-name="form_fields.email"
                                    :data-vv-as="trans('messages.email')"
                                    :error-messages="errors.collect('form_fields.email')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    type="password"
                                    v-model="form_fields.password"
                                    :label="trans('messages.password')"
                                    v-validate="'required|min:6'"
                                    data-vv-name="form_fields.password"
                                    :data-vv-as="trans('messages.password')"
                                    :error-messages="errors.collect('form_fields.password')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="form_fields.mobile"
                                    :label="trans('messages.mobile')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
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
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="form_fields.linkedin"
                                    :label="trans('messages.linkedin')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="form_fields.facebook"
                                    :label="trans('messages.facebook')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="form_fields.twitter"
                                    :label="trans('messages.twitter')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-select
                                    :items="gender_types"
                                    v-model="form_fields.gender"
                                    :label="trans('messages.gender')"
                                ></v-select>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    rows="4"
                                    v-model="form_fields.address"
                                    :label="trans('messages.address')"
                                >
                                </v-textarea>
                            </v-flex>

                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    rows="4"
                                    v-model="form_fields.note"
                                    :label="trans('messages.note')"
                                >
                                </v-textarea>
                            </v-flex>
                        </v-layout>
                        <v-layout row>
                            <v-flex xs12 sm4>
                                <v-checkbox
                                    :label="trans('messages.send_email')"
                                    value="true"
                                    v-model="form_fields.send_email"
                                >
                                </v-checkbox>
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
            gender_types: [],
            send_email: false,
            loading: false,
        };
    },
    created() {
        const self = this;
        self.customer_id = self.$route.params.id;
    },
    methods: {
        create() {
            const self = this;
            self.form_fields = [];
            self.$validator.reset();
            axios
                .get('/admin/contacts/create')
                .then(function(response) {
                    self.gender_types = response.data.gender_types;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        store() {
            const self = this;

            let data = _.pick(self.form_fields, [
                'name',
                'email',
                'password',
                'mobile',
                'alternate_num',
                'address',
                'skype',
                'linkedin',
                'facebook',
                'twitter',
                'gender',
                'note',
                'send_email',
            ]);
            data.customer_id = self.customer_id;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/contacts', data)
                        .then(function(response) {
                            self.loading = false;

                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateContactTable');
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
