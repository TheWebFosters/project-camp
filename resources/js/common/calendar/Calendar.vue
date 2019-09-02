<template>
    <div class="component-wrap" v-if="$hasRole('employee')">
        <!-- view task -->
        <TaskShow ref="taskShow"></TaskShow>
        <!-- view invoice -->
        <InvoiceShow ref="invoiceShow"></InvoiceShow>

        <v-btn flat icon @click="dialog = true">
            <v-icon dark medium>event_available</v-icon>
        </v-btn>
        <v-layout row justify-center>
            <v-dialog
                v-model="dialog"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
            >
                <v-card>
                    <v-toolbar dense flat>
                        <v-btn icon @click="dialog = false">
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>
                            {{ trans('messages.calendar') }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-chip label small color="#FF5252">
                            <v-icon left small>work_off</v-icon>
                            {{ trans('messages.leave') }}
                        </v-chip>
                        <v-chip label small color="#607D8B">
                            <v-icon left small>assessment</v-icon>
                            {{ trans('messages.project') }}
                        </v-chip>
                        <v-chip label small color="#795548">
                            <v-icon left small>assignment</v-icon>
                            {{ trans('messages.task') }}
                        </v-chip>
                        <v-chip label small color="#FF8A65">
                            <v-icon left small>receipt</v-icon>
                            {{ trans('messages.invoice') }}
                        </v-chip>
                    </v-toolbar>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout>
                                <v-flex xs12 sm3 md3>
                                    <v-btn-toggle dark>
                                        <v-btn flat @click="$refs.calendar.prev()">
                                            <v-icon>arrow_back_ios</v-icon>
                                            {{ trans('messages.prev') }}
                                        </v-btn>
                                        <v-btn flat @click="$refs.calendar.next()">
                                            {{ trans('messages.next') }}
                                            <v-icon>arrow_forward_ios</v-icon>
                                        </v-btn>
                                    </v-btn-toggle>
                                </v-flex>
                                <v-flex xs12 sm6 md6 text-md-center>
                                    <span class="display-1"> {{ calendarHeading }}</span>
                                </v-flex>
                                <v-flex xs12 sm3 md3 text-lg-right>
                                    <v-btn-toggle dark v-model="toggle_calendar_type">
                                        <v-btn flat @click="type = 'month'">
                                            {{ trans('messages.month') }}
                                        </v-btn>
                                        <v-btn flat @click="type = 'week'">
                                            {{ trans('messages.week') }}
                                        </v-btn>
                                        <v-btn flat @click="type = 'day'">
                                            {{ trans('messages.day') }}
                                        </v-btn>
                                    </v-btn-toggle>
                                </v-flex>
                            </v-layout>
                            <v-divider></v-divider>
                            <v-layout>
                                <v-flex>
                                    <v-sheet height="520">
                                        <v-calendar
                                            ref="calendar"
                                            v-model="start"
                                            :type="type"
                                            :now="now"
                                            :value="now"
                                            color="primary"
                                            @change="calendarChange"
                                        >
                                            <template v-slot:day="{ date }">
                                                <template v-for="event in eventsMap[date]">
                                                    <v-tooltip top>
                                                        <template slot="activator">
                                                            <div
                                                                class="my-event"
                                                                @click="seeEvent(event)"
                                                                :style="eventColor(event)"
                                                            >
                                                                <span
                                                                    v-html="event.name"
                                                                    :style="isEventFinished(event)"
                                                                >
                                                                </span>
                                                            </div>
                                                        </template>
                                                        <span>{{ event.name }}</span>
                                                    </v-tooltip>
                                                </template>
                                            </template>

                                            <template v-slot:dayHeader="{ date }">
                                                <template v-for="event in eventsMap[date]">
                                                    <!-- all day events don't have time -->
                                                    <v-tooltip top>
                                                        <template slot="activator">
                                                            <div
                                                                v-if="!event.time"
                                                                @click="seeEvent(event)"
                                                                :style="eventColor(event)"
                                                                class="my-event"
                                                            >
                                                                <span
                                                                    v-html="event.name"
                                                                    :style="isEventFinished(event)"
                                                                >
                                                                </span>
                                                            </div>
                                                        </template>
                                                        <span>{{ event.name }}</span>
                                                    </v-tooltip>
                                                </template>
                                            </template>
                                        </v-calendar>
                                    </v-sheet>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>
<script>
import TaskShow from '../projects/tasks/Show';
import InvoiceShow from '../projects/invoices/Show';
export default {
    components: {
        TaskShow,
        InvoiceShow,
    },
    data: () => ({
        type: 'month',
        start: moment()
            .startOf('month')
            .format('YYYY-MM-DD'),
        end: moment()
            .endOf('month')
            .format('YYYY-MM-DD'),
        now: moment(new Date()).format('YYYY-MM-DD'),
        typeOptions: [
            { text: 'Month', value: 'month' },
            { text: 'Week', value: 'week' },
            { text: 'Day', value: 'day' },
        ],
        calendarOverViews: [],
        calendarHeading: '',
        toggle_calendar_type: 0,
        dialog: false,
    }),
    mounted() {
        var params = {};
        this.calendarHeading = moment(this.start).format('MMMM YYYY');
        params['start'] = this.start;
        params['end'] = this.end;
        this.getCalendarDatafromApi(params);
    },
    computed: {
        eventsMap() {
            const event = {};
            this.calendarOverViews.forEach(e => (event[e.date] = event[e.date] || []).push(e));
            return event;
        },
    },
    methods: {
        calendarChange(calendar) {
            var params = {};
            if (this.type === 'month') {
                this.calendarHeading = moment(calendar.start.date).format('MMMM YYYY');
            } else if (this.type === 'day') {
                this.calendarHeading = moment(calendar.start.date).format('MMMM DD, YYYY');
            } else if (this.type === 'week') {
                var heading = '';
                heading =
                    moment(calendar.start.date).format('MMMM DD, YYYY') +
                    ' - ' +
                    moment(calendar.end.date).format('MMMM DD, YYYY');
                this.calendarHeading = heading;
            }
            params['start'] = calendar.start.date;
            params['end'] = calendar.end.date;
            this.getCalendarDatafromApi(params);
        },
        getCalendarDatafromApi(params) {
            const self = this;
            axios
                .get('/calendar-overview', {
                    params: params,
                })
                .then(function(response) {
                    self.calendarOverViews = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        eventColor(event) {
            if (event.type === 'project') {
                return 'background-color: #607D8B';
            } else if (event.type === 'task') {
                return 'background-color: #795548;';
            } else if (event.type === 'invoice') {
                return 'background-color: #FF8A65;';
            } else if (event.type === 'leave') {
                return 'background-color: #FF5252;';
            }
        },
        isEventFinished(event) {
            if (
                event.is_completed == 1 ||
                event.payment_status == 'paid' ||
                event.status == 'completed'
            ) {
                return 'text-decoration: line-through;';
            }
        },
        seeEvent(event) {
            if (event.type === 'project') {
                this.dialog = false;
                this.$router.push({
                    name: 'projects.project-tasks.list',
                    params: { id: event.id },
                });
            } else if (event.type === 'task') {
                this.$refs.taskShow.view(event);
            } else if (event.type === 'invoice') {
                var data = { transaction_id: event.id, project_id: event.project_id };
                this.$refs.invoiceShow.view(data);
            }
        },
    },
};
</script>
<style scoped>
.my-event {
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    border-radius: 2px;
    color: #ffffff;
    border: 1px solid;
    width: 100%;
    font-size: 12px;
    padding: 3px;
    cursor: pointer;
    margin-bottom: 1px;
}
</style>
