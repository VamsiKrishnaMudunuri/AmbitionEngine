@extends('layouts.page')
@section('title', Translator::transSmart('app.Career', 'Career'))

@section('styles')
    @parent

@endsection

@section('scripts')
    @parent

@endsection

@section('top_banner_image_url', URL::skin('packages/hot-desk/banner.jpg'))  <!-- need to change after this -->

@section('top_banner')
    <div class="text-left">
        <h2 class="text-yellow f-1-em f-w-700">{{ Translator::transSmart('app.Careers at Common Ground', 'Careers at Common Ground') }}</h2>
        <p class="f-14">
            {{ Translator::transSmart("app.Lorem ipsum dolor sit amet, consectetur adipisicing elit", "Lorem ipsum dolor sit amet, consectetur adipisicing elit") }}
            <br/>
            {{ Translator::transSmart("app.and lifestyle centered around business success.", "and lifestyle centered around business success.") }}
        </p>
    </div>
@endsection

@section('top_banner_message_box_class', 'd-flex align-content-center')

@section('full-width-section')
    <div class="page-careers">
        <section class="section" style="background-color: rgb(255, 238, 203);">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6"
                         style="background-image: url({{ asset('images/choose-us/coworking.jpg') }}); background-position: center">
                        <div class="image-frame">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mission-description">
                            <div class="page-header b-b-none">
                                <h3>
                                    <b>
                                        {{Translator::transSmart("app.Lorem Ipsum title to be replaced", "Lorem Ipsum title to be replaced")}}
                                    </b>
                                </h3>
                                <p>
                                    {{ Translator::transSmart("app.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.") }}
                                </p>

                                <p>
                                    {{ Translator::transSmart("app.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.") }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        </section>
        <section class="section" style="background-color: rgb(255, 238, 203);">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="mission-description">
                            <div class="page-header b-b-none">
                                <h3>
                                    <b>
                                        {{Translator::transSmart("app.Lorem Ipsum title to be replaced", "Lorem Ipsum title to be replaced")}}
                                    </b>
                                </h3>
                                <p>
                                    {{ Translator::transSmart("app.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad aliquam beatae dolor dolorum est ex explicabo mollitia nemo odio quibusdam quidem quo ratione recusandae sint sit, sunt totam velit?.") }}
                            </p>

                            {{
                                Html::linkRoute('page::career::job::index', Translator::transSmart("app.View Open Positions", "View Open Positions"), [], ['class' => 'btn btn-green m-t-20', 'title' => Translator::transSmart("app.View Open Positions", "View Open Positions")])
                            }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"
                         style="background-image: url({{ asset('images/choose-us/coworking.jpg') }}); background-position: center">
                        <div class="image-frame">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection