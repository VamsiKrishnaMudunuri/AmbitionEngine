<div class="item event" data-id="{{$post->getKey()}}">
        <div class="profile-photo">
                <div class="frame">
                        <a href="javascript:void(0);">

                                @php
                                        $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
                                        $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                @endphp

                                {{ \App\Models\Sandbox::s3()->link((!$post->galleriesSandboxWithQuery->isEmpty()) ? $post->galleriesSandboxWithQuery->first() : $sandbox, $post, $config, $dimension)}}

                        </a>
                </div>
        </div>
        <div class="details has-menu">
                <div class="time">
                        @php
                                $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
                                $start_date = CLDR::showDate($post->start->setTimezone($post->timezone), config('app.datetime.date.format'));
                                $end_date = CLDR::showDate($post->end->setTimezone($post->timezone), config('app.datetime.date.format'));
                                $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
                                $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
                                $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);
                                if(config('features.admin.event.timezone')){
                                    $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);
                                }else{
                                   $time = Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_time, $end_time), false, ['start_date' => $start_time, 'end_date' => $end_time]);
                                }
                        @endphp
                        <div>
                                {{$date}}
                        </div>
                        <div>
                                {{$time}}
                        </div>
                </div>
                <div class="place">
                        @php
                                $location = '';

                                if($post->hostWithQuery){
                                    $location = $post->hostWithQuery->name_or_address;
                                }
                        @endphp
                        <a href="javascript:void(0);" title="{{$location}}">
                        <span>
                            <i class="fa fa-map-marker fa-lg"></i>
                        </span>
                                <span>

                              {{$location}}
                        </span>
                        </a>

                </div>
                <div class="name">
                        {{Html::linkRoute('member::event::event', $post->name, array($post->getKeyName() => $post->getKey()), array('title' => $post->name, 'target' => '_blank'))}}
                </div>
                <div class="message">
                        <!--{{$post->message}}-->
                </div>
        </div>
        <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $post->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                        @if($isWrite)
                                <li>
                                        {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()), 'data-id' => $post->getKey(), 'data-url' => URL::route('admin::managing::property::edit-event', array('property_id' => $property->getKey(), $post->getKeyName() => $post->getKey()))))}}
                                </li>

                        @endif
                        @if($isDelete)
                                <li>
                                        {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete this event. Are you sure?', 'You are about to delete this event. Are you sure?'), 'data-id' => $post->getKey(), 'data-url' => URL::route('admin::managing::property::post-delete-event', array('property_id' => $property->getKey(), $post->getKeyName() => $post->getKey()))))}}
                                </li>
                        @endif

                        @if($isWrite || $isDelete)
                                <li role="separator" class="divider"></li>
                        @endif

                        @if($isWrite)
                                <li>
                                        {{Html::linkRoute(null, Translator::transSmart('app.Invite', 'Invite'), array(), array('class' => 'invite', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()),  'data-id' => $post->getKey(), 'data-url' => URL::route('admin::managing::property::invite-event', array('property_id' => $property->getKey(), $post->getKeyName() => $post->getKey()))))}}
                                </li>
                        @endif


</ul>
</div>
</div>
