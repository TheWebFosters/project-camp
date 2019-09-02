<template>
    <div class="component-wrap">
        <v-card class="elevation-3">
            <v-card-title>
                <v-icon>receipt</v-icon>
                <span class="headline"> {{ trans('messages.edit_invoice') }} </span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 md4>
                            <v-text-field
                                v-model="invoice.title"
                                :label="trans('messages.title')"
                                v-validate="'required'"
                                data-vv-name="title"
                                :data-vv-as="trans('messages.title')"
                                :error-messages="errors.collect('title')"
                                required
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 md4>
                            <v-autocomplete
                                item-text="name"
                                item-value="id"
                                :items="projectList"
                                v-model="projectId"
                                :label="trans('messages.project')"
                                v-validate="'required'"
                                data-vv-name="project"
                                :data-vv-as="trans('messages.project')"
                                :error-messages="errors.collect('project')"
                                required
                                @change="getCustomerId"
                            ></v-autocomplete>
                        </v-flex>
                        <v-flex xs12 md4>
                            <v-autocomplete
                                item-text="company"
                                item-value="id"
                                :items="customers"
                                v-model="invoice.customer_id"
                                :label="trans('messages.customer')"
                                v-validate="'required'"
                                data-vv-name="customer"
                                :data-vv-as="trans('messages.customer')"
                                :error-messages="errors.collect('customer')"
                                required
                                @change="getContact"
                            ></v-autocomplete>
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
                                                {{ trans('messages.date') }}
                                            </label>
                                            <flat-pickr
                                                v-model="transaction_date"
                                                v-validate="'required'"
                                                name="date"
                                                required
                                                :config="flatPickerDate()"
                                                :data-vv-as="trans('messages.date')"
                                            ></flat-pickr>
                                        </div>
                                    </div>
                                    <div class="v-messages theme--light error--text">
                                        {{ errors.first('date') }}
                                    </div>
                                </div>
                            </div>
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
                                                {{ trans('messages.due_date') }}
                                            </label>
                                            <flat-pickr
                                                v-model="due_date"
                                                name="due_date"
                                                :config="flatPickerDate()"
                                            ></flat-pickr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </v-flex>
                        <v-flex xs12 md4>
                            <v-autocomplete
                                :items="invoice_types"
                                v-model="invoice.status"
                                :label="trans('messages.type')"
                                v-validate="'required'"
                                data-vv-name="type"
                                :data-vv-as="trans('messages.type')"
                                :error-messages="errors.collect('type')"
                                required
                            ></v-autocomplete>
                        </v-flex>
                        <v-flex xs12 md4>
                            <v-autocomplete
                                item-text="name"
                                item-value="id"
                                :items="invoice_schemes"
                                v-model="invoice.invoice_scheme_id"
                                :label="trans('messages.invoice_scheme')"
                                v-validate="'required'"
                                data-vv-name="invoice_scheme"
                                :data-vv-as="trans('messages.invoice_scheme')"
                                :error-messages="errors.collect('invoice_scheme')"
                                required
                            >
                                <Popover
                                    slot="append"
                                    :helptext="trans('messages.invoice_scheme_tooltip')"
                                >
                                </Popover>
                            </v-autocomplete>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
        </v-card>
        <br />
        <v-card class="elevation-3">
            <v-card-text>
                <v-flex xs12 md12>
                    <v-container grid-list-md text-xs-center>
                        <v-layout row wrap>
                            <v-flex md3>
                                <h4>{{ trans('messages.task') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.rate') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.quantity') }}</h4>
                            </v-flex>
                            <v-flex md1>
                                <h4>{{ trans('messages.unit') }}</h4>
                            </v-flex>
                            <v-flex md2>
                                <h4>{{ trans('messages.tax') }} (%)</h4>
                            </v-flex>
                            <v-flex md1>
                                <h4>{{ trans('messages.total') }}</h4>
                            </v-flex>
                            <v-flex md1>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-btn small flat @click="addRow">
                                            {{ trans('messages.add_a_row') }}
                                            <v-icon small>add_circle_outline</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ trans('messages.add_a_row') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <template v-for="(row, index) in rows">
                                <v-flex md3>
                                    <v-text-field
                                        v-model="row.short_description"
                                        :label="trans('messages.task')"
                                        v-validate="'required'"
                                        data-vv-name="task"
                                        :data-vv-as="trans('messages.task')"
                                        :error-messages="errors.collect('task')"
                                        required
                                    >
                                        <v-tooltip slot="append" top>
                                            <template>
                                                <template>
                                                    <v-icon
                                                        small
                                                        slot="activator"
                                                        @click="
                                                            row.display_long_description = !row.display_long_description
                                                        "
                                                    >
                                                        info
                                                    </v-icon>
                                                </template>
                                            </template>
                                            <span>{{ trans('messages.toggle_description') }}</span>
                                        </v-tooltip>
                                    </v-text-field>
                                </v-flex>
                                <v-flex md2>
                                    <v-text-field
                                        @change="updateRowTotal(index)"
                                        v-model="row.rate"
                                        type="number"
                                        :label="trans('messages.rate')"
                                        v-validate="'required'"
                                        data-vv-name="rate"
                                        :data-vv-as="trans('messages.rate')"
                                        :error-messages="errors.collect('rate')"
                                        required
                                    ></v-text-field>
                                </v-flex>
                                <v-flex md2>
                                    <v-text-field
                                        @change="updateRowTotal(index)"
                                        v-model="row.quantity"
                                        type="number"
                                        :label="trans('messages.quantity')"
                                        v-validate="'required'"
                                        data-vv-name="quantity"
                                        :data-vv-as="trans('messages.quantity')"
                                        :error-messages="errors.collect('quantity')"
                                        required
                                    ></v-text-field>
                                </v-flex>
                                <v-flex md1>
                                    <v-text-field
                                        v-model="row.unit"
                                        :label="trans('messages.unit')"
                                        v-validate="'required'"
                                        data-vv-name="unit"
                                        :data-vv-as="trans('messages.unit')"
                                        :error-messages="errors.collect('unit')"
                                        required
                                    >
                                    </v-text-field>
                                </v-flex>
                                <v-flex md2>
                                    <v-text-field
                                        @change="calculateTotaltax(index)"
                                        v-model="row.tax"
                                        type="number"
                                        :label="trans('messages.tax')"
                                        v-validate="'required'"
                                        data-vv-name="tax"
                                        :data-vv-as="trans('messages.tax')"
                                        :error-messages="errors.collect('tax')"
                                        required
                                    ></v-text-field>
                                </v-flex>
                                <v-flex md1>
                                    <v-text-field
                                        readonly
                                        v-model="row.total"
                                        :label="trans('messages.total')"
                                        v-validate="'required'"
                                        data-vv-name="total"
                                        :data-vv-as="trans('messages.total')"
                                        :error-messages="errors.collect('total')"
                                        required
                                    ></v-text-field>
                                </v-flex>
                                <v-flex md1 v-if="index != 0">
                                    <v-tooltip top>
                                        <template slot="activator">
                                            <v-btn small flat icon @click="removeRow(index)">
                                                <v-icon small>clear</v-icon>
                                            </v-btn>
                                        </template>

                                        <span>{{ trans('messages.remove_a_row') }}</span>
                                    </v-tooltip>
                                </v-flex>
                                <v-flex md11 v-show="row.display_long_description">
                                    <v-textarea
                                        rows="1"
                                        v-model="row.long_description"
                                        :label="trans('messages.description')"
                                    ></v-textarea>
                                </v-flex>
                                <v-flex md1 v-show="row.display_long_description"></v-flex>
                            </template>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-card-text>
        </v-card>
        <br />
        <v-card class="elevation-3">
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12 md4>
                            <strong>{{ trans('messages.sub_total') }} :</strong>
                            <span>{{ sub_total }}</span>
                        </v-flex>
                        <v-flex xs12 md4>
                            <strong>{{ trans('messages.tax') }} :</strong>
                            <span>{{ total_tax }}</span>
                        </v-flex>
                        <v-flex xs12 md4>
                            <strong>{{ trans('messages.total') }} :</strong>
                            <span>{{ invoice_total }}</span>
                            <input type="hidden" name="invoice_total" v-model="invoice_total" />
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 md6>
                            <v-select
                                item-text="value"
                                item-value="key"
                                :items="discountType"
                                v-model="invoice.discount_type"
                                :label="trans('messages.discount_type')"
                            ></v-select>
                        </v-flex>
                        <v-flex xs12 md6>
                            <v-text-field
                                @change="updateInvoiceTotal"
                                type="number"
                                v-model="invoice.discount_amount"
                                :label="trans('messages.discount_amount')"
                            >
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 md12>
                            <v-textarea
                                rows="3"
                                v-model="invoice.terms"
                                :label="trans('messages.terms')"
                            >
                                <Popover
                                    slot="append"
                                    :helptext="trans('messages.will_be_displayed_in_invoice')"
                                >
                                </Popover>
                            </v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 md12>
                            <v-textarea
                                rows="3"
                                v-model="invoice.notes"
                                :label="trans('messages.notes')"
                            ></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row-wrap>
                        <v-flex xs12 md4>
                            <v-checkbox
                                v-model="mailToCustomer"
                                @change="getContact"
                                :label="trans('messages.send_email_to_customer')"
                            ></v-checkbox>
                        </v-flex>
                        <v-flex xs12 md4 v-show="displayContact">
                            <v-select
                                :items="contacts"
                                item-text="name"
                                item-value="id"
                                v-model="contact_id"
                                :label="trans('messages.contact')"
                            ></v-select>
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
    </div>
