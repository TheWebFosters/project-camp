<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900px">
            <!-- v-card -->
            <v-card>
                <v-card-title>
                    <v-icon>contact_mail</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_contact') }}
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
                                    v-model="contactData.name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="contactData.email"
                                    :label="trans('messages.email')"
                                    v-validate="'required|email'"
                                    data-vv-name="email"
                                    :data-vv-as="trans('messages.email')"
                                    :error-messages="errors.collect('email')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    type="password"
                                    v-model="contactData.password"
                                    :label="trans('messages.password')"
                                    v-validate="'min:6'"
                                    :messages="trans('messages.password_edit_help')"
                                    data-vv-name="contactData.password"
                                    :data-vv-as="trans('messages.password')"
                                    :error-messages="errors.collect('contactData.password')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="contactData.mobile"
                                    :label="trans('messages.mobile')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="contactData.alternate_num"
                                    :label="trans('messages.alternate_num')"
                                >
                                </v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="contactData.skype"
                                    :label="trans('messages.skype')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="contactData.linkedin"
                                    :label="trans('messages.linkedin')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="contactData.facebook"
                                    :label="trans('messages.facebook')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                    v-model="contactData.twitter"
                                    :label="trans('messages.twitter')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-select
                                    :items="gender_types"
                                    v-model="contactData.gender"
                                    :label="trans('messages.gender')"
                                ></v-select>
                            </v-flex>

                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    rows="4"
                                    v-model="contactData.address"
                                    :label="trans('messages.address')"
                                >
                                </v-textarea>
                            </v-flex>

                            <v-flex xs12 sm6 md6>
                                <v-textarea
                                    rows="4"
                                    v-model="contactData.note"
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
                                    v-model="contactData.send_email"
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
                    <v-btn
                        color="success"
                        @click="update(contactData.id)"
                        :loading="loading"
                        :disabled="loading"
                    >
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
            contactData: [],
            dialog: false,
            gender_types: [],
            send_email: false,
            loading: false,
        };
    },
    methods: {
        edit(data) {
            const self = this;
            self.contactData = [];
            self.$validator.reset();
            axios
                .get('/admin/contacts/' + data.id + '/edit', {
                    params: { customer_id: data.customer_id },
                })
                .then(function(response) {
                    self.contactData = response.data.user;
                    self.gender_types = response.data.gender_types;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        update(id) {
            const self = this;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/admin/contacts/' + id, self.contactData)
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
