<template>
    <div class="component-wrap">
        <v-timeline dense clipped>
            <v-timeline-item
                v-for="log in logs"
                :key="log.id"
                class=""
                :color="color(log.description)"
                icon-color="grey lighten-2"
                small
                :icon="icon(log.subject_type)"
            >
                <v-layout justify-space-between>
                    <v-flex xs7>
                        <span v-if="log.subject_type && log.subject_type == 'App\\Project'">
                            <span v-if="log.description == 'status'">
                                {{
                                    trans('messages.project_status_updated_activity', {
                                        name: log.causer.name,
                                        to: log.properties.to,
                                    })
                                }}
                            </span>

                            <span v-if="_.includes(['created', 'updated'], log.description)">
                                {{
                                    trans('messages.project_other_activity', {
                                        name: log.causer.name,
                                        description: log.description,
                                    })
                                }}
                            </span>
                        </span>

                        <span v-if="log.subject_type && log.subject_type == 'App\\ProjectTask'">
                            {{
                                trans('messages.task_activity', {
                                    name: log.causer.name,
                                    description: log.description,
                                    subject: log.subject.task_id + ' ' + log.subject.subject,
                                })
                            }}
                        </span>

                        <span
                            v-if="log.subject_type && log.subject_type == 'App\\ProjectMilestone'"
                        >
                            {{
                                trans('messages.milestone_activity', {
                                    name: log.causer.name,
                                    description: log.description,
                                    subject: log.properties.attributes.name,
                                })
                            }}
                        </span>

                        <span v-if="log.subject_type && log.subject_type == 'App\\Transaction'">
                            {{
                                trans('messages.invoice_activity', {
                                    name: log.causer.name,
                                    description: log.description,
                                    subject: log.subject.ref_no,
                                })
                            }}
                        </span>
                    </v-flex>
                    <v-flex xs5 text-xs-right>
                        <v-tooltip top>
                            <template slot="activator">
                                {{ $appFormatters.timeFromNow(log.created_at) }}
                            </template>

                            <span>{{ log.created_at | formatDateTime }}</span>
                        </v-tooltip>
                    </v-flex>
                </v-layout>
            </v-timeline-item>
        </v-timeline>

        <v-flex xs8 sm8 offset-xs2 offset-sm2 align-self-center>
            <v-btn
                outline
                small
                color="info"
                block
                v-if="!_.isNull(url)"
                v-on:click="getProjectActivitiesFromApi"
            >
                {{ trans('messages.load_more') }}
            </v-btn>
        </v-flex>
    </div>
</template>
<script>
export default {
    data() {
        return {
            project_id: this.$route.params.id,
            logs: [],
            url: null,
        };
    },
    methods: {
        getProjectActivities() {
            this.url = '/activities/project';
            this.logs = [];
            this.getProjectActivitiesFromApi();
        },
        getProjectActivitiesFromApi() {
            const self = this;
            axios
                .get(self.url, { params: { project_id: self.project_id } })
                .then(function(response) {
                    self.logs = _.concat(self.logs, response.data.data);
                    self.url = _.get(response, 'data.next_page_url', null);
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        icon: function(subject_type) {
            if (subject_type == 'App\\Project') {
                return 'assessment';
            } else if (subject_type == 'App\\ProjectTask') {
                return 'assignment';
            } else if (subject_type == 'App\\ProjectMilestone') {
                return 'outlined_flag';
            } else if (subject_type == 'App\\Transaction') {
                return 'receipt';
            }
        },
        color: function(description) {
            if (description == 'status') {
                return 'pink';
            } else if (description == 'created') {
                return 'green';
            } else if (description == 'updated') {
                return 'blue';
            } else if (description == 'deleted') {
                return 'red';
            }
        },
    },
};
</script>
