<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="800px">
            <v-card>
                <v-card-title>
                    <span class="headline">
                        <v-icon medium>alarm_on</v-icon>
                        {{ data.remind_for }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="dialog = false">clear</v-icon>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md6>
                                {{ trans('messages.remind_to') }} : {{ data.remind_to }}
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                {{ trans('messages.remind_on') }} :
                                {{ data.remind_on | formatDate }}
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md6>
                                <b>{{ trans('messages.remind_for') }}</b>
                                <br />
                                {{ data.remind_for }}
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <b>{{ trans('messages.notes') }}</b> <br />
                                {{ data.notes }}
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
            data: [],
        };
    },
    methods: {
        show(data) {
            const self = this;
            axios
                .get('/admin/reminders/' + data.reminder_id, {
                    params: { lead_id: data.lead_id },
                })
                .then(function(response) {
                    self.data = response.data;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
