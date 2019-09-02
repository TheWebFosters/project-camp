<template>
    <v-layout row wrap>
        <v-container fluid grid-list-md>
            <v-layout row wrap>
                <v-flex xs12 sm4 md4>
                    <v-card>
                        <v-expansion-panel v-model="panel" expand>
                            <v-expansion-panel-content>
                                <template v-slot:header>
                                    <div class="subheading">
                                        {{ trans('messages.ticket_info') }}
                                    </div>
                                </template>
                                <v-card>
                                    <v-card-text>
                                        <v-layout row>
                                            <v-flex xs12 sm12 md12>
                                                {{ ticket.reference_no }}
                                                <status-label :status="ticket.status">
                                                </status-label>
                                            </v-flex>
                                        </v-layout>
                                        <v-divider></v-divider>
                                        <v-layout row>
                                            <v-flex xs12 sm12 md12>
                                                <span class="grey--text text--darken-1">
                                                    {{ trans('messages.ticket_type') }} </span
                                                ><br />
                                                {{ ticket_type.name }}
                                            </v-flex>
                                        </v-layout>
                                        <v-divider></v-divider>
                                        <v-layout row>
                                            <v-flex xs12 sm12 md12>
                                                <span class="grey--text text--darken-1">
                                                    {{ trans('messages.submitted') }} </span
                                                ><br />
                                                {{ ticket.created_at | formatDateTime }}
                                            </v-flex>
                                        </v-layout>
                                        <v-divider></v-divider>
                                        <v-layout>
                                            <v-flex xs12 sm12 md12>
                                                <span class="grey--text text--darken-1">
                                                    {{ trans('messages.last_updated') }} </span
                                                ><br />

                                                {{
                                                    trans('messages.last_updated_by_name', {
                                                        name: last_update_by.name,
                                                    })
                                                }}
                                                {{ ticket.updated_at | formatDateTime }}
                                            </v-flex>
                                        </v-layout>
                                        <v-divider></v-divider>
                                        <v-layout>
                                            <v-flex xs12 sm12 md12>
                                                <span class="grey--text text--darken-1">
                                                    {{ trans('messages.priority') }} </span
                                                ><br />
                                                <status-label
                                                    v-if="!_.isEmpty(ticket.priority)"
                                                    :status="ticket.priority"
                                                >
                                                </status-label>
                                            </v-flex>
                                        </v-layout>
                                        <v-divider></v-divider>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-btn
                                            color="error"
                                            depressed
                                            small
                                            @click="updateStatus('closed')"
                                            v-if="ticket.status !== 'closed'"
                                        >
                                            <v-icon small>clear</v-icon>
                                            {{ trans('messages.closed') }}
                                        </v-btn>

                                        <v-btn
                                            color="success"
                                            depressed
                                            small
                                            @click="updateStatus('open')"
                                            v-if="ticket.status == 'closed'"
                                        >
                                            <v-icon small>check</v-icon>
                                            {{ trans('messages.reopen') }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm8 md8>
                    <v-layout row>
                        <v-flex xs12 sm12 md12>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">
                                        {{ ticket.title }}
                                    </span>
                                </v-card-title>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <v-container fluid grid-list-md>
                                        <v-layout row>
                                            <v-flex xs12 sm12 md12>
                                                <span v-html="ticket.description"></span>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex xs12 sm12 md12>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">
                                        {{ trans('messages.comments') }}
                                    </span>
                                </v-card-title>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <v-container fluid grid-list-md>
                                        <v-layout row wrap>
                                            <v-flex xs12 sm12 md12 v-if="reply">
                                                <v-textarea
                                                    rows="4"
                                                    v-model="form_fields.comment"
                                                    :label="trans('messages.comment')"
                                                    v-validate="'required'"
                                                    data-vv-name="comment"
                                                    :data-vv-as="trans('messages.comment')"
                                                    :error-messages="errors.collect('comment')"
                                                    required
                                                ></v-textarea>
                                                <v-btn color="success" @click="saveComment">{{
                                                    trans('messages.reply')
                                                }}</v-btn>
                                                <v-btn color="error" @click="closeReply">
                                                    {{ trans('messages.close') }}</v-btn
                                                >
                                            </v-flex>
                                            <v-flex xs12 sm12 md12>
                                                <v-btn
                                                    block
                                                    color="blue lighten-4"
                                                    @click="replyToComment"
                                                    v-if="reply_btn"
                                                >
                                                    <v-icon small>
                                                        edit
                                                    </v-icon>
                                                    {{ trans('messages.reply') }}
                                                </v-btn>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                    <v-container>
                                        <v-layout v-for="(comment, index) in comments" :key="index">
                                            <v-flex xs12 sm12 md12>
                                                <v-card class="elevation-3">
                                                    <v-card-text>
                                                        <p>
                                                            <v-icon small>
                                                                edit
                                                            </v-icon>
                                                            {{ comment.user.name }}
                                                            <v-icon small class="pl-3">
                                                                date_range
                                                            </v-icon>
                                                            {{
                                                                comment.created_at | formatDateTime
                                                            }}
                                                        </p>
                                                        <br />
                                                        <p>
                                                            <span v-html="comment.comment"> </span>
                                                        </p>
                                                    </v-card-text>
                                                </v-card>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
        </v-container>
    </v-layout>
</template>
<script>
import StatusLabel from '../../admin/status/StatusLabel';
export default {
    components: {
        StatusLabel,
    },
    data() {
        return {
            ticket_id: null,
            ticket: [],
            panel: [true],
            ticket_type: [],
            form_fields: [],
            reply: false,
            reply_btn: true,
            comments: [],
            last_update_by: [],
        };
    },
    created() {
        this.ticket_id = this.$route.params.id;
        this.getTicketInfoFromApi();
        this.getCommentForTicket();
    },
    methods: {
        getTicketInfoFromApi() {
            const self = this;
            axios
                .get('/tickets/' + self.ticket_id)
                .then(function(response) {
                    self.ticket = response.data;
                    self.ticket_type = response.data.ticket_type;
                    self.last_update_by = response.data.last_update_by;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        updateStatus(status) {
            const self = this;
            axios
                .get('/tickets-update-status', {
                    params: { ticket_id: self.ticket_id, status: status },
                })
                .then(function(response) {
                    self.getTicketInfoFromApi();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        saveComment() {
            const self = this;
            let data = _.pick(self.form_fields, ['comment']);
            data.ticket_id = self.ticket_id;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/ticket-comments', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.form_fields = [];
                                self.reply = false;
                                self.reply_btn = true;
                                self.getTicketInfoFromApi();
                                self.getCommentForTicket();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        replyToComment() {
            this.reply = true;
            this.reply_btn = false;
        },
        closeReply() {
            this.reply = false;
            this.reply_btn = true;
        },
        getCommentForTicket() {
            const self = this;
            axios
                .get('/ticket-comments', {
                    params: { ticket_id: self.ticket_id },
                })
                .then(function(response) {
                    self.comments = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
