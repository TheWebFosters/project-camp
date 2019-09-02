<template>
    <div>
        <v-layout wrap>
            <!-- currency Add -->
            <CurrencyAdd ref="currencyAdd"></CurrencyAdd>
            <v-flex xs12 sm12 md12>
                <v-card>
                    <v-card-text>
                        <v-tabs color="cyan" dark slider-color="yellow" grow>
                            <v-tab ripple>
                                <v-icon>settings</v-icon>
                                {{ trans('messages.system_settings') }}
                            </v-tab>
                            <v-tab ripple>
                                <v-icon>settings</v-icon>
                                {{ trans('messages.business_settings') }}
                            </v-tab>
                            <v-tab ripple>
                                <v-icon>receipt</v-icon>
                                {{ trans('messages.email_templates') }}
                            </v-tab>
                            <v-tab ripple v-if="$can('superadmin')">
                                <v-icon>settings</v-icon>
                                Cron
                            </v-tab>
                            <!-- System Settings -->
                            <v-tab-item>
                                <v-card flat>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout row wrap>
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-model="form_fields.APP_TITLE"
                                                        :label="trans('messages.app_title')"
                                                        v-validate="'required'"
                                                        data-vv-name="app_title"
                                                        :data-vv-as="trans('messages.app_title')"
                                                        :error-messages="
                                                            errors.collect('app_title')
                                                        "
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md6>
                                                    <v-autocomplete
                                                        :items="languages"
                                                        v-model="form_fields.APP_LOCALE"
                                                        :label="trans('messages.app_default_lang')"
                                                        v-validate="'required'"
                                                        data-vv-name="app_default_lang"
                                                        :data-vv-as="
                                                            trans('messages.app_default_lang')
                                                        "
                                                        :error-messages="
                                                            errors.collect('app_default_lang')
                                                        "
                                                        required
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-autocomplete
                                                        :items="dateFormats"
                                                        v-model="form_fields.date_format"
                                                        :label="trans('messages.date_format')"
                                                        v-validate="'required'"
                                                        data-vv-name="date_format"
                                                        :data-vv-as="trans('messages.date_format')"
                                                        :error-messages="
                                                            errors.collect('date_format')
                                                        "
                                                        required
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-autocomplete
                                                        :items="timeFormats"
                                                        v-model="form_fields.time_format"
                                                        :label="trans('messages.time_format')"
                                                        v-validate="'required'"
                                                        data-vv-name="time_format"
                                                        :data-vv-as="trans('messages.time_format')"
                                                        :error-messages="
                                                            errors.collect('time_format')
                                                        "
                                                        required
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-autocomplete
                                                        :items="timezones"
                                                        v-model="form_fields.APP_TIMEZONE"
                                                        :label="trans('messages.timezone')"
                                                        v-validate="'required'"
                                                        data-vv-name="timezone"
                                                        :data-vv-as="trans('messages.timezone')"
                                                        :error-messages="errors.collect('timezone')"
                                                        required
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-autocomplete
                                                        :items="week_days"
                                                        v-model="form_fields.first_day_of_week"
                                                        :label="trans('messages.first_day_of_week')"
                                                        v-validate="'required'"
                                                        data-vv-name="first_day_of_week"
                                                        :data-vv-as="
                                                            trans('messages.first_day_of_week')
                                                        "
                                                        :error-messages="
                                                            errors.collect('first_day_of_week')
                                                        "
                                                        required
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-flex xs12 sm4>
                                                    <v-checkbox
                                                        :label="
                                                            trans('messages.enable_client_signup')
                                                        "
                                                        color="primary"
                                                        value="1"
                                                        v-model="form_fields.ENABLE_CLIENT_SIGNUP"
                                                    ></v-checkbox>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <!-- /System Settings -->
                            <!-- Business Settings -->
                            <v-tab-item>
                                <v-card flat>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout row wrap>
                                                <v-flex xs12 sm4>
                                                    <v-text-field
                                                        v-model="form_fields.APP_NAME"
                                                        :label="trans('messages.company')"
                                                        v-validate="'required'"
                                                        data-vv-name="company"
                                                        :data-vv-as="trans('messages.company')"
                                                        :error-messages="errors.collect('company')"
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm4>
                                                    <v-text-field
                                                        v-model="form_fields.tax_number"
                                                        :label="trans('messages.tax_number')"
                                                        data-vv-name="tax_number"
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
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
                                                        v-model="form_fields.address_line_1"
                                                        :label="trans('messages.address_line_1')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-text-field
                                                        v-model="form_fields.address_line_2"
                                                        :label="trans('messages.address_line_2')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-text-field
                                                        v-model="form_fields.city"
                                                        :label="trans('messages.city')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-text-field
                                                        v-model="form_fields.state"
                                                        :label="trans('messages.state')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-text-field
                                                        v-model="form_fields.country"
                                                        :label="trans('messages.country')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md4>
                                                    <v-text-field
                                                        v-model="form_fields.zip_code"
                                                        :label="trans('messages.zip_code')"
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 md3>
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
                                            </v-layout>
                                            <v-layout row wrap>
                                                <v-flex xs12 sm6 md6>
                                                    <strong>{{ trans('messages.logo') }}:</strong
                                                    ><br />
                                                    <div class="dropzone" id="uploadLogo"></div>
                                                </v-flex>
                                                <v-flex xs12 sm6 md6>
                                                    <v-img
                                                        v-if="logo"
                                                        :src="logo"
                                                        height="125px"
                                                        contain
                                                    ></v-img>
                                                </v-flex>
                                            </v-layout>
                                            <v-layout row wrap>
                                                <v-flex xs12 sm6 md6>
                                                    <strong>{{ trans('messages.favicon') }}:</strong
                                                    ><br />
                                                    <div class="dropzone" id="uploadFavicon"></div>
                                                </v-flex>
                                                <v-flex xs12 sm6 md6>
                                                    <v-img
                                                        v-if="favicon"
                                                        :src="favicon"
                                                        height="80px"
                                                        contain
                                                    ></v-img>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <!-- /Business Settings -->
                            <!-- Email Template Settings -->
                            <v-tab-item>
                                <v-card class="mb-3">
                                    <v-card-title>
                                        <span class="headline">
                                            {{ trans('messages.customer') }}
                                        </span>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout
                                                row
                                                wrap
                                                v-for="template in email_templates"
                                                :key="template.key"
                                                v-if="template.type == 'customer'"
                                            >
                                                <v-flex xs12 sm12>
                                                    <p class="title">{{ template.name }}:</p>
                                                    <v-text-field
                                                        v-model="
                                                            email_templates[template.key]['subject']
                                                        "
                                                        :label="trans('messages.email_subject')"
                                                        v-validate="'required'"
                                                        data-vv-name="email_subject"
                                                        :data-vv-as="
                                                            trans('messages.email_subject')
                                                        "
                                                        :error-messages="
                                                            errors.collect('email_subject')
                                                        "
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm12 md12>
                                                    {{ trans('messages.email_body') }}
                                                    <quill-editor
                                                        v-model="
                                                            email_templates[template.key]['body']
                                                        "
                                                    >
                                                    </quill-editor>
                                                    <small
                                                        >{{ trans('messages.available_tags') }}
                                                        {{ template.tags }}</small
                                                    >
                                                </v-flex>
                                                <v-flex
                                                    xs12
                                                    sm4
                                                    v-if="template.add_attachment == 1"
                                                >
                                                    <v-checkbox
                                                        :label="trans('messages.attach_pdf')"
                                                        color="primary"
                                                        value="1"
                                                        v-model="
                                                            email_templates[template.key][
                                                                'attachment'
                                                            ]
                                                        "
                                                        hide-details
                                                    ></v-checkbox>
                                                </v-flex>
                                                <v-flex xs12>
                                                    <v-divider class="mb-3 mt-3"></v-divider>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                                <v-card class="mb-3">
                                    <v-card-title>
                                        <span class="headline"
                                            >{{ trans('messages.employee') }}
                                        </span>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout
                                                row
                                                wrap
                                                v-for="template in email_templates"
                                                :key="template.key"
                                                v-if="template.type == 'employee'"
                                            >
                                                <v-flex xs12 sm12>
                                                    <p class="title">{{ template.name }}:</p>
                                                    <v-text-field
                                                        v-model="
                                                            email_templates[template.key]['subject']
                                                        "
                                                        :label="trans('messages.email_subject')"
                                                        v-validate="'required'"
                                                        data-vv-name="email_subject"
                                                        :data-vv-as="
                                                            trans('messages.email_subject')
                                                        "
                                                        :error-messages="
                                                            errors.collect('email_subject')
                                                        "
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm12 md12>
                                                    {{ trans('messages.email_body') }}
                                                    <quill-editor
                                                        v-model="
                                                            email_templates[template.key]['body']
                                                        "
                                                    >
                                                    </quill-editor>
                                                    <small
                                                        >{{ trans('messages.available_tags') }}
                                                        {{ template.tags }}</small
                                                    >
                                                </v-flex>
                                                <v-flex
                                                    xs12
                                                    sm4
                                                    v-if="template.add_attachment == 1"
                                                >
                                                    <v-checkbox
                                                        :label="trans('messages.attach_pdf')"
                                                        color="primary"
                                                        value="1"
                                                        v-model="
                                                            email_templates[template.key][
                                                                'attachment'
                                                            ]
                                                        "
                                                        hide-details
                                                    ></v-checkbox>
                                                </v-flex>
                                                <v-flex xs12>
                                                    <v-divider class="mb-3 mt-3"></v-divider>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                                <v-card>
                                    <v-card-title>
                                        <span class="headline">{{ trans('messages.leads') }} </span>
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout
                                                row
                                                wrap
                                                v-for="template in email_templates"
                                                :key="template.key"
                                                v-if="template.type == 'leads'"
                                            >
                                                <v-flex xs12 sm12>
                                                    <p class="title">{{ template.name }}:</p>
                                                    <v-text-field
                                                        v-model="
                                                            email_templates[template.key]['subject']
                                                        "
                                                        :label="trans('messages.email_subject')"
                                                        v-validate="'required'"
                                                        data-vv-name="email_subject"
                                                        :data-vv-as="
                                                            trans('messages.email_subject')
                                                        "
                                                        :error-messages="
                                                            errors.collect('email_subject')
                                                        "
                                                        required
                                                    ></v-text-field>
                                                </v-flex>
                                                <v-flex xs12 sm12 md12>
                                                    {{ trans('messages.email_body') }}
                                                    <quill-editor
                                                        v-model="
                                                            email_templates[template.key]['body']
                                                        "
                                                    >
                                                    </quill-editor>
                                                    <small
                                                        >{{ trans('messages.available_tags') }}
                                                        {{ template.tags }}</small
                                                    >
                                                </v-flex>
                                                <v-flex
                                                    xs12
                                                    sm4
                                                    v-if="template.add_attachment == 1"
                                                >
                                                    <v-checkbox
                                                        :label="trans('messages.attach_pdf')"
                                                        color="primary"
                                                        value="1"
                                                        v-model="
                                                            email_templates[template.key][
                                                                'attachment'
                                                            ]
                                                        "
                                                        hide-details
                                                    ></v-checkbox>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <!-- /Email Template Settings -->
                            <!-- Cron Settings -->
                            <v-tab-item v-if="$can('superadmin')">
                                <v-card flat>
                                    <v-card-text>
                                        <v-container grid-list-md>
                                            <v-layout row wrap>
                                                <v-flex xs12 sm12>
                                                    <strong
                                                        >{{
                                                            trans('messages.cron_command_label')
                                                        }}:</strong
                                                    >
                                                    <br />
                                                    <code>{{ cron_job_command }}</code>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <!-- /Cron Settings -->
                        </v-tabs>
                    </v-card-text>
                </v-card>
                <div class="text-xs-center mt-3">
                    <v-flex xs12 sm4 offset-sm4>
                        <v-btn
                            color="success"
                            @click="store"
                            :loading="loading"
                            :disabled="loading"
                            block
                        >
                            {{ trans('messages.update') }}
                        </v-btn>
                    </v-flex>
                </div>
            </v-flex>
        </v-layout>
    </div>
