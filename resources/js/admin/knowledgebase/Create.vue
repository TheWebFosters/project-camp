<template>
    <div class="component-wrap">
        <!-- category Add -->
        <CategoryAdd ref="categoryAdd"></CategoryAdd>
        <v-card>
            <v-card-title>
                <v-icon>ballot</v-icon>
                <span class="headline"> {{ trans('messages.create_knowladge_base') }} </span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 md5>
                            <v-text-field
                                v-model="form_fields.title"
                                :label="trans('messages.title')"
                                v-validate="'required'"
                                data-vv-name="title"
                                :data-vv-as="trans('messages.title')"
                                :error-messages="errors.collect('title')"
                                required
                            >
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12 md5>
                            <v-autocomplete
                                item-text="name"
                                item-value="id"
                                :items="categories"
                                v-model="category_id"
                                multiple
                                :label="trans('messages.category')"
                                v-validate="'required'"
                                data-vv-name="category"
                                :data-vv-as="trans('messages.category')"
                                :error-messages="errors.collect('category')"
                                required
                            ></v-autocomplete>
                        </v-flex>
                        <v-flex md2>
                            <v-btn @click="createCategory" small color="primary" fab dark>
                                <v-icon>add</v-icon>
                            </v-btn>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 sm12 md12>
                            {{ trans('messages.description') }}
                            <quill-editor v-model="form_fields.description"> </quill-editor>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 md12>
                            <div class="dropzone" id="fileUpload"></div>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 sm6>
                            <v-switch
                                :label="trans('messages.is_active')"
                                v-model="form_fields.is_active"
                            ></v-switch>
                        </v-flex>
                        <v-flex xs12 md6>
                            <v-checkbox
                                :label="trans('messages.show_to_employee')"
                                color="primary"
                                value="1"
                                v-model="form_fields.show_to_employee"
                                hide-details
                            ></v-checkbox>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                    {{ trans('messages.save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
import CategoryAdd from '../../common/projects/category/Add';
export default {
    components: {
        CategoryAdd,
    },
    data() {
        return {
            form_fields: [],
            loading: false,
            categories: [],
            category_id: [],
            dropzone: null,
        };
    },
    created() {
        this.create();
        this.$eventBus.$on('updateCategoryList', data => {
            this.categories.push(data);
            this.category_id.push(data.id);
        });
    },
    beforeDestroy() {
        this.$eventBus.$off('updateCategoryList');
    },
    methods: {
        create() {
            const self = this;
            axios
                .get('/admin/knowledge-bases/create')
                .then(function(response) {
                    self.categories = response.data;
                    self.form_fields.medias = [];
                    self.initDropzone();
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        createCategory() {
            var data = { type: 'knowledge_base' };
            this.$refs.categoryAdd.create(data);
        },
        initDropzone() {
            const self = this;
            if (self.dropzone) {
                self.dropzone.destroy();
            }
            self.dropzone = new Dropzone('div#fileUpload', {
                url: APP.APP_URL + '/media',
                paramName: 'file',
                maxFilesize: APP.UPLOAD_FILE_MAX_SIZE,
                uploadMultiple: true,
                dictDefaultMessage: self.trans('messages.drop_document_here'),
                headers: { 'X-CSRF-TOKEN': _token },
                autoProcessQueue: true,
                success: function(file, response) {
                    if (response.success == true) {
                        self.form_fields.medias.push(response.file_name);
                    }
                },
            });
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, [
                'title',
                'description',
                'medias',
                'is_active',
                'show_to_employee',
            ]);
            data.category_id = self.category_id;
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/admin/knowledge-bases', data)
                        .then(function(response) {
                            self.$validator.reset();
                            self.form_fields = [];
                            self.category_id = [];
                            self.loading = false;
                            self.create();
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.goBack();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
    },
};
</script>

<style>
.ql-container {
    min-height: 250px !important;
}
</style>
