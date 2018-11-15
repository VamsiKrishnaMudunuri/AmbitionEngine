<div class="item guest" data-id="{{$guest->getKey()}}">

        <div class="details has-menu">
                <div class="time">
                   <div>{{ CLDR::showDateTime( $guest->schedule, config('app.datetime.datetime.format')), $property->timezone }}</div>
                </div>

                <div class="name">
                    {{Html::linkRouteWithIcon(null, $guest->name, null, array(), array('title' => $guest->name, 'class' => 'view-detail', 'data-url' => URL::route('admin::managing::property::view-guest', array('property_id' => $property->getKey(), $guest->getKeyName() => $guest->getKey()))))}}
                </div>
                <div class="message">
                    <!--{{$guest->remark}}-->
                </div>
        </div>
        <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $guest->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                        @if($isWrite)
                                <li>
                                        {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit', 'data-inline-loading' => sprintf('menu-%s', $guest->getKey()), 'data-id' => $guest->getKey(), 'data-url' => URL::route('admin::managing::property::edit-guest', array('property_id' => $property->getKey(), $guest->getKeyName() => $guest->getKey()))))}}
                                </li>

                        @endif
                        @if($isDelete)
                                <li>
                                        {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $guest->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete this guest visit. Are you sure?', 'You are about to delete this guest visit. Are you sure?'), 'data-id' => $guest->getKey(), 'data-url' => URL::route('admin::managing::property::post-delete-guest', array('property_id' => $property->getKey(), $guest->getKeyName() => $guest->getKey()))))}}
                                </li>
                        @endif

                </ul>
        </div>
</div>
