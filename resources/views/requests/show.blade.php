@extends('layouts.layout')

@section('title')
    {{__('Request Detail')}}
@endsection

@section('sidebar')
    @include('layouts.sidebar', ['sidebar' => Menu::get('sidebar_request')])
@endsection

@section('meta')
    <meta name="request-id" content="{{ $request->id }}">
@endsection

@section('breadcrumbs')
    @include('shared.breadcrumbs', ['routes' => [
        __('Requests') => route('requests.index'),
        $request->name . ' #'. $request->getKey() => null,
    ], 'dynamic' => true])
@endsection
@section('content')
    <div id="request" class="container-fluid px-3">
        <div class="d-flex flex-column flex-md-row">
            <div class="flex-grow-1">
                <div class="container-fluid">
                    <ul class="nav nav-tabs" id="requestTab" role="tablist">
                        <template v-if="status">
                            @if($request->status === 'ERROR')
                                <li class="nav-item">
                                    <a class="nav-link active" id="errors-tab" data-toggle="tab" href="#errors"
                                       role="tab" @click="switchTab('errors')"
                                       aria-controls="errors" aria-selected="false">{{__('Errors')}}</a>
                                </li>
                            @endif
                            <li class="nav-item" v-if="!showSummary">
                                <a class="nav-link" :class="{ active: activePending }" id="pending-tab"
                                   data-toggle="tab" @click="switchTab('pending')" href="#pending" role="tab"
                                   aria-controls="pending" aria-selected="true">{{__('Tasks')}}</a>
                            </li>
                            <li class="nav-item" v-if="showSummary">
                                <a id="summary-tab" data-toggle="tab" href="#summary" role="tab"
                                   aria-controls="summary" @click="switchTab('summary')" aria-selected="false"
                                   v-bind:class="{ 'nav-link':true, active: showSummary }">
                                    {{__('Summary')}}
                                </a>
                            </li>
                            @if ($request->status === 'COMPLETED' && !$request->errors)
                                @can('editData', $request)
                                    <li class="nav-item">
                                        <a id="editdata-tab" data-toggle="tab" href="#editdata" role="tab"
                                           aria-controls="editdata" aria-selected="false"
                                           class="nav-link"  @click="switchTab('editdata')">
                                            {{__('Data')}}
                                        </a>
                                    </li>
                                @endcan
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab"
                                   aria-controls="completed" aria-selected="false" @click="switchTab('completed')">{{__('Completed')}}</a>
                            </li>
                            @if(count($files) > 0 && !hasPackage('package-files'))
                                <li class="nav-item">
                                    <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab"
                                       aria-controls="files" aria-selected="false" @click="switchTab('files')">{{__('Files')}}</a>
                                </li>
                            @endif

                            <template v-for="{ tab } in packages">
                              <li class="nav-item">
                                  <a class="nav-link"
                                    :id="tab.id"
                                    data-toggle="tab"
                                    :href="'#' + tab.target"
                                    role="tab"
                                    @click="switchTab(tab.target)">
                                      @{{ tab.name }}
                                    </a>
                              </li>
                            </template>

                                <li class="nav-item" v-show="canViewPrint">
                                    <a class="nav-link" id="forms-tab" data-toggle="tab" href="#forms"
                                       role="tab" aria-controls="forms" aria-selected="false" @click="switchTab('forms')">
                                        {{__('Forms')}}
                                    </a>
                                </li>
                            @isset($addons)
                                @foreach ($addons as $addon)
                                    <li class="nav-item">
                                        <a class="nav-link" id="{{$addon['id'] . '-tab'}}" data-toggle="tab" href="{{'#' . $addon['id']}}"
                                        role="tab" aria-controls="{{ $addon['id'] }}" aria-selected="false">
                                            {{ __($addon['title']) }}
                                        </a>
                                    </li>
                                @endforeach
                            @endisset
                        </template>
                    </ul>
                    <div class="tab-content" id="requestTabContent">
                        <div class="tab-pane card card-body border-top-0 p-0" :class="{ active: activeErrors }" id="errors" role="tabpanel"
                             aria-labelledby="errors-tab">
                            <request-errors :errors="errorLogs"></request-errors>
                        </div>
                        <div class="tab-pane fade show card card-body border-top-0 p-0" :class="{ active: activePending }" id="pending" role="tabpanel"
                             aria-labelledby="pending-tab" v-if="!showSummary">
                            <request-detail ref="pending" :process-request-id="requestId" status="ACTIVE" :is-admin="{{Auth::user()->is_administrator ? 'true' : 'false'}}">
                            </request-detail>
                        </div>
                        <div class="card card-body border-top-0 p-0" v-bind:class="{ 'tab-pane':true, active: showSummary }" id="summary"
                             role="tabpanel" aria-labelledby="summary-tab">
                            <template v-if="showSummary">
                                <template v-if="showScreenSummary">
                                    <div class="p-3">
                                        <vue-form-renderer ref="screen" :config="screenSummary.config" v-model="dataSummary" :computed="screenSummary.computed"/>
                                    </div>
                                </template>
                                <template v-else>
                                    <template v-if="summary.length > 0">
                                        <div class="card border-0">
                                            <data-summary :summary="dataSummary"></data-summary>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="card border-0">
                                            <div class="card-header bg-white">
                                                <h5 class="m-0">
                                                    {{ __('No Data Found') }}
                                                </h5>
                                            </div>

                                            <div class="card-body">
                                                <p class="card-text">
                                                    {{ __("Sorry, this request doesn't contain any information.") }}
                                                </p>
                                            </div>
                                        </div>
                                    </template>

                                </template>
                            </template>
                        </div>
                        @if ($request->status === 'COMPLETED')
                            @can('editData', $request)
                                <div id="editdata" role="tabpanel" aria-labelledby="editdata" class="tab-pane card card-body border-top-0 p-3">
                                    @include('tasks.editdata')
                                </div>
                            @endcan
                        @endif
                        <div class="tab-pane fade card card-body border-top-0 p-0" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            <request-detail ref="completed" :process-request-id="requestId" status="CLOSED" :is-admin="{{Auth::user()->is_administrator ? 'true' : 'false'}}">
                            </request-detail>
                        </div>

                        <template v-for="{ tab, component } in packages">
                          <div class="tab-pane fade card card-body border-top-0 p-0" :id="tab.target" role="tabpanel">
                            <component :is="component" :process-request-id="requestId"></component>
                          </div>
                        </template>

                        <div class="tab-pane fade card card-body border-top-0 p-3" id="files" role="tabpanel" aria-labelledby="files-tab">
                            <div class="card">
                                <div>
                                    <table class="vuetable table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{__('File Name')}}</th>
                                            <th>{{__('MIME Type')}}</th>
                                            <th>{{__('Created At')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($files as $file)
                                            <tr>
                                                <td>
                                                    <a href="{{url('request/' .$request->id .'/files/' . $file->id)}}">{{$file->file_name}}</a>
                                                </td>
                                                <td>{{$file->mime_type}}</td>
                                                <td>{{ $file->created_at->format('m/d/y h:i a')}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade card card-body border-top-0 p-0" id="forms" role="tabpanel" aria-labelledby="forms-tab" v-show="canViewPrint">
                            <request-screens :id="requestId" :information="dataSummary" :screens="screenRequested" ref="forms">
                            </request-screens>
                        </div>

                        @isset($addons)
                            @foreach ($addons as $addon)
                                <div class="tab-pane fade show" id="{{$addon['id']}}" role="tabpanel" aria-labelledby="{{ $addon['id'] }}">
                                    {!! $addon['content'] !!}
                                </div>
                            @endforeach
                        @endisset
                    </div>
                </div>
                  @can('view-comments')
                    <timeline commentable_id="{{ $request->getKey() }}"
                              commentable_type="{{ get_class($request) }}"
                              :reactions="configurationComments.reactions"
                              :voting="configurationComments.voting"
                              :edit="configurationComments.edit"
                              :remove="configurationComments.remove"
                              :adding="configurationComments.comments"
                              :readonly="request.status === 'COMPLETED'"
                              />
                  @endcan
            </div>
            <div class="ml-md-3 mt-3 mt-md-0">
                <template v-if="statusLabel">
                    <div class="card">
                        <div :class="classStatusCard">
                            <h4 style="margin:0; padding:0; line-height:1">@{{ __(statusLabel) }}</h4>
                        </div>
                        <ul class="list-group list-group-flush w-100">
                            @if($request->user_id)
                            <li class="list-group-item">
                                <h5>{{__('Requested By')}}</h5>
                                <avatar-image v-if="userRequested" size="32"
                                              class="d-inline-flex pull-left align-items-center"
                                              :input-data="requestBy" display-name="true"></avatar-image>
                                <span v-if="!userRequested">{{__('Web Entry')}}</span>
                            </li>
                            @endif
                            @if($canCancel == true && $request->status === 'ACTIVE')
                                <template>
                                    <li class="list-group-item">
                                        <h5>{{__('Cancel Request')}}</h5>
                                        <button type="button" class="btn btn-outline-danger btn-block" @click="onCancel" aria-haspopup="dialog">
                                            <i class="fas fa-stop-circle"></i> {{__('Cancel')}}
                                        </button>
                                    </li>
                                </template>
                            @endif
                            @if($canManuallyComplete == true)
                                <li class="list-group-item">
                                    <h5>{{__('Manually Complete Request')}}</h5>
                                    <button type="button" class="btn btn-outline-success btn-block"
                                            data-toggle="modal" @click="completeRequest">
                                        <i class="fas fa-stop-circle"></i> {{__('Complete')}}
                                    </button>
                                </li>
                            @endif
                            @if($request->parentRequest)
                            <li class="list-group-item">
                              <h5>{{__('Parent Request')}}</h5>
                              <i :class="requestStatusClass('{{$request->parentRequest->status}}')"></i>
                            <a href="/requests/{{$request->parentRequest->getKey()}}">{{$request->parentRequest->name}}</a>
                            </li>
                            @endif
                            @if(count($request->childRequests))
                            <li class="list-group-item">
                              <h5>{{__('Child Requests')}}</h5>
                              @foreach($request->childRequests as $childRequest)
                              <div>
                              <i :class="requestStatusClass('{{$childRequest->status}}')"></i>
                              <a href="/requests/{{$childRequest->getKey()}}">{{$childRequest->name}}</a>
                              </div>
                              @endforeach
                            </li>
                            @endif
                            @if ($request->participants->count())
                            <li class="list-group-item">
                                <h5>{{__('Participants')}}</h5>
                                <avatar-image size="32" class="d-inline-flex pull-left align-items-center"
                                              :input-data="participants" hide-name="true"></avatar-image>
                            </li>
                            @endif
                            <li class="list-group-item">
                                <h5>@{{statusLabel}}</h5>
                                <i class="far fa-calendar-alt"></i>
                                <small>@{{ moment(statusDate).format() }}</small>
                                <br>

                            </li>
                        </ul>
                    </div>
                </template>
            </div>
        </div>
    </div>

@endsection

@section('js')

    @foreach($manager->getScripts() as $script)
        <script src="{{$script}}"></script>
    @endforeach

    @if(hasPackage('package-files'))
      <!-- TODO: Replace with script injector like we do for modeler and screen builder -->
      <script src="{{ mix('js/manager.js', 'vendor/processmaker/packages/package-files') }}"></script>
    @endif

    <script src="{{mix('js/requests/show.js')}}"></script>
    <script>
      new Vue({
        el: "#request",
        mixins: addons,
        data() {
          return {
            showCancelRequest: false,
            //Edit data
            fieldsToUpdate: [],
            jsonData: "",
            selectedData: '',
            monacoLargeOptions: {
              automaticLayout: true,
            },
            showJSONEditor: false,
            data: @json($request->getRequestData()),
            requestId: @json($request->getKey()),
            screenRequested: @json($screenRequested),
            request: @json($request),
            files: @json($files),
            refreshTasks: 0,
            canCancel: @json($canCancel),
            canViewPrint : @json($canPrintScreens),
            status: 'ACTIVE',
            userRequested: [],
            errorLogs: @json(['data'=>$request->errors]),
            disabled: false,
            packages: [],
            processId: @json($request->process->id),
            canViewComments: @json($canViewComments),
            configurationComments : {
              comments: false,
              reactions: false,
              edit: false,
              voting: false,
              remove: false,
            },
          };
        },
        computed: {
          activeErrors() {
            return this.request.status === 'ERROR';
          },
          activePending() {
            return this.request.status === 'ACTIVE';
          },
          /**
           * Get the list of participants in the request.
           *
           */
          participants() {
            return this.request.participants;
          },
          /**
           * Request Summary - that is blank place holder if there are in progress tasks,
           * if the request is completed it will show key value pairs.
           *
           */
          showSummary() {
            return this.request.status === 'COMPLETED' || this.request.status === 'CANCELED';
          },
          /**
           * If the screen summary is configured.
           **/
          showScreenSummary() {
            return this.request.summary_screen !== null
          },
          /**
           * Get the summary of the Request.
           *
           */
          summary() {
            return this.request.summary;
          },
          /**
           * Get Screen summary
           * */
          screenSummary() {
            return this.request.summary_screen;
          },
          /**
           * prepare data screen
           **/
          dataSummary() {
            let options = {};
            this.request.summary.forEach(option => {
              options[option.key] = option.value
            });
            return options;
          },
          classStatusCard() {
            let header = {
              "ACTIVE": "bg-success",
              "COMPLETED": "bg-secondary",
              "CANCELED": 'bg-danger',
              "ERROR": "bg-danger"
            };
            return 'card-header text-capitalize text-white ' + header[this.request.status.toUpperCase()];
          },
          labelDate() {
            let label = {
              "ACTIVE": 'Created',
              "COMPLETED": 'Completed On',
              "CANCELED": 'Canceled ',
              "ERROR": 'Failed On'
            };
            return label[this.request.status.toUpperCase()];
          },
          statusDate() {
            let status = {
              "ACTIVE": this.request.created_at,
              "COMPLETED": this.request.completed_at,
              "CANCELED": this.request.updated_at,
              "ERROR": this.request.updated_at
            };

            return status[this.request.status.toUpperCase()];
          },
          statusLabel() {
            let status = {
              "ACTIVE": "{{__('In Progress')}}",
              "COMPLETED": "{{__('Completed')}}",
              "CANCELED": "{{__('Canceled')}}",
              "ERROR": "{{__('Error')}}",
            };

            return status[this.request.status.toUpperCase()];
          },
          requestBy() {
            return [this.request.user]
          },
        },
        methods: {
          switchTab(tab) {
            ProcessMaker.EventBus.$emit('tab-switched', tab);  
          },
          requestStatusClass(status) {
            status = status.toLowerCase();
            let bubbleColor = {
              active: "text-success",
              inactive: "text-danger",
              error: "text-danger",
              draft: "text-warning",
              archived: "text-info",
              completed: "text-primary",
            };
            return 'fas fa-circle ' + bubbleColor[status] + ' small';
          },
          // Data editor
          updateRequestData() {
            const data = JSON.parse(this.jsonData);
            ProcessMaker.apiClient
              .put("requests/" + this.requestId, {
                data: data
              })
              .then(response => {
                this.fieldsToUpdate.splice(0);
                ProcessMaker.alert("{{__('The request data was saved.')}}", "success");
              });
          },
          saveJsonData() {
            try {
              const value = JSON.parse(this.jsonData);
              this.updateRequestData();
            } catch (e) {
              // Invalid data
            }
          },
          editJsonData() {
            this.jsonData = JSON.stringify(this.data, null, 4);
          },
          /**
           * Refresh the Request details.
           *
           */
          refreshRequest() {
            this.$refs.pending.fetch();
            this.$refs.completed.fetch();
            ProcessMaker.apiClient.get(`requests/${this.requestId}`, {
              params: {
                include: 'participants,user,summary,summaryScreen'
              }
            })
              .then((response) => {
                for (let attribute in response.data) {
                  this.updateModel(this.request, attribute, response.data[attribute]);
                }
                this.refreshTasks++;
              });
          },
          /**
           * Update a model property.
           *
           */
          updateModel(obj, prop, value, defaultValue) {
            const descriptor = Object.getOwnPropertyDescriptor(obj, prop);
            value = value !== undefined ? value : (descriptor ? obj[prop] : defaultValue);
            if (descriptor && !(descriptor.get instanceof Function)) {
              delete obj[prop];
              Vue.set(obj, prop, value);
            } else if (descriptor && obj[prop] !== value) {
              Vue.set(obj, prop, value);
            }
          },
          /**
           * Listen for Request updates.
           *
           */
          listenRequestUpdates() {
            let userId = document.head.querySelector('meta[name="user-id"]').content;
            Echo.private(`ProcessMaker.Models.User.${userId}`)
              .notification((token) => {
                if (token.request_id === this.requestId) {
                  this.refreshRequest();
                }
              });
          },
          /**
           * disable buttons in screen
           */
          cleanScreenButtons() {
            if (this.showScreenSummary) {
              this.$refs.screen.config[0].items.forEach(item => {
                item.config.disabled = true;
                if (item.component === 'FormButton') {
                  item.config.event = '';
                  item.config.variant = item.config.variant + '  disabled';
                }
              });
            }
          },
          okCancel() {
            //single click
            if (this.disabled) {
              return
            }
            this.disabled = true;
            ProcessMaker.apiClient.put(`requests/${this.requestId}`, {
              status: 'CANCELED'
            })
              .then(response => {
                ProcessMaker.alert(this.$t('The request was canceled.'), 'success');
                window.location.reload();
              })
              .catch(error => {
                this.disabled = false;
              });
          },
          onCancel() {
            ProcessMaker.confirmModal(
                this.$t("Caution!"),
                this.$t('Are you sure you want cancel this request?'),
                "",
                () => {
                  this.okCancel();
                }
            );
          },
          completeRequest() {
            ProcessMaker.confirmModal(
              this.$t("Caution!"),
              this.$t("Are you sure you want to complete this request?"),
              "",
              () => {
                ProcessMaker.apiClient.put(`requests/${this.requestId}`, {
                  status: 'COMPLETED'
                }).then(() => {
                  ProcessMaker.alert(this.$t('Request Completed'), 'success')
                  location.reload()
                })
              })
          },
          getConfigurationComments() {
            if (this.canViewComments) {
              const commentsPackage = 'comment-editor' in Vue.options.components;
              if (commentsPackage) {
                ProcessMaker.apiClient.get(`comments/configuration`, {
                  params: {
                    id: this.processId,
                    type: 'Process',
                  }
                })
                  .then(response => {
                    this.configurationComments.comments = !!response.data.comments;
                    this.configurationComments.reactions = !!response.data.reactions;
                    this.configurationComments.voting = !!response.data.voting;
                    this.configurationComments.edit = !!response.data.edit;
                    this.configurationComments.remove = !!response.data.remove;
                  });
              }
            }
          },
        },
        mounted() {
          this.getConfigurationComments();
          this.packages = window.ProcessMaker.requestShowPackages;
          this.listenRequestUpdates();
          this.cleanScreenButtons();
          this.editJsonData();
        },
      });
    </script>
@endsection
