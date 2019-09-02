<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900px">
            <!-- v-card -->
            <v-card>
                <v-card-title>
                    <v-icon medium>alarm_on</v-icon>
                    <span class="headline">
                        {{ trans('messages.add_reminder') }}
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
                                    v-model="form_fields.remind_for"
                                    :label="trans('messages.remind_for')"
                                    v-validate="'required'"
                                    data-vv-name="form_fields.remind_for"
                                    :data-vv-as="trans('messages.remind_for')"
                                    :error-messages="errors.collect('form_fields.remind_for')"
                                    required
                                ></v-text-field>
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
                                                    {{ trans('messages.remind_on') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="remind_on"
                                                    v-validate="'required'"
                                                    name="remind_on"
                                                    required
                                                    :config="flatPickerDateTime()"
                                                    :data-vv-as="trans('messages.remind_on')"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                        <div class="v-messages theme--light error--text">
                                            {{ errors.first('remind_on') }}
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="employees"
                                    v-model="form_fields.remind_to"
                                    :label="trans('messages.remind_to')"
                                    v-validate="'required'"
                                    data-vv-name="remind_to"
                                    :data-vv-as="trans('messages.remind_to')"
                                    :error-messages="errors.collect('remind_to')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <v-textarea
                                    v-model="form_fields.notes"
                                    :label="trans('messages.notes')"
                                    rows="3"
                                ></v-textarea>
                            </v-flex>
                            <v-flex xs12 sm4 md4>
                                <v-checkbox
                                    v-model="send_email"
                                    :label="trans('messages.send_email')"
                                >
                                    <Popover
                                        slot="append"
                                        :helptext="trans('messages.auto_email_will_be_sent')"
                                    >
                                    </Popover>
                                </v-checkbox>
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
import Popover from '../../../admin/popover/Popover';
export default {
    components: {
        Popover,
    },
    data() {
        const self = this;
        return {
            form_fields: [],
            remind_on: null,
            dialog: false,
            employees: [],
            lead_id: null,
            loading: false,
            send_email: false,
        };
    },
    methods: {
        create(data) {
            const self = this;
            self.lead_id = data.lead_id;
            self.form_fields = [];
            self.send_email = false;
            self.remind_on = null;
            self.$validator.reset();
            self.getEmployee();
            self.dialog = true;
        },
        getEmployee() {
            const self = this;
            axios
                .get('/admin/users-all')
                .then(function(response) {
                    self.employees = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        store() {
            const self = this;

            let data = _.pick(self.form_fields, ['remind_for', 'remind_to', 'notes']);
            data.remind_on = self.remind_on;
            data.lead_id = self.lead_id;
            data.send_email = self.send_email;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/reminders', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateReminderTable');
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
