<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <span class="headline">
                        <v-icon>receipt</v-icon>
                        {{ trans('messages.send_invoice_reminder') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon small @click="dialog = false">
                        <v-icon>clear</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="form_fields.email"
                                    :label="trans('messages.email')"
                                    v-validate="'required'"
                                    data-vv-name="email"
                                    :data-vv-as="trans('messages.email')"
                                    :error-messages="errors.collect('email')"
                                    required
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="form_fields.subject"
                                    :label="trans('messages.email_subject')"
                                    v-validate="'required'"
                                    data-vv-name="email_subject"
                                    :data-vv-as="trans('messages.email_subject')"
                                    :error-messages="errors.collect('email_subject')"
                                    required
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                {{ trans('messages.email_body') }}
                                <quill-editor v-model="form_fields.body"> </quill-editor>
                            </v-flex>
                            <v-flex xs12 sm4 md4>
                                <v-checkbox
                                    :label="trans('messages.attach_pdf')"
                                    color="primary"
                                    value="1"
                                    v-model="form_fields.attachment"
                                    hide-details
                                ></v-checkbox>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn flat :loading="loading" :disabled="loading" @click="sendNotification">
                        {{ trans('messages.send') }}
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
            transaction_id: null,
            project_id: null,
            form_fields: [],
            loading: false,
        };
    },
    methods: {
        create(data) {
            const self = this;
            self.transaction_id = data.transaction_id;
            self.project_id = data.project_id;
            axios
                .get('invoices/get-invoice-reminder', {
                    params: { transaction_id: data.transaction_id, project_id: data.project_id },
                })
                .then(function(response) {
                    self.form_fields = response.data;
                    self.form_fields.attachment = response.data.attachment.toString();
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        sendNotification() {
            const self = this;
            let data = _.pick(self.form_fields, ['email', 'subject', 'body', 'attachment']);
            data.transaction_id = self.transaction_id;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('invoices/post-invoice-reminder', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
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
