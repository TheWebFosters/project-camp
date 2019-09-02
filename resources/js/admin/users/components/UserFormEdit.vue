<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon medium>person</v-icon>
                <span class="headline">
                    {{ trans('messages.edit_employee') }}
                </span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12 sm6>
                            <v-text-field
                                :label="trans('messages.name')"
                                v-model="name"
                                v-validate="'required'"
                                data-vv-name="name"
                                :data-vv-as="trans('messages.name')"
                                :error-messages="errors.collect('name')"
                                required
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6>
                            <v-text-field
                                :label="trans('messages.email')"
                                v-model="email"
                                v-validate="'required|email'"
                                data-vv-name="email"
                                :data-vv-as="trans('messages.email')"
                                :error-messages="errors.collect('email')"
                                required
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                :label="trans('messages.password')"
                                :messages="trans('messages.password_edit_help')"
                                type="password"
                                v-validate="'min:6'"
                                v-model="password"
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
                            <v-divider class="mb-2 mt-1"></v-divider>
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
                            <v-divider class="mb-2 mt-1"></v-divider>
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
                        <!-- bank details -->
                        <v-flex xs12 sm12 md12>
                            <v-icon small>
                                account_balance_wallet
                            </v-icon>
                            <span class="subheading">
                                {{ trans('messages.bank_details') }}
                            </span>
                            <v-divider class="mb-2 mt-1"></v-divider>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                v-model="form_fields.account_holder_name"
                                :label="trans('messages.account_holder_name')"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                type="number"
                                v-model="form_fields.account_no"
                                :label="trans('messages.account_no')"
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                v-model="form_fields.bank_name"
                                :label="trans('messages.bank_name')"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                v-model="form_fields.bank_identifier_code"
                                :label="trans('messages.bank_identifier_code')"
                            >
                                <Popover
                                    slot="append"
                                    :helptext="trans('messages.tooltip_bank_identifier_code')"
                                >
                                </Popover>
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                v-model="form_fields.branch_location"
                                :label="trans('messages.branch_location')"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field
                                v-model="form_fields.tax_payer_id"
                                :label="trans('messages.tax_payer_id')"
                            >
                                <Popover
                                    slot="append"
                                    :helptext="trans('messages.tooltip_tax_payer_id')"
                                >
                                </Popover>
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12 md12>
                            <v-textarea
                                rows="3"
                                v-model="form_fields.note"
                                :label="trans('messages.note')"
                            >
                            </v-textarea>
                        </v-flex>
                        <v-flex xs12 sm4>
                            <v-autocomplete
                                item-text="name"
                                item-value="id"
                                :items="roles"
                                v-model="form_fields.role_id"
                                :label="trans('messages.role')"
                                v-validate="'required'"
                                data-vv-name="role"
                                :data-vv-as="trans('messages.role')"
                                :error-messages="errors.collect('role')"
                                required
                            ></v-autocomplete>
                        </v-flex>
                        <v-flex xs12 sm4>
                            <v-switch
                                :label="trans('messages.pre_Active_acount')"
                                v-model="active"
                            ></v-switch>
                        </v-flex>
                        <v-flex xs12 sm4>
                            <v-checkbox
                                :label="trans('messages.send_email')"
                                value="true"
                                v-model="send_email"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="save()" color="primary" dark>
                    {{ trans('messages.update') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import Popover from '../../popover/Popover';
export default {
    components: {
        Popover,
    },
    props: {
        propUserId: {
            required: true,
        },
    },
    data() {
        const self = this;

        return {
            valid: false,
            name: '',
            form_fields: [],
            birth_date: null,
            gender_types: [],
            email: '',
            password: '',
            active: '',
            roles: [],
            send_email: false,
        };
    },
    mounted() {
        const self = this;

        this.loadUser(() => {});
    },
    methods: {
        save() {
            const self = this;

            let payload = {
                name: self.name,
                mobile: self.form_fields.mobile,
                alternate_num: self.form_fields.alternate_num,
                home_address: self.form_fields.home_address,
                current_address: self.form_fields.current_address,
                skype: self.form_fields.skype,
                linkedin: self.form_fields.linkedin,
                facebook: self.form_fields.facebook,
                twitter: self.form_fields.twitter,
                birth_date: self.birth_date,
                guardian_name: self.form_fields.guardian_name,
                gender: self.form_fields.gender,
                note: self.form_fields.note,
                email: self.email,
                password: self.password ? self.password : null,
                active: self.active ? moment().format('YYYY-MM-DD') : null,
                role: self.form_fields.role_id,
                send_email: self.send_email,
                account_holder_name: self.form_fields.account_holder_name,
                account_no: self.form_fields.account_no,
                bank_name: self.form_fields.bank_name,
                bank_identifier_code: self.form_fields.bank_identifier_code,
                branch_location: self.form_fields.branch_location,
                tax_payer_id: self.form_fields.tax_payer_id,
            };

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.$store.commit('showLoader');
                    axios
                        .put('/admin/users/' + self.propUserId, payload)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            self.$store.commit('hideLoader');

                            if (response.data.success === true) {
                                self.goBack();
                            }
                        })
                        .catch(function(error) {
                            self.$store.commit('hideLoader');

                            if (error.response) {
                                self.$store.commit('showSnackbar', {
                                    message: error.response.data.message,
                                    color: 'error',
                                    duration: 3000,
                                });
                            } else if (error.request) {
                                console.log(error.request);
                            } else {
                                console.log('Error', error.message);
                            }
                        });
                }
            });
        },
        loadUser(cb) {
            const self = this;

            axios.get('/admin/users/' + self.propUserId + '/edit').then(function(response) {
                let User = response.data.user;
                self.form_fields = User;
                self.gender_types = response.data.gender_types;
                self.birth_date = User.birth_date;
                self.name = User.name;
                self.email = User.email;
                self.active = User.active !== null;
                self.roles = response.data.roles;
                self.form_fields.role_id = response.data.role_id;
            });
        },
    },
};
</script>
