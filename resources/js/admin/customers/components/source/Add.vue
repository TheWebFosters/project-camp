<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" width="500">
            <v-card>
                <v-card-title>
                    <v-icon medium>pageview</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_source') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="dialog = false" icon> <v-icon>clear</v-icon> </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="source"
                                    :label="trans('messages.source')"
                                    v-validate="'required'"
                                    data-vv-name="source"
                                    :data-vv-as="trans('messages.source')"
                                    :error-messages="errors.collect('source')"
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
            source: null,
            loading: false,
        };
    },
    methods: {
        create(data) {
            const self = this;
            self.source = null;
            self.$validator.reset();
            self.dialog = true;
        },
        store() {
            const self = this;
            let data = {
                name: self.source,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/source', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateSourceList', response.data.source);
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