</template>
<script>
import Popover from '../../../admin/popover/Popover';
export default {
    components: {
        Popover,
    },
    data() {
        return {
            invoiceId: '',
            projectId: '',
            invoice: [],
            customers: [],
            project: {},
            transaction_date: '',
            due_date: '',
            discountType: [],
            rows: [],
            total_tax: 0,
            invoice_total: 0,
            sub_total: 0,
            contact_id: null,
            mailToCustomer: false,
            contacts: [],
            displayContact: true,
            projectList: [],
            customer_id: null,
            loading: false,
            invoice_types: [],
            invoice_schemes: [],
        };
    },
    created() {
        const self = this;
        self.invoiceId = self.$route.params.id;
        self.projectId = Number(self.$route.params.project_id);
        self.getProjects();
        self.getInvoiceDataFromApi(self.projectId, self.invoiceId);
    },
    methods: {
        getInvoiceDataFromApi(projectId, invoiceId) {
            const self = this;
            axios
                .get('/invoices/' + invoiceId + '/edit', {
                    params: { project_id: projectId },
                })
                .then(function(response) {
                    self.invoice = response.data.invoice;
                    self.invoice_schemes = response.data.invoice_schemes;
                    self.customers = response.data.customers;
                    self.rows = response.data.invoice_line;
                    self.discountType = response.data.discount_type;
                    self.transaction_date = response.data.invoice.transaction_date;
                    self.due_date = response.data.invoice.due_date;
                    self.invoice_types = response.data.invoice_type;
                    self.calculateTaxAndSubtotal();
                    self.getContact();
                    self.contact_id = response.data.invoice.contact_id;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        calculateTaxAndSubtotal() {
            const self = this;
            let rows = self.rows;
            var total = 0.0;
            var tax = 0.0;
            var total_tax = 0.0;
            var row_total_inc_tax = 0.0;

            if (self.sub_total == 0) {
                _.forEach(rows, function(row) {
                    total = _.add(parseFloat(row.total), total);
                });
                self.sub_total = _.floor(total, 2);
            }
            if (self.total_tax == 0) {
                _.forEach(rows, function(row) {
                    tax = _.divide(parseFloat(row.tax), 100);
                    row_total_inc_tax = _.multiply(tax, parseFloat(row.total));
                    total_tax = _.add(row_total_inc_tax, total_tax);
                });

                self.total_tax = _.floor(total_tax, 2);
            }
            if (self.invoice_total == 0) {
                self.invoice_total = parseFloat(self.invoice.total);
            }
        },
        addRow() {
            const self = this;
            let row = {
                short_description: '',
                display_long_description: false,
                rate: 0,
                quantity: 0,
                unit: '',
                tax: 0,
                total: 0,
            };
            self.rows.unshift(row);
        },
        removeRow(index) {
            const self = this;
            self.rows.splice(index, 1);
            self.updateSubTotal();
            self.calculateTotaltax();
            self.updateInvoiceTotal();
        },
        updateRowTotal(index) {
            const self = this;
            let rows = self.rows;

            let qty = parseFloat(self.rows[index].quantity);
            let rate = parseFloat(self.rows[index].rate);

            let total = _.multiply(qty, rate);

            self.rows[index].total = _.floor(total, 2);

            self.updateSubTotal();
        },
        updateSubTotal() {
            const self = this;
            let rows = self.rows;
            var total = 0.0;
            _.forEach(rows, function(row) {
                total = _.add(parseFloat(row.total), total);
            });

            self.sub_total = _.floor(total, 2);
            self.calculateTotaltax();
        },
        calculateTotaltax() {
            const self = this;
            let rows = self.rows;
            var tax = 0.0;
            var total_tax = 0.0;
            var row_total_inc_tax = 0.0;
            _.forEach(rows, function(row) {
                tax = _.divide(parseFloat(row.tax), 100);
                row_total_inc_tax = _.multiply(tax, parseFloat(row.total));
                total_tax = _.add(row_total_inc_tax, total_tax);
            });

            self.total_tax = _.floor(total_tax, 2);
            self.updateInvoiceTotal();
        },
        updateInvoiceTotal() {
            const self = this;
            var discount_type = self.invoice.discount_type;
            var discount_amount = parseFloat(self.invoice.discount_amount);
            var total_tax = self.total_tax;
            var sub_total = self.sub_total;

            self.invoice_total = sub_total + total_tax;

            //Apply discount
            if (discount_type == 'fixed') {
                self.invoice_total = self.invoice_total - discount_amount;
            }
            if (discount_type == 'percentage') {
                self.invoice_total =
                    self.invoice_total - (discount_amount / 100) * self.invoice_total;
            }

            self.invoice_total = _.floor(self.invoice_total, 2);
        },
        store() {
            const self = this;
            let data = {
                project_id: self.projectId,
                title: self.invoice.title,
                customer_id: self.invoice.customer_id,
                contact_id: self.contact_id,
                transaction_date: self.transaction_date,
                due_date: self.due_date,
                discount_type: self.invoice.discount_type,
                discount_amount: self.invoice.discount_amount,
                total: self.invoice_total,
                terms: self.invoice.terms,
                notes: self.invoice.notes,
                status: self.invoice.status,
                invoice_lines: self.rows,
                do_mail: self.mailToCustomer,
                invoice_scheme_id: self.invoice.invoice_scheme_id,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/invoices/' + self.invoiceId, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.goBack();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        getContact() {
            const self = this;
            var mailToCustomer = self.mailToCustomer;
            var customer_id = self.invoice.customer_id;
            if (mailToCustomer == true) {
                axios
                    .get('/admin/customers/' + customer_id + '/contacts')
                    .then(function(response) {
                        self.contacts = response.data;
                        self.displayContact = true;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            } else if (mailToCustomer == false) {
                self.contact_id = null;
                self.displayContact = false;
            }
        },
        getProjects() {
            const self = this;
            axios
                .get('projects/projects-list')
                .then(function(response) {
                    self.projectList = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getCustomerId() {
            const self = this;
            axios
                .get('projects/' + self.projectId + '/customer')
                .then(function(response) {
                    self.invoice.customer_id = response.data.customer_id;
                    self.getContact();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
