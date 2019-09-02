<template>
    <v-layout wrap align-center justify-center row fill-height>
        <v-flex xs12 sm10 md10>
            <v-card elevation="3">
                <v-layout row>
                    <v-flex xs12 sm12 md12>
                        <v-card-title primary-title>
                            <div>
                                <div class="headline">{{ data.name }}</div>
                                <div>
                                    <v-tooltip top>
                                        <template slot="activator">
                                            <v-icon>person</v-icon>
                                            {{ data.guardian_name }}
                                        </template>

                                        <span>{{ trans('messages.guardian_name') }}</span>
                                    </v-tooltip>
                                </div>
                                <div>
                                    <v-tooltip top>
                                        <template slot="activator">
                                            <v-icon>cake</v-icon>
                                            {{ data.birth_date | formatDate }}
                                        </template>

                                        <span>{{ trans('messages.date_of_birth') }}</span>
                                    </v-tooltip>
                                </div>
                                <div>
                                    <v-tooltip top>
                                        <template slot="activator">
                                            <v-icon>radio_button_checked</v-icon>
                                            {{ trans('messages.' + data.gender) }}
                                        </template>

                                        <span>{{ trans('messages.gender') }}</span>
                                    </v-tooltip>
                                </div>
                            </div>
                            <v-spacer></v-spacer>
                            <v-img
                                v-if="data.avatar_url"
                                :src="data.avatar_url"
                                height="125px"
                                contain
                            ></v-img>
                        </v-card-title>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <v-card-text>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>mobile_friendly</v-icon>
                                    {{ data.mobile }}
                                </template>

                                <span>{{ trans('messages.mobile') }}</span>
                            </v-tooltip>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>mobile_friendly</v-icon>
                                    {{ data.alternate_num }}
                                </template>

                                <span>{{ trans('messages.alternate_num') }}</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>email</v-icon>
                                    {{ data.email }}
                                </template>

                                <span>{{ trans('messages.email') }}</span>
                            </v-tooltip>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>web</v-icon>
                                    {{ data.skype }}
                                </template>

                                <span>{{ trans('messages.skype') }}</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>public</v-icon>
                                    {{ data.twitter }}
                                </template>

                                <span>{{ trans('messages.twitter') }}</span>
                            </v-tooltip>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>public</v-icon>
                                    {{ data.linkedin }}
                                </template>

                                <span>{{ trans('messages.linkedin') }}</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>public</v-icon>
                                    {{ data.facebook }}
                                </template>

                                <span>{{ trans('messages.facebook') }}</span>
                            </v-tooltip>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>location_on</v-icon>
                                    {{ data.current_address }}
                                </template>

                                <span>{{ trans('messages.current_address') }}</span>
                            </v-tooltip>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-tooltip top>
                                <template slot="activator">
                                    <v-icon>location_on</v-icon>
                                    {{ data.home_address }}
                                </template>

                                <span>{{ trans('messages.home_address') }}</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        v-if="$can('profile.edit')"
                        block
                        outline
                        color="accent"
                        dark
                        @click="$router.push({ name: 'profile.edit', params: { id: data.id } })"
                    >
                        <v-icon>edit</v-icon>
                        {{ trans('messages.edit') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
export default {
    data() {
        return {
            data: [],
            profileImg: [],
        };
    },
    created() {
        const self = this;
        self.getUserProfile();
    },
    methods: {
        getUserProfile() {
            const self = this;
            axios
                .get('/manage-profiles')
                .then(function(response) {
                    self.data = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
