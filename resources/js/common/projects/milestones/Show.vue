<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" max-width="900">
            <!-- card -->
            <v-card>
                <v-card-title class="headline">
                    <v-icon medium>outlined_flag</v-icon>
                    {{ trans('messages.view_milestone') }}
                    <v-spacer></v-spacer>
                    <v-icon @click="dialog = false">clear</v-icon>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <!-- container -->
                    <v-container align-center>
                        <v-layout row wrap>
                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.name') }} : </strong>
                                {{ milestone.name }}
                            </v-flex>

                            <v-flex xs12 md6>
                                <strong>{{ trans('messages.due_date') }} : </strong>
                                {{ milestone.due_date | formatDateTime }}
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 md12>
                                <strong>{{ trans('messages.description') }} : </strong> <br />
                                <span v-html="milestone.description"> </span>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <!-- /container -->
                </v-card-text>
                <v-divider></v-divider>
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
            dialog: false,
            styleObject: {
                'white-space': 'pre-line',
            },
            milestone: [],
        };
    },
    methods: {
        view(data) {
            const self = this;
            axios
                .get('/project-milestones/' + data.id, {
                    params: { project_id: data.project_id },
                })
                .then(function(response) {
                    self.milestone = response.data;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
