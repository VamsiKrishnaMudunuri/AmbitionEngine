@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/header.js') }}
@endsection

<div class="managing-menu">
    <div class="row">

        <div class="col-sm-12">
            <ul class="tabs">
                    <li>
                        @php
                            $dashboard = Translator::transSmart('app.Dashboard', 'Dashboard');
                        @endphp
                        {{Html::linkRoute('admin::managing::property::index', $dashboard, ['property_id' => $property->getKey()], ['title' => $dashboard])}}
                    </li>
                    <li>
                            {{Html::linkRoute('admin::managing::lead::index', Translator::transSmart("app.Leads", "Leads"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Leads", "Leads")])}}
                    </li>
                    <li>
                            {{Html::linkRoute('admin::managing::subscription::index', Translator::transSmart("app.Subscriptions", "Subscriptions"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Subscriptions", "Subscriptions")])}}
                    </li>

                    <li>
                            {{Html::linkRoute('admin::managing::reservation::index', Translator::transSmart("app.Bookings", "Bookings"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Bookings", "Bookings")])}}
                    </li>

                    <li>
                            {{Html::linkRoute('admin::managing::member::index', Translator::transSmart("app.Members", "Members"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Members", "Members")])}}
                    </li>
                    <li>
                            {{Html::linkRoute('admin::managing::staff::index', Translator::transSmart("app.Staff", "Staff"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Staff", "Staff")])}}
                    </li>

                    <li class="dropdown">


                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">

                                    <span class="name">{{Translator::transSmart('app.Reports', 'Reports')}}</span>

                                    <span class="caret"></span>

                            </a>

                            <ul class="dropdown-menu">
                                    <li>
                                            {{Html::linkRoute('admin::managing::report::finance::salesoverview::occupancy', Translator::transSmart("app.Sales Overview", "Sales Overview"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Sales Overview", "Sales Overview")])}}
                                    </li>
                                    <li>
                                            {{Html::linkRoute('admin::managing::report::finance::subscription::invoice', Translator::transSmart("app.Subscription Invoice", "Subscription Invoice"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Subscription Invoice", "Subscription Invoice")])}}
                                    </li>
                                    <li>
                                            {{Html::linkRoute('admin::managing::report::reservation::room::listing', Translator::transSmart("app.Meeting Room", "Meeting Room"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Meeting Room", "Meeting Room")])}}
                                    </li>
                            </ul>


                    </li>

                    <li>
                            {{Html::linkRoute('admin::managing::package::index', Translator::transSmart("app.Prime Package", "Prime Package"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Prime Package", "Prime Package")])}}
                    </li>

                    <li>
                            {{Html::linkRoute('admin::managing::facility::item::index', Translator::transSmart("app.Facilities", "Facilities"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Facilities", "Facilities")])}}
                    </li>
                    <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">

                                    <span class="name">{{Translator::transSmart('app.Files', 'Files')}}</span>

                                    <span class="caret"></span>

                            </a>

                            <ul class="dropdown-menu">
                                    <li>
                                            {{Html::linkRoute('admin::managing::file::agreement::index', Translator::transSmart("app.Agreements", "Agreements"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Agreements", "Agreements")])}}
                                    </li>
                                    <li>
                                            {{Html::linkRoute('admin::managing::file::manual::index', Translator::transSmart("app.Manuals", "Manuals"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Manuals", "Manuals")])}}
                                    </li>
                            </ul>


                    </li>
                    <li>
                        {{Html::linkRoute('admin::managing::property::page', Translator::transSmart("app.Page", "Page"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Page", "Page")])}}
                    </li>
                    <li>
                        {{Html::linkRoute('admin::managing::image::index', Translator::transSmart("app.Images", "Images"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Images", "Images")])}}
                    </li>
                    <li>
                        {{Html::linkRoute('admin::managing::gallery::index', Translator::transSmart("app.Galleries", "Galleries"), ['property_id' => $property->getKey()], ['title' => Translator::transSmart("app.Galleries", "Galleries")])}}
                    </li>




            </ul>

        </div>

    </div>
</div>
