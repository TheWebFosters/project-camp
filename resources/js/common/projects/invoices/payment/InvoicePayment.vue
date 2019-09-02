<template>
    <div class="component-wrap">
        <v-layout row justify-center>
            <v-dialog v-model="dialog" max-width="700">
                <v-card>
                    <v-card-title>
                        <v-icon medium>money</v-icon>
                        <span class="headline">
                            {{ trans('messages.payment') }}
                        </span>
                        <v-spacer></v-spacer>
                        <v-btn @click="dialog = false" flat small icon>
                            <v-icon>clear</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout wrap>
                                <v-flex xs12 sm6 md6>
                                    <v-text-field
                                        type="number"
                                        v-model="transaction_total"
                                        :label="trans('messages.amount')"
                                        v-validate="'max_value:' + max_val"
                                        data-vv-name="amount"
                                        :data-vv-as="trans('messages.amount')"
                                        :error-messages="errors.collect('amount')"
                                        required
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6 v-if="show_conversion_rate == 'true'">
                                    <v-text-field
                                        type="number"
                                        v-model="conversion_rate"
                                        :label="trans('messages.conversion_rate')"
                                        v-validate="'required'"
                                        data-vv-name="conversion_rate"
                                        :data-vv-as="trans('messages.conversion_rate')"
                                        :error-messages="errors.collect('conversion_rate')"
                                        required
                                        @change="calculateFinalAmount"
                                    >
                                        <Popover
                                            slot="append"
                                            :helptext="trans('messages.tooltip_conversion_rate')"
                                        >
                                        </Popover>
                                    </v-text-field>
                                </v-flex>
                                <v-flex xs12 sm6 md6 v-if="show_conversion_rate == 'true'">
                                    <v-text-field
                                        type="number"
                                        v-model="final_amount"
                                        :label="trans('messages.final_amount')"
                                        v-validate="'required'"
                                        data-vv-name="final_amount"
                                        :data-vv-as="trans('messages.final_amount')"
                                        :error-messages="errors.collect('final_amount')"
                                        required
                                        @change="calculateConversionRate"
                                    >
                                        <Popover
                                            slot="append"
                                            :helptext="trans('messages.tooltip_final_amount')"
                                        >
                                        </Popover>
                                    </v-text-field>
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
                                                        {{ trans('messages.paid_on') }}
                                                    </label>
                                                    <flat-pickr
                                                        v-model="paid_on"
                                                        v-validate="'required'"
                                                        name="paid_on"
                                                        required
                                                        :config="flatPickerDate()"
                                                        :data-vv-as="trans('messages.paid_on')"
                                                    ></flat-pickr>
                                                </div>
                                            </div>
                                            <div class="v-messages theme--light error--text">
                                                {{ errors.first('paid_on') }}
                                            </div>
                                        </div>
                                    </div>
                                </v-flex>
                            </v-layout>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <v-textarea
                                        rows="3"
                                        v-model="payment_details"
                                        :label="trans('messages.payment_details')"
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
                            {{ trans('messages.pay') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>
<script>
import Popover from '../../../../admin/popover/Popover';
export default {
    components: {
        Popover,
    },
    data() {
        return {
            dialog: false,
            paid_on: null,
            transaction_id: null,
            transaction_total: null,
            payment_details: null,
            conversion_rate: null,
            show_conversion_rate: null,
            final_amount: null,
            max_val: null,
            loading: false,
        };
    },
    methods: {
        create(invoice_params) {
            const self = this;
            self.$validator.reset();
            self.payment_details = null;
            self.transaction_total = null;
            self.paid_on = null;
            self.conversion_rate = null;
            self.final_amount = null;
            self.transaction_id = invoice_params.transaction_id;
            axios
                .get('/transaction-payments/create', {
                    params: {
                        transaction_id: invoice_params.transaction_id,
                        type: invoice_params.type,
                    },
                })
                .then(function(response) {
                    self.transaction_total = response.data.transaction_total;
                    self.max_val = response.data.transaction_total;
                    self.show_conversion_rate = response.data.conversion_rate;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        calculateFinalAmount() {
            const self = this;
            var final_amount = _.multiply(self.transaction_total, self.conversion_rate);
            self.final_amount = _.floor(final_amount, 2);
        },
        calculateConversionRate() {
            const self = this;
            var rate = _.divide(self.final_amount, self.transaction_total);
            self.conversion_rate = _.floor(rate);
        },
        store() {
            const self = this;
            let data = {
                transaction_id: self.transaction_id,
                amount: self.transaction_total,
                paid_on: self.paid_on,
                payment_details: self.payment_details,
                conversion_rate: self.conversion_rate,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/transaction-payments', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updatePaymentTransaction');
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
