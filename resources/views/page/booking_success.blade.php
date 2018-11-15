<div class="page-booking-success">
        <div class="row">
            <div class="col-xs-12 col-sm-12">

                <div class="image-frame">
                   <div class="layer"></div>
                   <div class="warm-message">
                       <h2>
                           {{Translator::transSmart("app.THANK YOU <br /> YOU'LL HEAR FROM US SOON!", "THANK YOU <br /> YOU'LL HEAR FROM US SOON!", true)}}
                       </h2>
                       <p>
                           {{Translator::transSmart("app.While waiting, check out a little more about Common Ground's services:", "While waiting, check out a little more about Common Ground's services:", true)}}
                       </p>
                       {{Html::linkRouteWithLRIcon('page::index', Translator::transSmart('app.CHOOSE US', 'CHOOSE US'), null, 'fa-fw fa-caret-right', ['slug' => 'choose-us'], ['title' => Translator::transSmart('app.CHOOSE US', 'CHOOSE US'), 'class' => 'btn btn-theme'])}}

                   </div>
                   {{Html::skin('booking/booking.jpg')}}
                </div>

            </div>

        </div>
    </div>


