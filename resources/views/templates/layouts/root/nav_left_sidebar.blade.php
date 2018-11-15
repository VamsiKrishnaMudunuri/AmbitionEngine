<div class="nav-left-sidebar root">
    <ul>

        @if(Auth::check())

            <li class="photo bright">
                <div class="photo-frame md">
                    <a href="{{URL::current()}}">
                        {{Html::skin('logo.png')}}
                    </a>
                </div>
            </li>

            @can(Utility::rights('root.slug'), \App\Models\Root::class)

                <li>
                    {{Html::linkRoute('root::module::index', Translator::transSmart("app.Modules", "Modules"), [], ['title' => Translator::transSmart("app.Modules", "Modules")], true)}}
                </li>

            @endcan

        @endif

    </ul>
</div>
