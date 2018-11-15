<nav class="navbar navbar-inverse navbar-fixed-top auth">
    <div class="container">

        <div class="navbar-header">
            <a class="navbar-brand" href="{{Config::get('app.url')}}">
                {{Html::skin('logo.png')}}
            </a>
            <div class="navbar-menu">
                <ul class="nav navbar-nav navbar-left">

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <button type="button" class="navbar-toggle" data-toggle-direction="right">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>