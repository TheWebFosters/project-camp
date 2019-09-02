<template>
    <div class="component-wrap">
        <TaskShow ref="taskShow"></TaskShow>
        <v-menu offset-y min-width="400">
            <v-btn flat icon slot="activator" @click="markAsRead">
                <v-badge overlap right color="red">
                    <template slot="badge" v-if="notifications_count > 0">
                        <span> {{ notifications_count }} </span>
                    </template>
                    <v-icon dark medium>notifications</v-icon>
                </v-badge>
            </v-btn>

            <v-list
                three-line
                dense
                v-if="notifications.length"
                style="max-height: 400px"
                class="scroll-y"
            >
                <div v-for="(notification, index) in notifications" :key="index">
                    <!-- project notification -->
                    <v-list-tile
                        v-if="
                            notification.type &&
                                notification.type ==
                                    'App\\Notifications\\ProjectCreatedNotification'
                        "
                        @click="
                            $router.push({
                                name: 'projects.project-tasks.list',
                                params: { id: notification.project.id },
                            })
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.project.creator)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-uppercase font-weight-bold">
                                    {{ notification.project.creator.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.created_at | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{
                                    trans('messages.project_notification_text', {
                                        project_name: notification.project.name,
                                    })
                                }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <!-- task notification -->
                    <v-list-tile
                        @click="view(notification.task)"
                        v-if="
                            notification.type &&
                                notification.type == 'App\\Notifications\\TaskCreatedNotification'
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.task.task_creator)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-uppercase font-weight-bold">
                                    {{ notification.task.task_creator.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.created_at | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{
                                    trans('messages.task_notification_text', {
                                        task_subject: notification.task.subject,
                                    })
                                }}
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                {{ trans('messages.project') }} :
                                {{ notification.task.project.name }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <!-- leave notification (applied)-->
                    <v-list-tile
                        v-if="
                            notification.type &&
                                notification.type == 'App\\Notifications\\LeaveApplied'
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.applicant)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-uppercase font-weight-bold">
                                    {{ notification.applicant.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.created_at | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{ trans('messages.leave_applied_notification_text') }}
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                {{ trans('messages.date') }} :
                                {{ notification.leave.start_date | formatDate }}
                                {{ trans('messages.to') }}
                                {{ notification.leave.end_date | formatDate }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <!-- leave notification (responded)-->
                    <v-list-tile
                        v-if="
                            notification.type &&
                                notification.type == 'App\\Notifications\\LeaveResponded'
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.answered_by)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-uppercase font-weight-bold">
                                    {{ notification.answered_by.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.created_at | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{ trans('messages.leave_answered_notification_text') }}
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                {{ trans('messages.status') }} :
                                <span class="text-capitalize">
                                    {{ notification.leave_answered.status }}
                                </span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <!-- reminder -->
                    <v-list-tile
                        v-if="
                            notification.type &&
                                notification.type == 'App\\Notifications\\SendReminderNotification'
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.remind_to)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-capitalize font-weight-bold">
                                    {{ notification.remind_to.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.reminder.remind_on | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span class="text-capitalize font-weight-bold">
                                    {{ trans('messages.reminder_notification_text') }}
                                </span>
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                <span class="text-capitalize"
                                    >{{ notification.reminder.remind_for }}
                                </span>
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                <span class="text-capitalize">
                                    {{ trans('messages.lead') }} : {{ notification.lead.company }}
                                </span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <!-- ticket created -->
                    <v-list-tile
                        v-if="
                            notification.type &&
                                notification.type == 'App\\Notifications\\TicketCreated'
                        "
                        :style="notificationBackgroundColor(notification.read_at)"
                        avatar
                        @click="
                            $router.push({
                                name: 'tickets.view',
                                params: { id: notification.ticket.id },
                            })
                        "
                    >
                        <v-list-tile-avatar>
                            <avatar
                                :members="convertObjectToArray(notification.created_by)"
                                :tooltip="true"
                            >
                            </avatar>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="text-capitalize font-weight-bold">
                                    {{ notification.created_by.name }}
                                </span>
                                <span class="pl-5">
                                    {{ notification.created_at | formatDateTime }}
                                </span>
                            </v-list-tile-title>
                            <v-list-tile-sub-title>
                                <span
                                    v-html="
                                        trans('messages.created_ticket', {
                                            ref_no: notification.ticket.reference_no,
                                        })
                                    "
                                ></span>
                            </v-list-tile-sub-title>
                            <v-list-tile-sub-title>
                                <span class="text-capitalize">
                                    {{ trans('messages.title') }} : {{ notification.ticket.title }}
                                </span>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-divider v-if="index + 1 < notifications.length" :key="index"></v-divider>
                </div>
                <v-list-tile>
                    <v-btn
                        outline
                        small
                        color="info"
                        dark
                        block
                        v-if="!_.isNull(url)"
                        @click="markAsRead"
                    >
                        {{ trans('messages.load_more') }}
                    </v-btn>
                </v-list-tile>
            </v-list>
            <v-list v-else>
                <v-list-tile>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            <p v-if="loading" class="text-md-center font-weight-medium">
                                {{ trans('messages.loading') }}
                            </p>

                            <p
                                v-if="notifications.length == 0 && loading == false"
                                class="text-md-center font-weight-medium"
                            >
                                {{ trans('messages.no_notification_found') }}
                            </p>
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-menu>
    </div>
</template>
<script>
import TaskShow from '../projects/tasks/Show';
export default {
    components: {
        TaskShow,
    },
    data() {
        return {
            notifications_count: null,
            badgeColor: {
                color: 'red',
            },
            notifications: [],
            url: null,
            loading: false,
        };
    },
    mounted: function() {
        const self = this;
        self.url = '/notifications-mark-as-read';
        self.getNotificationsFromApi();
        setInterval(() => {
            self.getNotificationsFromApi();
        }, APP.NOTIFICATION_REFRESH_TIMEOUT);
    },
    methods: {
        getNotificationsFromApi() {
            const self = this;
            axios
                .get('/notifications')
                .then(function(response) {
                    self.notifications_count = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        markAsRead() {
            const self = this;

            self.loading = true;
            if (self.url == null) {
                self.url = '/notifications-mark-as-read';
                self.notifications = [];
            }

            axios
                .get(self.url)
                .then(function(response) {
                    self.loading = false;
                    self.notifications = _.concat(self.notifications, response.data.data);
                    self.url = _.get(response, 'data.next_page_url', null);
                    self.getNotificationsFromApi();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        view(task) {
            const self = this;
            self.$refs.taskShow.view(task);
        },
        notificationBackgroundColor(type) {
            if (type != null) {
                return { opacity: '0.6' };
            }
        },
        convertObjectToArray(notificationObject) {
            var user = [];

            user.push(notificationObject);

            return user;
        },
    },
};
</script>
