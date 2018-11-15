<div class="row _remark-container">
	<div class="col-sm-12">
		<div class="form-group">
			
			@php
				$field = '_remark';
				$field1 = 'remark';
				$name = $field;
				$translate = Translator::transSmart('app.Remark:', 'Remark:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-12 control-label hide">{{$translate}}</label>
			<div class="col-sm-12">
				{{Html::validation($lead, $field)}}
				<div class="_remark-validation-container">
				
				</div>
				<div class="help-block">
					{{Translator::transSmart('app.Remark written here will be saved for activity log.', 'Remark written here will be saved for activity log.')}}
				</div>
				{{Form::textarea($name, '' , array('id' => $name, 'class' => sprintf('%s form-control', $field),  'maxlength' => $lead_activity->getMaxRuleValue($field1), 'rows' => 3, 'title' => $translate, 'placeholder' => '', 'data-validation-message' => Translator::transSmart('app.Please fill up remark.', 'Please fill up remark.')))}}
			</div>
		
		</div>
	</div>
</div>

@php
	$save_text =  Translator::transSmart('app.Save', 'Save');
	$cancel_text = Translator::transSmart('app.Cancel', 'Cancel');
@endphp

<div class="row modal-prompt-footer">
	<div class="col-sm-12">
		<div class="form-group">
			<div class="btn-group">
				{{Form::button($save_text, array('title' => $save_text, 'class' => 'btn btn-theme btn-block modal-submit'))}}
			</div>
			<div class="btn-group">
				{{Form::button($cancel_text, array('title' => $cancel_text, 'class' => 'btn btn-theme btn-block modal-cancel'))}}
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<div class="form-group text-center">
	
			@if(!$lead->exists || $lead->isAllowToEdit())
				<div class="btn-group">
					
					{{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
				
				</div>
			@endif
			<div class="btn-group">
				
				{{Form::submit($cancel_text, array('title' => $cancel_text, 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route('admin::managing::lead::index', array($property->getKey()))) . '"; return false;')) }}
			
			</div>
		</div>
	</div>
</div>