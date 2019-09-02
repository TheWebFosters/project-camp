<template>
    <div class="component-wrap">
        <v-container grid-list-md>
            <v-layout wrap>
                <!-- task & chart-->
                <v-flex xs12 sm8 md8>
                    <v-layout row>
                        <v-flex xs12 md3>
                            <v-card elevation="4">
                                <v-card-title>
                                    <v-icon left> assignment </v-icon>
                                    <span class="title font-weight-light">
                                        {{ remainingTask }}
                                    </span>
                                </v-card-title>
                                <v-flex xs7 offset-xs5 offset-md2 offset-lg3>
                                    <v-card-text> {{ trans('messages.tasks') }} </v-card-text>
                                </v-flex>
                            </v-card>
                        </v-flex>
                        <v-flex xs12 md3>
                            <v-card elevation="4">
                                <v-card-title>
                                    <v-icon left> outlined_flag </v-icon>
                                    <span class="title font-weight-light"> {{ milestone }} </span>
                                </v-card-title>
                                <v-flex xs7 offset-xs5 offset-md2 offset-lg2>
                                    <v-card-text> {{ trans('messages.milestones') }} </v-card-text>
                                </v-flex>
                            </v-card>
                        </v-flex>
                        <v-flex xs12 md3>
                            <v-card elevation="4">
                                <v-card-title>
                                    <v-icon left> receipt </v-icon>
                                    <span class="title font-weight-light"> {{ invoice }} </span>
                                </v-card-title>
                                <v-flex xs7 offset-xs5 offset-md2 offset-lg2>
                                    <v-card-text> {{ trans('messages.invoices') }} </v-card-text>
                                </v-flex>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs12 sm12 md12>
                            <v-card elevation="4">
                                <v-card-text> <canvas id="myChart"></canvas> </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout v-if="project.description">
                        <v-flex xs12 sm12 md12>
                            <v-card elevation="4">
                                <v-card-text>
                                    <v-tooltip top>
                                        <span v-html="project.description" slot="activator"></span>
                                        <span>{{ trans('messages.description') }}</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-flex>
                <!-- /task & chart -->
                <!-- project member -->
                <v-flex xs12 sm4 md4>
                    <v-card elevation="4">
                        <v-card-title>
                            <v-icon>assessment</v-icon>
                            {{ project.name }}
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-layout row wrap>
                                {{ trans('messages.project_progress') }}
                                <v-flex xs12 sm12 md12 class="text-md-center">
                                    <v-progress-circular
                                        :rotate="180"
                                        :size="80"
                                        :width="8"
                                        :value="
                                            projectProgress(
                                                project.tasks_count,
                                                project.completed_task
                                            )
                                        "
                                        color="teal"
                                    >
                                        {{
                                            projectProgress(
                                                project.tasks_count,
                                                project.completed_task
                                            )
                                        }}
                                    </v-progress-circular>
                                </v-flex>
                            </v-layout>
                            <v-divider></v-divider>
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md12>
                                    <div v-if="customer">
                                        <v-icon>business_center</v-icon>
                                        {{ customer.company }} <br />

                                        <b>{{ trans('messages.tax_number') }}: </b>
                                        {{ customer.tax_number }}
                                        <br />

                                        <b>{{ trans('messages.mobile') }}: </b>
                                        {{ customer.mobile }} <br />

                                        <b>{{ trans('messages.shipping_address') }}: </b>
                                        <span id="project_info">
                                            {{ customer.shipping_address }} </span
                                        ><br />
                                    </div>
                                    <b>{{ trans('messages.status') }}:</b>
                                    {{ trans('messages.' + project.status) }} <br />
                                    <b>{{ trans('messages.category') }}:</b> <br />
                                    <span v-for="category in categories">
                                        <v-chip small>{{ category.name }}</v-chip>
                                    </span>
                                    <v-divider></v-divider>
                                    <b>{{ trans('messages.members') }}</b>
                                </v-flex>
                            </v-layout>
                            <avatar :members="projectMembers"></avatar>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <!-- /project member -->
            </v-layout>
        </v-container>
    </div>
</template>
<script>
import avatar from '../components/Avatar';
export default {
    components: {
        avatar,
    },
    data() {
        return {
            projectId: null,
            remainingTask: null,
            project: [],
            customer: [],
            projectMembers: [],
            projectLead: '',
            milestone: null,
            invoice: null,
            categories: [],
        };
    },
    mounted() {
        const self = this;
        self.projectId = self.$route.params.id;
        self.getProjectOverviewFromApi(self.projectId);
    },
    methods: {
        getProjectOverviewFromApi(projectId) {
            axios
                .get('/projects/' + projectId)
                .then(response => {
                    this.remainingTask = response.data.task;
                    this.project = response.data.project;
                    this.customer = response.data.project.customer;
                    this.projectMembers = response.data.project.members;
                    this.projectLead = response.data.project.lead;
                    this.milestone = response.data.milestone;
                    this.invoice = response.data.invoice;
                    this.categories = response.data.project.categories;
                    this.initChartJs(response.data.x_axis, response.data.y_axis);
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        initChartJs(x_axis_data, y_axis_data) {
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: x_axis_data,
                    datasets: [
                        {
                            label: 'Task Added',
                            data: y_axis_data,
                            borderColor: '#36a2eb',
                        },
                    ],
                },

                // Configuration options go here
                options: {},
            });
        },
    },
};
</script>
