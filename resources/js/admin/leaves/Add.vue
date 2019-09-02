<template>
    <div>
        <v-layout row justify-center>
            <v-dialog v-model="dialog" width="800">
                <v-card>
                    <v-card-title>
                        <v-icon medium>work_off</v-icon>
                        <span class="headline">
                            {{ trans('messages.apply_leave') }}
                        </span>
                        <v-spacer></v-spacer>
                        <v-btn flat icon @click="dialog = false"> <v-icon>clear</v-icon> </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout row wrap>
                                <v-flex xs12 md6>
                                    <div class="v-input v-text-field theme--light">
                                        <div class="v-input__control">
                                            <div class="v-input__slot">
                                                <div class="v-text-field__slot">
                                                    <label
                                                        aria-hidden="true"
                                                        class="v-label v-label--active theme--light flat_picker_label"
                                                    >
                                                        {{ trans('messages.start_date') }}
                                                    </label>
                                                    <flat-pickr
                                                        v-model="start_date"
                                                        v-validate="'required'"
                                                        name="start_date"
                                                        required
                                                        :config="flatPickerDate()"
                                                        :data-vv-as="trans('messages.start_date')"
                                                    ></flat-pickr>
                                                </div>
                                            </div>
                                            <div class="v-messages theme--light error--text">
                                                {{ errors.first('start_date') }}
                                            </div>
                                        </div>
                                    </div>
                                </v-flex>
                                <v-flex xs12 md6>
                                    <div class="v-input v-text-field theme--light">
                                        <div class="v-input__control">
                                            <div class="v-input__slot">
                                                <div class="v-text-field__slot">
                                                    <label
                                                        aria-hidden="true"
                                                        class="v-label v-label--active theme--light flat_picker_label"
                                                    >
                                                        {{ trans('messages.end_date') }}
                                                    </label>
                                                    <flat-pickr
                                                        v-model="end_date"
                                                        v-validate="'required'"
                                                        name="end_date"
                                                        required
                                                        :config="flatPickerDate()"
                                                        :data-vv-as="trans('messages.end_date')"
                                                    ></flat-pickr>
                                                </div>
                                            </div>
                                            <div class="v-messages theme--light error--text">
                                                {{ errors.first('end_date') }}
                                            </div>
                                        </div>
                                    </div>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs12 sm12 md12>
                                    <v-textarea
                                        rows="7"
                                        v-model="reason"
                                        :label="trans('messages.reason')"
                                        v-validate="'required'"
                                        data-vv-name="reason"
                                        :data-vv-as="trans('messages.reason')"
                                        :error-messages="errors.collect('reason')"
                                        required
                                    ></v-textarea>
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
                            @click="store"
                            :loading="loading"
                            :disabled="loading"
                        >
                            {{ trans('messages.apply') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            dialog: false,
            start_date: null,
            end_date: null,
            reason: '',
        };
    },
    methods: {
        create() {
            this.start_date = null;
            this.end_date = null;
            this.reason = '';
            this.$validator.reset();
            this.dialog = true;
        },
        store() {
            const self = this;
            let data = {
                start_date: self.start_date,
                end_date: self.end_date,
                reason: self.reason,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/leaves', data)
                        .then(function(response) {
                            self.$validator.reset();
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateLeaveTable');
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
