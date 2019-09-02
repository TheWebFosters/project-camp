<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" width="500">
            <v-card>
                <v-card-title>
                    <v-icon medium>money</v-icon>
                    <span class="headline">
                        {{ trans('messages.add_currency') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="dialog = false" icon> <v-icon>clear</v-icon> </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form_fields.iso_name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
                                    required
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form_fields.symbol"
                                    :label="trans('messages.symbol')"
                                    v-validate="'required'"
                                    data-vv-name="symbol"
                                    :data-vv-as="trans('messages.symbol')"
                                    :error-messages="errors.collect('symbol')"
                                    required
                                >
                                </v-text-field>
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
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            form_fields: [],
            loading: false,
        };
    },
    methods: {
        create() {
            const self = this;
            self.form_fields = [];
            self.$validator.reset();
            self.dialog = true;
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, ['iso_name', 'symbol']);
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/currencies', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });
                            console.log(response.data);
                            if (response.data.success === true) {
                                self.$eventBus.$emit(
                                    'updateCurrenciesList',
                                    response.data.currency
                                );
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
