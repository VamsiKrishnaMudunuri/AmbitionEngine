<li class="col-xs-12 col-sm-6 col-md-4 col-lg-3" data-id="{{$sandbox->getKey()}}">

    <div class="item">
        <div class="avatar">
            <a href="javascript:void(0);">
                {{ $sandbox::s3()->link($sandbox, $property, $sandboxConfig, $sandboxDimension)}}
            </a>
        </div>
        <div class="tool">


            @if($acls[Utility::rights('write.slug')])


                    {{
                         Html::linkRouteWithIcon(
                           null,
                           null,
                          'fa-pencil fa-fw fa-lg',
                          [],
                          [
                          'title' => Translator::transSmart('app.Edit', 'Edit'),
                          'class' => 'edit-photo',
                          'data-url' => $actions[Utility::rights('write.slug')]
                          ]
                         )

                    }}

            @endif

            <span class="dropdown">
                {{
                   Html::linkRouteWithIcon(
                     null,
                     null,
                    'fa-link fa-fw fa-lg',
                    [],
                    [
                    'title' => Translator::transSmart('app.Copy Link', 'Copy Link'),
                     'data-toggle' => 'dropdown',
                    ]
                   )

                }}

                <ul class="dropdown-menu">
                    <li>
                        {{

                            Html::linkRouteWithIcon(
                                 null,
                                  Translator::transSmart('app.Small', 'Small'),
                                null,
                                [],
                                [
                                'title' => Translator::transSmart('app.Copy Small Image', 'Copy Small Image'),
                                'class' => 'copy-link',
                                'data-absolute-url' => $sandbox::s3()->link($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.sm.slug'), array(), null, true)
                                ]
                               )

                        }}
                    </li>
                    <li>
                        {{

                            Html::linkRouteWithIcon(
                                 null,
                                  Translator::transSmart('app.Medium', 'Medium'),
                                null,
                                [],
                                [
                                'title' => Translator::transSmart('app.Copy Medium Image', 'Copy Medium Image'),
                                'class' => 'copy-link',
                                'data-absolute-url' => $sandbox::s3()->link($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.md.slug'), array(), null, true)
                                ]
                               )

                        }}
                    </li>
                    <li>
                        {{

                            Html::linkRouteWithIcon(
                                 null,
                                  Translator::transSmart('app.Large', 'Large'),
                                null,
                                [],
                                [
                                'title' => Translator::transSmart('app.Copy Large Image', 'Copy Large Image'),
                                'class' => 'copy-link',
                                'data-absolute-url' => $sandbox::s3()->link($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.lg.slug'), array(), null, true)
                                ]
                               )

                        }}
                    </li>
                    <li>
                        {{

                            Html::linkRouteWithIcon(
                                 null,
                                  Translator::transSmart('app.Original', 'Original'),
                                null,
                                [],
                                [
                                'title' => Translator::transSmart('app.Copy Original Image', 'Copy Original Image'),
                                'class' => 'copy-link',
                                'data-absolute-url' => $sandbox::s3()->link($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.standard.slug'), array(), null, true)
                                ]
                               )

                        }}
                    </li>
                </ul>
            </span>
            <span class="dropdown">
                {{
                  Html::linkRouteWithIcon(
                    null,
                    null,
                   'fa-cloud-download fa-fw fa-lg',
                   [],
                   [
                   'title' => Translator::transSmart('app.Download', 'Download'),
                    'data-toggle' => 'dropdown'
                   ]
                  )

               }}

                <ul class="dropdown-menu">
                    <li>

                        {{
                            $sandbox::s3()->downloadLink($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.sm.slug'), Translator::transSmart('app.Small', 'Small'))
                        }}

                    </li>
                    <li>

                        {{
                            $sandbox::s3()->downloadLink($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.md.slug'), Translator::transSmart('app.Medium', 'Medium'))
                        }}

                    </li>
                    <li>

                        {{
                            $sandbox::s3()->downloadLink($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.lg.slug'), Translator::transSmart('app.Large', 'Large'))
                        }}

                    </li>
                    <li>

                        {{
                            $sandbox::s3()->downloadLink($sandbox, $property, $sandboxConfig, \Illuminate\Support\Arr::get($sandboxConfig, 'dimension.standard.slug'), Translator::transSmart('app.Original', 'Original'))
                        }}

                    </li>
                </ul>
            </span>
            @if($acls[Utility::rights('delete.slug')])

                    {{
                         Html::linkRouteWithIcon(
                          null,
                          null,
                          'fa-trash fa-fw fa-lg',
                          [],
                          [
                          'title' => Translator::transSmart('app.Delete', 'Delete'),
                          'class' => 'delete-photo',
                           'data-confirm-message' => Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?'),
                           'data-url' => $actions[Utility::rights('delete.slug')]
                          ]
                         )
                     }}

            @endif





        </div>

        <div class="content">
            <div class="name">{{$sandbox->title}}</div>
            <div class="description">{{$sandbox->description}}</div>
        </div>

    </div>

</li>