<template>
    <div class="component-wrap">
        <v-card elevation="3">
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_backups') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    :loading="loading"
                    :disabled="loading"
                    color="blue-grey"
                    class="primary lighten-1"
                    @click="createBackup"
                >
                    {{ trans('messages.create_new_backup') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-list two-line subheader v-if="backups.length">
                    <template v-for="(backup, index) in backups">
                        <v-list-tile avatar :key="index" @click="">
                            <v-list-tile-avatar>
                                <v-icon>backup</v-icon>
                            </v-list-tile-avatar>

                            <v-list-tile-content>
                                <v-list-tile-title>{{ backup.file_name }}</v-list-tile-title>
                                <v-list-tile-sub-title>
                                    {{ $appFormatters.formatByteToMB(backup.file_size) }}
                                </v-list-tile-sub-title>
                                <v-list-tile-sub-title>
                                    {{
                                        $appFormatters.timeFromNowForUnixTimeStamp(
                                            backup.last_modified
                                        )
                                    }}
                                </v-list-tile-sub-title>
                            </v-list-tile-content>

                            <v-list-tile-action>
                                <v-btn icon ripple :href="backup.download_link">
                                    <v-icon color="grey lighten-1">
                                        file_download
                                    </v-icon>
                                </v-btn>
                                <v-btn icon ripple @click="deleteBackup(backup.file_name)">
                                    <v-icon color="grey lighten-1">
                                        delete_outline
                                    </v-icon>
                                </v-btn>
                            </v-list-tile-action>
                        </v-list-tile>
                        <v-divider dark></v-divider>
                    </template>
                </v-list>
                <v-layout row wrap v-else>
                    <v-flex xs12 sm12>
                        <div>
                            <v-alert :value="true" color="info" outline>
                                {{ trans('messages.no_records_found') }}
                            </v-alert>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    data() {
        return {
            backups: [],
            loading: false,
        };
    },
    created() {
        const self = this;
        self.getBackupFromApi();
    },
    methods: {
        createBackup() {
            const self = this;
            self.loading = true;
            axios
                .get('/admin/backups/create')
                .then(function(response) {
                    self.getBackupFromApi();

                    self.$store.commit('showSnackbar', {
                        message: response.data.msg,
                        color: response.data.success,
                    });
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        getBackupFromApi() {
            const self = this;
            axios
                .get('/admin/backups')
                .then(function(response) {
                    self.backups = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        deleteBackup(file_name) {
            const self = this;

            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .get('/admin/backups/delete/' + file_name)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getBackupFromApi();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                cancelCb: () => {
                    console.log('CANCEL');
                },
            });
        },
    },
};
</script>
