@extends('layouts.page')
@section('title', Translator::transSmart('app.Choose Us', 'Choose Us'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/choose-us.css') }}
@endsection

@section('container', 'container-fluid')

@section('top_banner_image_url', URL::skin('choose-us/coworker.jpg'))

@section('top_banner')
    <div class="page-choose-us top-banner">
        <div class="box">
        </div>
        <div>
            <h2>
                {{Translator::transSmart("app.REDEFINING CONCEPT OF A WORKSPACE, WITH COMMUNITY AT IT’S CORE AND SUPPORT SERVICES FOR THE SUCCESS OF EVERY BUSINESS", "REDEFINING CONCEPT OF A WORKSPACE, WITH COMMUNITY AT IT’S CORE AND SUPPORT SERVICES FOR THE SUCCESS OF EVERY BUSINESS", true)}}
            </h2>
        </div>

    </div>
@endsection
@section('content')

    <div class="page-choose-us">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                   <div class="container">
                       <div class="row">
                           <div class="col-xs-12 col-sm-5">
                               <div>
                                   <h3>
                                       <b>
                                        {{Translator::transSmart("app.CONNECT, LIVE AND LEARN TOGETHER", "CONNECT, LIVE AND LEARN TOGETHER")}}
                                       </b>
                                   </h3>
                                   <br />
                                   <p>
                                       {{Translator::transSmart("app.When you join Common Ground, you join a team which shares a desire to succeed. We’re there with you and you can bet we’ll have your back.", "When you join Common Ground, you join a team which shares a desire to succeed. We’re there with you and you can bet we’ll have your back.") }}
                                   </p>
                                   <br />
                                   <p>
                                       {{Translator::transSmart("app.We know that we are stronger together than we are apart, so we’ve set the scene for collaboration to take place and we’ll keep you inspired along the way.", "We know that we are stronger together than we are apart, so we’ve set the scene for collaboration to take place and we’ll keep you inspired along the way.") }}
                                   </p>
                                   <br />
                                   <p>
                                       {{Translator::transSmart("app.Here’s what we have lined up the moment you sign up.", "Here’s what we have lined up the moment you sign up.") }}
                                   </p>
                                   <br />
                                   <br />
                                   {{Html::skin('choose-us/gathering.jpg', array('class' => 'img-responsive'))}}
                                   {{Html::skin('choose-us/meeting.jpg', array('class' => 'img-responsive'))}}
                               </div>
                           </div>
                           <div class="col-xs-12 col-sm-7">
                               <div>
                                    <table class="table event">
                                       <tr>

                                            <td><b>{{Translator::transSmart("app.CG APP", "CG APP")}}</b></td>
                                            <td>
                                                {{Translator::transSmart("app.The Common Ground App connects you with other members in real time. Get quick feedback on a problem or plan after hour drinks, the app is community - digitised.", "The Common Ground App connects you with other members in real time. Get quick feedback on a problem or plan after hour drinks, the app is community - digitised.")}}
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><b>{{Translator::transSmart("app.BUSINESS MASTERCLASS", "BUSINESS MASTERCLASS")}}</b></td>
                                            <td>
                                                {{Translator::transSmart("app.Business leaders from different industries share their knowledge, tips and tricks.", "Business leaders from different industries share their knowledge, tips and tricks.")}}
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><b>{{Translator::transSmart("app.CO. LAB", "CO. LAB")}}</b></td>
                                            <td>
                                                {{Translator::transSmart("app.We want to know you, so tell us your story and ideas, get to know other members and what they are up to.", "We want to know you, so tell us your story and ideas, get to know other members and what they are up to.")}}
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><b>{{Translator::transSmart("app.WORKSHOP", "WORKSHOP")}}</b></td>
                                            <td>
                                                {{Translator::transSmart("app.Learn new and exciting skills, from coding to cooking and everything in between!", "Learn new and exciting skills, from coding to cooking and everything in between!")}}
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><b>{{Translator::transSmart("app.LIFESTYLE", "LIFESTYLE")}}</b></td>
                                            <td>
                                                {{Translator::transSmart("app.Chill out and grab a drink with other members, chat and get inspired.", "Chill out and grab a drink with other members, chat and get inspired.")}}
                                            </td>
                                        </tr>
                                    </table>
                                   <div class="image-frame ambition">
                                   {{Html::skin('choose-us/ambition.png', array('class' => 'img-responsive'))}}
                                   </div>
                               </div>

                           </div>
                       </div>
                   </div>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5">
                            <div>
                                <h3>
                                    <b>
                                        {{Translator::transSmart("app.COMMON GROUND WORKSPACE", "COMMON GROUND WORKSPACE")}}
                                    </b>
                                </h3>
                                <br />
                                <p>
                                    {{Translator::transSmart("app.You’ll never look at an office the same way. We don’t want you to feel like you’re coming to work when you step into Common Ground. Our spaces are artistically designed to inspire creativity and innovation. We provide you with all the essentials you need and more.", "You’ll never look at an office the same way. We don’t want you to feel like you’re coming to work when you step into Common Ground. Our spaces are artistically designed to inspire creativity and innovation. We provide you with all the essentials you need and more.") }}
                                </p>
                                <br />
                                <p>
                                    {{Html::linkRouteWithLRIcon('page::index', Translator::transSmart("app.ABOUT OUR OFFERINGS", "ABOUT OUR OFFERINGS"), null, 'fa-fw fa-caret-right', ['slug' => 'packages'], ['title' => Translator::transSmart("app.ABOUT OUR OFFERINGS", "ABOUT OUR OFFERINGS"), 'class' => 'btn btn-green'])}}
                                </p>
                                <br />
                                <p>
                                    {{Html::skin('choose-us/office.jpg', array('class' => 'img-responsive'))}}
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-7">
                            <div>
                                <table class="table facility">
                                <tr>
                                    <td>
                                        <div class="image-frame">
                                            {{Html::skin('choose-us/modern.png', array('class' => 'img-responsive'))}}
                                        </div>
                                    </td>
                                    <td><b>{{Translator::transSmart("app.Modern, stylish and unique spaces", "Modern, stylish and unique spaces")}}</b></td>
                                    <td>{{Translator::transSmart("app.All Common Ground spaces are highly designed environments that balance style and professionalism. We make sure  each venue is where you need to be to impress.", "All Common Ground spaces are highly designed environments that balance style and professionalism. We make sure  each venue is where you need to be to impress.")}}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="image-frame">
                                            {{Html::skin('choose-us/wifi.png', array('class' => 'img-responsive'))}}
                                        </div>
                                    </td>
                                    <td><b>{{Translator::transSmart("app.Super fast internet", "Super fast internet")}}</b></td>
                                    <td>{{Translator::transSmart("app.All Common Ground locations are Wi-Fi enabled & also include Hard-wired (Ethernet) connect.", "All Common Ground locations are Wi-Fi enabled & also include Hard-wired (Ethernet) connect.")}}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="image-frame">
                                            {{Html::skin('choose-us/printer.png', array('class' => 'img-responsive'))}}
                                        </div>
                                    </td>
                                    <td><b>{{Translator::transSmart("app.Business-Class Printers", "Business-Class Printers")}}</b></td>
                                    <td>{{Translator::transSmart("app.Multiple printers/copiers & scanners are available at each Common Ground workspace.", "Multiple printers/copiers & scanners are available at each Common Ground workspace.")}}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="image-frame">
                                            {{Html::skin('choose-us/refreshment.png', array('class' => 'img-responsive'))}}
                                        </div>
                                    </td>
                                    <td><b>{{Translator::transSmart("app.Free Refreshments", "Free Refreshments")}}</b></td>
                                    <td>{{Translator::transSmart("app.Complimentary water, coffee & tea is available to all members. Micro roasted coffee & professional baristas are available at selected spaces.", "Complimentary water, coffee & tea is available to all members. Micro roasted coffee & professional baristas are available at selected spaces.")}}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="image-frame">
                                            {{Html::skin('choose-us/staff.png', array('class' => 'img-responsive'))}}
                                        </div>
                                    </td>
                                    <td><b>{{Translator::transSmart("app.Onsite Staff", "Onsite Staff")}}</b></td>
                                    <td>{{Translator::transSmart("app.Front desk staff are available for all administrative matters while community managers are there for all other enquiries Mon - Fri (9am-6pm).", "Front desk staff are available for all administrative matters while community managers are there for all other enquiries Mon - Fri (9am-6pm).")}}</td>
                                </tr>
                            </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5">
                            <div>
                                <h3>
                                    <b>
                                        {{Translator::transSmart("app.SUPPORT SERVICES", "SUPPORT SERVICES")}}
                                    </b>
                                </h3>
                                <br />
                                <p>
                                    {{Translator::transSmart("app.Your success is key to our success. So let us take care of the small stuff so you can spend time on the important things. We’ve sourced out the best service providers so whether you need a hand filing your taxes or organising a party, we’ve got you covered.", "Your success is key to our success. So let us take care of the small stuff so you can spend time on the important things. We’ve sourced out the best service providers so whether you need a hand filing your taxes or organising a party, we’ve got you covered.") }}
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-7">
                            <div>
                                <table class="table service">
                                    <tr>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/ceo-sec.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.CO-SEC", 'CO-SEC')}}</b>
                                        </td>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/msc.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.MSC", 'MSC')}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/accounting.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.ACCOUNTING AND TAX", 'ACCOUNTING AND TAX')}}</b>
                                        </td>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/graphic-design.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.GRAPHICS DESIGN", 'GRAPHICS DESIGN')}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/legal.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.LEGAL", 'LEGAL')}}</b>
                                        </td>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/courier.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.COURIER", 'COURIER')}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/insurance.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.INSURANCE", 'INSURANCE')}}</b>
                                        </td>
                                        <td>
                                            <div class="image-frame">
                                                {{Html::skin('choose-us/hr.png', array('class' => 'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>
                                            <b>{{Translator::transSmart("app.HR", 'HR')}}</b>
                                        </td>

                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection


@section('bottom_banner_image_url', URL::skin('choose-us/coworking.jpg'))

@section('bottom_banner')
    <div class="page-choose-us bottom-banner">
        <div>
            <table class="table two-square-boxes">
                <tbody>
                <tr>

                    <td>

                        <h2>
                            {{Html::linkRoute('page::index', Translator::transSmart("app.CHECKOUT OUR PACKAGES", "CHECKOUT OUR PACKAGES"), ['slug' => 'packages'], [ 'title' => Translator::transSmart("app.CHECKOUT OUR PACKAGES", "CHECKOUT OUR PACKAGES")])}}
                        </h2>
                    </td>


                </tr>
                </tbody>
            </table>
        </div>


    </div>
@endsection