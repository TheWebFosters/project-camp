<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @if(!empty($favicon))
        <link rel="shortcut icon" href="{{asset('uploads/' . $favicon)}}"/>
    @endif
    <!-- Styles -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <!-- app js values -->
    <script type="application/javascript">
        var APP = {};
        APP.APP_URL = '{{config('app.url')}}';
        APP.TIMEZONE = '{{config('app.timezone')}}';
        APP.NOTIFICATION_REFRESH_TIMEOUT = '{{config('constants.notification_refresh_timeout')}}';
        APP.UPLOAD_FILE_MAX_SIZE = '{{config('constants.upload_file_max_size')}}';
        APP.RTL = @if(in_array(config('app.locale'), config('constants.langs_rtl'))) true @else false @endif;
        APP.FIRST_DAY_OF_WEEK = '{{$first_day_of_week}}';
        @auth
            @php
                $user = Auth::user();
            @endphp

            APP.USER_PERMISSIONS = {!! json_encode($user->getUserPermissions($user), true) !!};
            APP.USER_ROLES = {!! json_encode($user->getUserRoles($user), true) !!};
            APP.DATE_FORMAT = {!! json_encode($user->appDateFormat()) !!};
            APP.TIME_FORMAT = {!! json_encode($user->appTimeFormat()) !!};
        @else
            @php
                $user = null;
            @endphp
            APP.USER_PERMISSIONS = [];
            APP.USER_ROLES = [];
        @endauth
    </script>
</head>
<body>
<div id="admin">

    <template>
        <v-app id="inspire">
            <v-navigation-drawer
                class="blue-grey lighten-5"
                app
                fixed
                width="232"
                v-model="drawer"
                clipped
                mobile-break-point="400"
                >
                <v-list expand dense>

                    @foreach($nav as $n)
                        @if($n->navType==\App\Components\Core\Menu\MenuItem::$NAV_TYPE_NAV)
                            <v-list-tile :to="{name:'{{$n->routeName}}'}" :exact="false">
                                <v-list-tile-action>
                                    <v-icon>{{$n->icon}}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{$n->label}}
                                    </v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        @elseif($n->navType==\App\Components\Core\Menu\MenuItem::$NAV_TYPE_PARENT)
                            <v-list-group
                                prepend-icon="{{$n->icon}}"
                              > 
                                <template slot="activator">
                                    <v-list-tile>
                                            <v-list-tile-title>
                                                {{$n->label}}
                                            </v-list-tile-title>
                                    </v-list-tile>
                                </template>
                                @foreach($n->children as $sub_menu)
                                    <v-list-tile :to="{name:'{{$sub_menu['route_name']}}'}"
                                        :exact="false">
                                        <v-list-tile-action>
                                            <v-icon>{{$sub_menu['icon']}}</v-icon>
                                        </v-list-tile-action>

                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{$sub_menu['label']}}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                @endforeach
                            </v-list-group>
                        @else
                            <v-divider></v-divider>
                        @endif
                    @endforeach
                </v-list>
            </v-navigation-drawer>

            <v-toolbar class="primary" app dark flat fixed dense height="56"
                :clipped-left="true">
                <v-toolbar-side-icon @click="drawerToggle"></v-toolbar-side-icon>
                <v-toolbar-title class="hidden-sm-and-down">{{config('app.name')}}</v-toolbar-title>

                <v-layout align-center justify-end row fill-height/>
                    <quick-add-button></quick-add-button>
                    
                    <calendar></calendar>
                    
                    <notification></notification>

                    <v-menu 
                        attach
                        offset-y
                        bottom
                        left
                        nudge-bottom="14"
                        transition="slide-x-transition"
                        >
                        <v-btn flat slot="activator">
                            <avatar :members="{{ json_encode([0 => $user->toArray()]) }}" :tooltip="true">
                            </avatar>&nbsp;{{ $user->name }}
                            <v-icon dark medium>more_vert</v-icon>
                        </v-btn>

                        <v-list>
                            <v-list-tile @click="$router.push({ name: 'profile.list' })">
                                <v-list-tile-title>
                                    <v-icon> account_circle </v-icon>
                                    {{trans('messages.profile')}}
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="clickLogout('{{route('logout')}}','{{url('/login')}}')">
                                <v-list-tile-title>
                                    <v-icon> directions_walk </v-icon>
                                    {{trans('messages.logout')}}
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-layout>
            </v-toolbar>
            
            <v-content>
                <transition name="fade">
                    <router-view></router-view>
                </transition>
            </v-content>
            
            <div v-scroll="onScroll">
                <v-footer app
                    v-show="toggleFooter">
                    <span>
                        @lang('messages.application_copyright',[
                            'name' => config('app.name', 'Laravel'),
                            'version' => config('author.app_version'),
                            'year' => date('Y')
                        ])
                    </span>
                </v-footer>
            </div>
        </v-app>

        <!-- loader -->
        <div v-if="showLoader" class="wask_loader bg_half_transparent">
            <moon-loader color="red"></moon-loader>
        </div>

        <!-- snackbar -->
        <v-snackbar
                :timeout="snackbarDuration"
                :color="snackbarColor"
                top
                right
                v-model="showSnackbar">
            @{{ snackbarMessage }}
        </v-snackbar>

        <!-- dialog confirm -->
        <v-dialog v-show="showDialog" v-model="showDialog" lazy absolute max-width="450px">
            <v-btn color="primary" slot="activator">Open Dialog</v-btn>
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon v-if="dialogIcon" medium>@{{dialogIcon}}</v-icon> @{{ dialogTitle }}</div>
                </v-card-title>
                <v-card-text>@{{ dialogMessage }}</v-card-text>
                <v-card-actions v-if="dialogType=='confirm'">
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat="flat" @click.native="dialogCancel">
                        @lang('messages.cancel')
                    </v-btn>
                    <v-btn color="green darken-1" flat="flat" @click.native="dialogOk">
                        @lang('messages.ok')
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </template>

</div>

    <!-- Scripts -->
    <script src="{{ env('APP_URL') . '/js/lang.js' }}"></script>
    <script src="{{ asset(mix('js/manifest.js')) }}"></script>
    <script src="{{ asset(mix('js/vendor.js')) }}"></script>

    @if($user->is_employee)
        <script src="{{ asset(mix('js/admin.js')) }}"></script>
    @elseif($user->is_client)
        <script src="{{ asset(mix('js/client.js')) }}"></script>
    @endif
</body>
<style type="text/css">
    /* quill editor toolbar */
.ql-toolbar {
  background-color: white;
}
.ql-container {
    min-height: 200px;
    font-size: 15px;
    overflow-y: scroll;
    resize: vertical;
}
</style>
</html>