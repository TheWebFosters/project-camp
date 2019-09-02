<template>
    <v-layout row justify-center>
        <!-- currency Add -->
        <CurrencyAdd ref="currencyAdd"></CurrencyAdd>
        <v-dialog v-model="dialog" full-width>
            <v-card>
                <v-card-title>
                    <v-icon medium>person</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_cutomer') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false"> <v-icon>clear</v-icon> </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-form ref="customerFormEdit">
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.company"
                                    :label="trans('messages.company')"
                                    v-validate="'required'"
                                    data-vv-name="company"
                                    :data-vv-as="trans('messages.company')"
                                    :error-messages="errors.collect('company')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.tax_number"
                                    :label="trans('messages.tax_number')"
                                    data-vv-name="tax_number"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.mobile"
                                    :label="trans('messages.mobile')"
                                    v-validate="'required'"
                                    data-vv-name="mobile"
                                    :data-vv-as="trans('messages.mobile')"
                                    :error-messages="errors.collect('mobile')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md2>
                                <v-autocomplete
                                    item-text="currency"
                                    item-value="id"
                                    :items="currencies"
                                    v-model="customerData.currency_id"
                                    :label="trans('messages.currency')"
                                    v-validate="'required'"
                                    data-vv-name="currency"
                                    :data-vv-as="trans('messages.currency')"
                                    :error-messages="errors.collect('currency')"
                                    required
                                ></v-autocomplete>
                            </v-flex>

                            <v-flex md1>
                                <v-btn @click="addCurrency" small color="primary" fab dark>
                                    <v-icon>add</v-icon>
                                </v-btn>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-text-field
                                    v-model="customerData.alternate_contact_no"
                                    :label="trans('messages.alternate_num')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md4>
                                <v-text-field
                                    v-model="customerData.email"
                                    :label="trans('messages.email')"
                                    v-validate="'required|email'"
                                    data-vv-name="email"
                                    :data-vv-as="trans('messages.email')"
                                    :error-messages="errors.collect('email')"
                                    required
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-text-field
                                    v-model="customerData.website"
                                    v-validate="{ url: { require_protocol: true } }"
                                    data-vv-name="website"
                                    :data-vv-as="trans('messages.website')"
                                    :error-messages="errors.collect('website')"
                                    :label="trans('messages.website')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.city"
                                    :label="trans('messages.city')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.state"
                                    :label="trans('messages.state')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.country"
                                    :label="trans('messages.country')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md3>
                                <v-text-field
                                    v-model="customerData.zip_code"
                                    :label="trans('messages.zip_code')"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 md6>
                                <v-textarea
                                    v-model="customerData.billing_address"
                                    auto-grow
                                    :label="trans('messages.billing_address')"
                                    rows="3"
                                ></v-textarea>
                            </v-flex>

                            <v-flex xs12 md6>
                                <v-textarea
                                    v-model="customerData.shipping_address"
                                    auto-grow
                                    :label="trans('messages.shipping_address')"
                                    rows="3"
                                ></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-form>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn
                        color="success"
                        @click="update(customerData.id)"
                        :loading="loading"
                        :disabled="loading"
                    >
                        {{ trans('messages.update') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import CurrencyAdd from '../components/currency/Add';
export default {
    components: {
        CurrencyAdd,
    },
    data: () => ({
        dialog: false,
        customerData: [],
        currencies: [],
        loading: false,
    }),
    created() {
        this.$eventBus.$on('updateCurrenciesList', data => {
            this.getCurrenciesListFromApi();
            this.customerData.currency_id = data.id;
        });
    },
    beforeDestroy() {
        this.$eventBus.$off('updateCurrenciesList');
    },
    methods: {
        edit(data) {
            const self = this;
            self.$validator.reset();
            axios
                .get('/admin/customers/' + data + '/edit')
                .then(function(response) {
                    self.customerData = response.data;
                    self.getCurrenciesListFromApi();
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
                        .put('/admin/customers/' + id, self.customerData)
                        .then(function(response) {
                            self.loading = false;
                            self.$validator.reset();
                            self.customerData = [];
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateCustomerTable');
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        addCurrency() {
            this.$refs.currencyAdd.create();
        },
        getCurrenciesListFromApi() {
            const self = this;
            axios
                .get('/admin/currencies')
                .then(function(response) {
                    self.currencies = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
