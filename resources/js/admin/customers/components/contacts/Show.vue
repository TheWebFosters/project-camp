<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="600">
            <!-- card -->
            <v-card>
                <v-card-title class="headline">
                    {{ trans('messages.view_contact') }}
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false">
                        <v-icon>clear</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <!-- container -->
                    <v-container align-center>
                        <v-layout row wrap>
                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.name') }} : </strong>
                                {{ contactData.name }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.email') }} : </strong>
                                {{ contactData.email }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.mobile') }} : </strong>
                                {{ contactData.mobile }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.alternate_num') }} : </strong>
                                {{ contactData.alternate_num }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.skype') }} : </strong>
                                {{ contactData.skype }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.linkedin') }} : </strong>
                                {{ contactData.linkedin }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.facebook') }} : </strong>
                                {{ contactData.facebook }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.twitter') }} : </strong>
                                {{ contactData.twitter }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.gender') }} : </strong>
                                {{ trans('messages.' + contactData.gender) }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.address') }} : </strong>
                                {{ contactData.address }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.note') }} : </strong>
                                {{ contactData.note }}
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <!-- /container -->
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
            <!-- /card -->
        </v-dialog>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            contactData: [],
            dialog: false,
        };
    },
    methods: {
        view(data) {
            const self = this;
            axios
                .get('/admin/contacts/' + data.id, { params: { customer_id: data.customer_id } })
                .then(function(response) {
                    self.contactData = response.data;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