</template>
<script>
import CurrencyAdd from '../customers/components/currency/Add';
export default {
    components: {
        CurrencyAdd,
    },
    data() {
        return {
            form_fields: [],
            dropzone: null,
            logo: null,
            favicon: null,
            languages: [],
            dateFormats: [],
            timeFormats: [],
            timezones: [],
            loading: false,
            currencies: [],
            week_days: [],
            dropzoneFav: null,
            cron_job_command: '',
            email_templates: [],
        };
    },
    created() {
        this.getSystemDataFromApi();
        this.$eventBus.$on('updateCurrenciesList', data => {
            this.getCurrenciesListFromApi();
            this.form_fields.currency_id = data.id;
        });
    },
    beforeDestroy() {
        this.$eventBus.$off('updateCurrenciesList');
    },
    methods: {
        getSystemDataFromApi() {
            const self = this;
            axios
                .get('/admin/system-settings/create')
                .then(function(response) {
                    self.getCurrenciesListFromApi();
                    self.form_fields = response.data.system;
                    self.form_fields.currency_id = Number(response.data.system.currency_id);
                    self.logo = response.data.logo;
                    self.favicon = response.data.favicon;
                    self.languages = response.data.languages;
                    self.timezones = response.data.timezone_list;
                    self.dateFormats = response.data.date_formats;
                    self.timeFormats = response.data.time_formats;
                    self.form_fields.APP_LOCALE = response.data.default_values.APP_LOCALE;
                    self.form_fields.APP_NAME = response.data.default_values.APP_NAME;
                    self.form_fields.ENABLE_CLIENT_SIGNUP =
                        response.data.default_values.ENABLE_CLIENT_SIGNUP;
                    self.form_fields.APP_TIMEZONE = response.data.default_values.APP_TIMEZONE;
                    self.form_fields.APP_TITLE = response.data.default_values.APP_TITLE;
                    self.week_days = response.data.week_days;
                    self.cron_job_command = response.data.cron_job_command;
                    self.email_templates = response.data.email_templates;
                    self.initDropzone();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        initDropzone() {
            const self = this;
            if (self.dropzone) {
                self.dropzone.destroy();
            }
            self.dropzone = new Dropzone('div#uploadLogo', {
                url: APP.APP_URL + '/admin/system-settings-upload-logo',
                paramName: 'file',
                params: { type: 'logo' },
                uploadMultiple: false,
                maxFiles: 1,
                acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF',
                dictDefaultMessage: self.trans('messages.drop_image_here'),
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                init: function() {
                    var prevFile;
                    this.on('addedfile', function() {
                        if (typeof prevFile !== 'undefined') {
                            this.removeFile(prevFile);
                        }
                    });
                    this.on('success', function(file, response) {
                        prevFile = file;
                    });
                },
                success: function(file, response) {
                    self.form_fields.logo = response;
                },
            });

            if (self.dropzoneFav) {
                self.dropzoneFav.destroy();
            }
            self.dropzoneFav = new Dropzone('div#uploadFavicon', {
                url: APP.APP_URL + '/admin/system-settings-upload-logo',
                paramName: 'file',
                params: { type: 'favicon' },
                uploadMultiple: false,
                maxFiles: 1,
                acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF',
                dictDefaultMessage: self.trans('messages.drop_image_here'),
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                init: function() {
                    var prevFile;
                    this.on('addedfile', function() {
                        if (typeof prevFile !== 'undefined') {
                            this.removeFile(prevFile);
                        }
                    });
                    this.on('success', function(file, response) {
                        prevFile = file;
                    });
                },
                success: function(file, response) {
                    self.form_fields.favicon = response;
                },
            });
        },
        store() {
            const self = this;

            let data = _.pick(self.form_fields, [
                'tax_number',
                'mobile',
                'alternate_contact_no',
                'email',
                'city',
                'state',
                'country',
                'zip_code',
                'logo',
                'APP_NAME',
                'APP_TITLE',
                'APP_LOCALE',
                'date_format',
                'time_format',
                'APP_TIMEZONE',
                'currency_id',
                'first_day_of_week',
                'address_line_1',
                'address_line_2',
                'favicon',
                'ENABLE_CLIENT_SIGNUP',
            ]);
            data.email_templates = self.email_templates;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/system-settings', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                window.location.reload();
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
