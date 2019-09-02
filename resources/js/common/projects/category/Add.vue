<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" width="500">
            <v-card>
                <v-card-title>
                    <v-icon medium>category</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_category') }}
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
                                    v-model="name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
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
            name: null,
            type: null,
            project_id: null,
            loading: false,
        };
    },
    methods: {
        create(data) {
            const self = this;
            self.name = null;
            (self.type = data.type), self.$validator.reset();
            self.dialog = true;
            if (data.project_id) {
                self.project_id = data.project_id;
            } else {
                self.project_id = null;
            }
        },
        store() {
            const self = this;
            let data = {
                name: self.name,
                type: self.type,
                project_id: self.project_id,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/categories', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateCategoryList', response.data.category);
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
