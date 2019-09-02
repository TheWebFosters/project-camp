<template>
    <div>
        <v-layout row justify-center>
            <!-- currency Add -->
            <CurrencyAdd ref="currencyAdd"></CurrencyAdd>
            <StatusAdd ref="createStatus"></StatusAdd>
            <SourceAdd ref="createSource"></SourceAdd>
            <v-dialog v-model="dialog" full-width>
                <v-card>
                    <v-card-title>
                        <div>
                            <v-icon medium>verified_user</v-icon>
                            <span class="headline">
                                {{ trans('messages.edit_lead') }}
                            </span>
                        </div>
                        <v-spacer></v-spacer>
                        <v-btn flat icon @click="dialog = false"> <v-icon>clear</v-icon> </v-btn>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-form ref="LeadFormAdd">
                        <v-container grid-list-md>
                            <v-flex xs12 sm12 md12>
                                <v-card class="elevation-3 mb-3">
                                    <v-card-title>
                                        <v-icon>
                                            verified_user
                                        </v-icon>
                                        <span class="subheading">
                                            {{ trans('messages.lead_info') }}
                                        </span>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-layout row wrap>
                                            <v-flex xs12 md3>
                                                <v-autocomplete
                                                    item-text="name"
                                                    item-value="id"
                                                    :items="statuses"
                                                    v-model="form_fields.status_id"
                                                    :label="trans('messages.status')"
                                                    v-validate="'required'"
                                                    data-vv-name="status"
                                                    :data-vv-as="trans('messages.status')"
                                                    :error-messages="errors.collect('status')"
                                                    required
                                                ></v-autocomplete>
                                            </v-flex>

                                            <v-flex md1>
                                                <v-btn
                                                    @click="addStatus"
                                                    small
                                                    color="primary"
                                                    fab
                                                    dark
                                                >
                                                    <v-icon>add</v-icon>
                                                </v-btn>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-autocomplete
                                                    item-text="name"
                                                    item-value="id"
                                                    :items="sources"
                                                    v-model="form_fields.source_id"
                                                    :label="trans('messages.source')"
                                                ></v-autocomplete>
                                            </v-flex>

                                            <v-flex md1>
                                                <v-btn
                                                    @click="addSource"
                                                    small
                                                    color="primary"
                                                    fab
                                                    dark
                                                >
                                                    <v-icon>add</v-icon>
                                                </v-btn>
                                            </v-flex>

                                            <v-flex xs12 md4>
                                                <v-autocomplete
                                                    item-text="name"
                                                    item-value="id"
                                                    :items="employees"
                                                    v-model="form_fields.assigned_to"
                                                    :label="trans('messages.assigned_to')"
                                                ></v-autocomplete>
                                            </v-flex>
                                        </v-layout>
                                    </v-card-text>
                                </v-card>
                            </v-flex>

                            <v-flex xs12 sm12 md12>
                                <v-card class="elevation-3 mb-3">
                                    <v-card-title>
                                        <v-icon>
                                            business_center
                                        </v-icon>
                                        <span class="subheading">
                                            {{ trans('messages.company_info') }}
                                        </span>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-layout row wrap mt-2>
                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.company"
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
                                                    v-model="form_fields.tax_number"
                                                    :label="trans('messages.tax_number')"
                                                    data-vv-name="tax_number"
                                                    required
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.mobile"
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
                                                    v-model="form_fields.currency_id"
                                                    :label="trans('messages.currency')"
                                                    v-validate="'required'"
                                                    data-vv-name="currency"
                                                    :data-vv-as="trans('messages.currency')"
                                                    :error-messages="errors.collect('currency')"
                                                    required
                                                ></v-autocomplete>
                                            </v-flex>

                                            <v-flex md1>
                                                <v-btn
                                                    @click="addCurrency"
                                                    small
                                                    color="primary"
                                                    fab
                                                    dark
                                                >
                                                    <v-icon>add</v-icon>
                                                </v-btn>
                                            </v-flex>

                                            <v-flex xs12 md4>
                                                <v-text-field
                                                    v-model="form_fields.alternate_contact_no"
                                                    :label="trans('messages.alternate_num')"
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 sm6 md4>
                                                <v-text-field
                                                    v-model="form_fields.email"
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
                                                    v-model="form_fields.website"
                                                    v-validate="{ url: { require_protocol: true } }"
                                                    data-vv-name="website"
                                                    :data-vv-as="trans('messages.website')"
                                                    :error-messages="errors.collect('website')"
                                                    :label="trans('messages.website')"
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.city"
                                                    :label="trans('messages.city')"
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.state"
                                                    :label="trans('messages.state')"
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.country"
                                                    :label="trans('messages.country')"
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 md3>
                                                <v-text-field
                                                    v-model="form_fields.zip_code"
                                                    :label="trans('messages.zip_code')"
                                                ></v-text-field>
                                            </v-flex>
                                        </v-layout>
                                        <v-layout row-wrap>
                                            <v-flex xs12 md6>
                                                <v-textarea
                                                    v-model="form_fields.billing_address"
                                                    auto-grow
                                                    :label="trans('messages.billing_address')"
                                                    rows="3"
                                                ></v-textarea>
                                            </v-flex>

                                            <v-flex xs12 md6>
                                                <v-textarea
                                                    v-model="form_fields.shipping_address"
                                                    auto-grow
                                                    :label="trans('messages.shipping_address')"
                                                    rows="3"
                                                ></v-textarea>
                                            </v-flex>
                                        </v-layout>
                                        <v-layout row>
                                            <v-flex xs12 md12>
                                                <v-textarea
                                                    v-model="form_fields.description"
                                                    auto-grow
                                                    :label="trans('messages.description')"
                                                    rows="3"
                                                ></v-textarea>
                                            </v-flex>
                                        </v-layout>
                                        <v-layout row>
                                            <v-flex xs12 sm4 md4>
                                                <div class="v-input v-text-field theme--light">
                                                    <div class="v-input__control">
                                                        <div class="v-input__slot">
                                                            <div class="v-text-field__slot">
                                                                <label
                                                                    aria-hidden="true"
                                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                                >
                                                                    {{
                                                                        trans(
                                                                            'messages.contacted_date'
                                                                        )
                                                                    }}
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
                                            <v-flex xs12 sm4>
                                                <v-checkbox
                                                    :label="trans('messages.send_email')"
                                                    value="true"
                                                    v-model="form_fields.send_email"
                                                >
                                                </v-checkbox>
                                            </v-flex>
                                        </v-layout>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-container>
                    </v-form>
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
                            {{ trans('messages.update') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>
<script>
import CurrencyAdd from '../customers/components/currency/Add';
import StatusAdd from '../customers/components/status/Add';
import SourceAdd from '../customers/components/source/Add';
export default {
    components: {
        CurrencyAdd,
        StatusAdd,
        SourceAdd,
    },
    data() {
        const self = this;
        return {
            form_fields: [],
            dialog: false,
            currencies: [],
            send_email: false,
            loading: false,
            statuses: [],
            sources: [],
            employees: [],
            contacted_date: null,
            url: null,
            leads_id: null,
        };
    },
    created() {
        this.$eventBus.$on('updateCurrenciesList', data => {
            this.getCurrenciesListFromApi();
            this.form_fields.currency_id = data.id;
        });
        this.$eventBus.$on('updateStatusList', data => {
            this.getStatuesListFromApi();
            this.form_fields.status_id = data.id;
        });
        this.$eventBus.$on('updateSourceList', data => {
            this.getSourceListFromApi();
            this.form_fields.source_id = data.id;
        });
    },
    beforeDestroy() {
        this.$eventBus.$off('updateCurrenciesList');
        this.$eventBus.$off('updateStatusList');
        this.$eventBus.$off('updateSourceList');
    },
    methods: {
        create(data) {
            const self = this;
            self.form_fields = [];
            self.contacted_date = null;
            self.leads_id = data.id;
            self.$validator.reset();
            self.getLeadFromApi(data.id);
            self.getCurrenciesListFromApi();
            self.getStatuesListFromApi();
            self.getSourceListFromApi();
            self.getEmployee();
            self.dialog = true;
        },
        getLeadFromApi(id) {
            const self = this;
            axios
                .get('/admin/leads/' + id + '/edit')
                .then(function(response) {
                    self.form_fields = response.data;
                    self.contacted_date = response.data.contacted_date;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        store() {
            const self = this;

            let data = _.pick(self.form_fields, [
                'company',
                'tax_number',
                'mobile',
                'currency_id',
                'alternate_contact_no',
                'email',
                'website',
                'city',
                'state',
                'country',
                'zip_code',
                'billing_address',
                'shipping_address',
                'send_email',
                'status_id',
                'source_id',
                'assigned_to',
                'description',
            ]);

            data.contacted_date = self.contacted_date;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/admin/leads/' + self.leads_id, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateLeadsTable');
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
        addStatus() {
            this.$refs.createStatus.create();
        },
        getStatuesListFromApi() {
            const self = this;
            axios
                .get('/admin/status')
                .then(function(response) {
                    self.statuses = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        addSource() {
            this.$refs.createSource.create();
        },
        getSourceListFromApi() {
            const self = this;
            axios
                .get('/admin/source')
                .then(function(response) {
                    self.sources = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
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
    },
};
</script>
