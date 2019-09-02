<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" width="800">
            <v-card>
                <v-card-title>
                    <v-icon medium>money</v-icon>
                    <span class="headline">
                        {{ trans('messages.new_scheme') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="dialog = false" icon> <v-icon>clear</v-icon> </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs8 sm8 md8>
                                <v-radio-group v-model="type" row @change="preview">
                                    <v-radio
                                        :label="trans('messages.custom_prefix')"
                                        value="custom"
                                    ></v-radio>
                                    <v-radio
                                        :label="trans('messages.year_prefix')"
                                        value="year"
                                    ></v-radio>
                                </v-radio-group>
                            </v-flex>

                            <v-flex xs4 sm4 md4 v-if="preview_text">
                                <v-card color="blue-grey darken-2" class="white--text">
                                    <v-card-title primary-title>
                                        <span>{{ preview_text }}</span>
                                    </v-card-title>
                                </v-card>
                            </v-flex>

                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
                                    required
                                    @change="preview"
                                >
                                </v-text-field>
                            </v-flex>

                            <v-flex xs6 sm6 md6>
                                <v-text-field
                                    v-model="prefix"
                                    :label="trans('messages.prefix')"
                                    v-validate="'required'"
                                    data-vv-name="prefix"
                                    :data-vv-as="trans('messages.prefix')"
                                    :error-messages="errors.collect('prefix')"
                                    required
                                    :disabled="type == 'year'"
                                    @change="preview"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs6 sm6 md6>
                                <v-text-field
                                    v-model="start_from"
                                    :label="trans('messages.start_from')"
                                    v-validate="'required'"
                                    data-vv-name="start_from"
                                    :data-vv-as="trans('messages.start_from')"
                                    :error-messages="errors.collect('start_from')"
                                    required
                                    @change="preview"
                                >
                                </v-text-field>
                            </v-flex>

                            <v-flex xs6 sm6 md6>
                                <v-select
                                    :items="no_of_digits_items"
                                    v-model="no_of_digits"
                                    @change="preview"
                                    :label="trans('messages.no_of_digits')"
                                ></v-select>
                            </v-flex>
                            <v-flex xs6 sm6 md6>
                                <v-checkbox
                                    v-model="is_default"
                                    :label="trans('messages.is_default')"
                                ></v-checkbox>
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
                    <v-btn color="success" @click="store"> {{ trans('messages.save') }} </v-btn>
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
            type: null,
            name: null,
            prefix: '',
            start_from: 1,
            is_default: false,
            no_of_digits_items: [1, 2, 3, 4, 5, 6, 7, 8, 9],
            no_of_digits: 1,
            preview_text: null,
        };
    },
    methods: {
        preview() {
            if (this.type == 'custom') {
                this.preview_text = '#' + this.prefix;
            } else if (this.type == 'year') {
                this.preview_text = '#' + moment().format('YYYY');
            }
            this.preview_text =
                this.preview_text + '-' + _.padStart(this.start_from, this.no_of_digits, 0);
        },
        create() {
            const self = this;
            self.dialog = true;
            self.type = null;
            self.name = null;
            self.prefix = '';
            self.start_from = 1;
            self.is_default = false;
            self.no_of_digits = 1;
            self.preview_text = null;
            self.$validator.reset();
        },
        store() {
            const self = this;
            let data = {
                name: self.name,
                scheme_type: self.type,
                prefix: self.prefix,
                start_number: self.start_from,
                total_digits: self.no_of_digits,
                is_default: self.is_default,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    axios
                        .post('/admin/invoice-scheme', data)
                        .then(function(response) {
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateInvoiceSchemeTable');
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
