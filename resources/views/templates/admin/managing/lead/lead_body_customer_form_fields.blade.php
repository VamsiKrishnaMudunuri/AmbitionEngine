<div class="row">
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'first_name';
				$name = $field;
				$translate = Translator::transSmart('app.First Name:', 'First Name:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				{{Form::text($name,  $lead->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'last_name';
				$name = $field;
				$translate = Translator::transSmart('app.Last Name:', 'Last Name:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				{{Form::text($name,  $lead->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
			</div>
		
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field = 'email';
				$name = $field;
				$translate = Translator::transSmart('app.Email:', 'Email:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				{{Form::email('email',  $lead->getAttribute($field), array('class' =>  sprintf('%s form-control', $field), 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate, 'autocomplete' => 'off'))}}
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'company';
				$name = $field;
				$translate = Translator::transSmart('app.Company:', 'Company:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				{{Form::text($name,  $lead->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $lead->getMaxRuleValue($field), 'title' => $translate))}}
			</div>
		
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field1 = 'contact_country_code';
				$field2 = 'contact_number';
				$name1 = $field1;
				$name2 = $field2;
				$translate1 = Translator::transSmart('app.Contact:', 'Contact:');
				$translate2 = Translator::transSmart('app.Country Code', 'Country Code');
				$translate3 = Translator::transSmart('app.Number', 'Number');
			@endphp
			
			<label for="phone_country_code" class="col-sm-3 control-label">{{$translate1}}</label>
			<div class="col-sm-9 phone">
				{{Html::validation($lead, [$field1, $field2])}}
				{{Form::select($name1, CLDR::getPhoneCountryCodes() , $lead->getAttribute($field1), array('id' => $name1, 'class' => sprintf('%s form-control country-code', $field1), 'title' => $translate2, 'placeholder' => $translate2))}}
				<span>-</span>
				{{Form::text($name2, $lead->getAttribute($field2) , array('id' => $name2, 'class' => sprintf('%s form-control number integer-value', $field2), 'maxlength' => $lead->getMaxRuleValue($field2), 'title' => $translate3, 'placeholder' => $translate3))}}
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
	
	</div>
</div>