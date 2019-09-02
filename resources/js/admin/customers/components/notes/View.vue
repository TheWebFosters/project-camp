<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog">
            <!-- card -->
            <v-card>
                <v-card-title class="headline">
                    {{ data.heading }}
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
                            <v-flex xs12 md12>
                                <span v-html="data.description"></span>
                            </v-flex>
                        </v-layout>
                    </v-container>

                    <v-container v-show="data.media && data.media.length">
                        <v-divider></v-divider>
                        <v-list>
                            <v-subheader inset> {{ trans('messages.documents') }} </v-subheader>
                            <v-list-tile v-for="item in data.media" :key="item.id">
                                <v-list-tile-action>
                                    <a :href="item.download_url"> <v-icon>archive</v-icon> </a>
                                </v-list-tile-action>

                                <v-list-tile-content>
                                    <v-list-tile-title
                                        v-text="item.display_name"
                                    ></v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-container>

                    <!-- /container -->
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <div class="caption">
                        <v-icon right dark>create</v-icon>
                        {{ _.get(data, 'user.name', null) }}

                        <v-icon right dark>calendar_today</v-icon>
                        {{ data.created_at | formatDate }}
                    </div>
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
            dialog: false,
            data: [],
        };
    },
    methods: {
        view(data) {
            const self = this;

            axios
                .get('/admin/customer-notes/' + data.id, {
                    params: { customer_id: data.customer_id },
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
