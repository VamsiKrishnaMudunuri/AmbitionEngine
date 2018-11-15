@php
    $subscribe = new \App\Models\Subscriber();
@endphp
{{ Form::open(array('route' => 'mailchimp::post-subscribe', 'files' => true, 'class' => 'form-horizontal page-subscribe-form')) }}
<div class="message-box"></div>
<div class="form-group">
    <div class="col-xs-12 col-sm-10 col-md-10 input">
        {{Form::email('email', null, array('class' => 'form-control input-email text-white', 'maxlength' => $subscribe->getMaxRuleValue('email'), 'title' => Translator::transSmart('app.Your e-mail address', 'Your e-mail address'), 'placeholder' => Translator::transSmart('app.Your e-mail address', 'Your e-mail address')))}}
    </div>
    <div class="col-xs-12 col-sm-2 col-md-2 m-t-10 m-t-sm-0">
        <button class="btn input-submit" data-should-redirect="{{ route('mailchimp::subscribe-thank-you') }}">Submit</button>
    </div>
</div>
{{ Form::close() }}