<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field = $lead->getCreatedAtColumn();
				$name = $field;
				$translate = Translator::transSmart('app.Creator:', 'Creator:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				<p class="form-control-static">
					{{Utility::display($lead->getCreatorFullName(Translator::transSmart('app.System', 'System')))}}
				</p>
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field = $lead->getCreatedAtColumn();
				$name = $field;
				$translate = Translator::transSmart('app.Editor:', 'Editor:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				<p class="form-control-static">
					{{Utility::display($lead->getEditorFullName(Translator::transSmart('app.System', 'System')))}}
				</p>
			</div>
		
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field = $lead->getCreatedAtColumn();
				$name = $field;
				$translate = Translator::transSmart('app.Created:', 'Created:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				<p class="form-control-static">
					@php
						$time =  CLDR::showDateTime($lead->getAttribute($field), config('app.datetime.datetime.format_timezone'), $property->timezone)
					@endphp
					{{Utility::display($time)}}
				</p>
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			
			@php
				$field = $lead->getUpdatedAtColumn();
				$name = $field;
				$translate = Translator::transSmart('app.Modified:', 'Modified:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				<p class="form-control-static">
					@php
						$time =  CLDR::showDateTime($lead->getAttribute($field), config('app.datetime.datetime.format_timezone'), $property->timezone)
					@endphp
					{{Utility::display($time)}}
				</p>
			</div>
		
		</div>
	</div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            
            @php
                    $field = 'ref';
                    $name = $field;
                    $translate = Translator::transSmart('app.Lead No:', 'Lead No:');
            @endphp
           
            <label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
            <div class="col-sm-9">
	            {{Html::validation($lead, $field)}}
                <p class="form-control-static">
	                {{Utility::display($lead->ref)}}
                </p>
            </div>
            
        </div>
    </div>
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'status';
				$name = $field;
				$translate = Translator::transSmart('app.Status:', 'Status:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				
				{{Html::validation($lead, $field)}}
					
				@if($lead->exists)
					{{Form::select($name, Utility::constant('lead_status', true) , $lead->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
				@else
					<p class="form-control-static">
						{{Utility::constant('lead_status.lead.name')}}
					</p>
				@endif
					
				
			</div>
		
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'source';
				$name = $field;
				$translate = Translator::transSmart('app.Source:', 'Source:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				@if($lead->exists && !$lead->is_editable)
					<p class="form-control-static">
						{{Utility::constant(sprintf('lead_source.%s.name', $lead->source))}}
					</p>
				@else
					{{Html::validation($lead, $field)}}
					{{Form::select($name, Utility::constant('lead_source', true) , $lead->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
				@endif
			</div>
		
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field =  $lead->pic()->getForeignKey();
				$field1 = sprintf('_%s', $field);
				$name = $field;
				$name1 = $field1;
				$translate = Translator::transSmart('app.Responsible Person:', 'Responsible Person:');
				$translate1= Translator::transSmart('app.Search by name, username or email.', 'Search by name, username or email.');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				{{Html::validation($lead, $field)}}
				{{Form::hidden($name, ($lead->getAttribute($field)) ? $lead->getAttribute($field): null , array('class' => sprintf('%s_hidden', $field)))}}
				<div class="twitter-typeahead-container">
					{{Form::text($name1, ($lead->pic ? $lead->pic->full_name : '') , array('id' => $name1, 'class' => sprintf('%s form-control twitter-typeahead-user', $field1), 'data-target' => $field, 'data-url' => URL::route('api::search::user-staff'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => $translate1))}}
				</div>
			</div>
		
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = $lead->referrer()->getForeignKey();
				$field1 = sprintf('_%s', $field);
				$name = $field;
				$name1 = $field1;
				$translate = Translator::transSmart('app.Referrer:', 'Referrer:');
				$translate1= Translator::transSmart('app.Search by name, username or email.', 'Search by name, username or email.');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				
				@if($lead->exists && !$lead->is_editable)
					
					<p class="form-control-static">
						@if($lead->referrer)
							{{$lead->referrer->full_name}}
						@endif
					</p>
					
				@else
					
					{{Html::validation($lead, $field)}}
					{{Form::hidden($name, ($lead->getAttribute($field)) ? $lead->getAttribute($field): null , array('class' => sprintf('%s_hidden', $field)))}}
					<div class="twitter-typeahead-container">
						{{Form::text($name1, ($lead->referrer ? $lead->referrer->full_name : '') , array('id' => $name1, 'class' => sprintf('%s form-control twitter-typeahead-user', $field1), 'data-target' => $field, 'data-url' => URL::route('api::search::user-member'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => $translate1))}}
					</div>
					<span class="help-block">
						{{Translator::transSmart('app.This field is optional only if source is admin/website.', 'This field is optional only if source is admin/website.')}}
					</span>
					
				@endif
				
			</div>
			
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group required">
			
			@php
				$field = 'commission_schema';
				$name = $field;
				$translate = Translator::transSmart('app.Commission Schema:', 'Commission Schema:');
			@endphp
			
			<label for="{{$name}}" class="col-sm-3 control-label">{{$translate}}</label>
			<div class="col-sm-9">
				@if($lead->exists && !$lead->is_editable)
					
					<p class="form-control-static">
						{{Utility::constant(sprintf('commission_schema.%s.name', $lead->commission_schema))}}
					</p>
				
				@else
					{{Html::validation($lead, $field)}}
					{{Form::select($name, Utility::constant('commission_schema', true, [Utility::constant('commission_schema.salesperson.slug')]) , $lead->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate, 'placeholder' => ''))}}
					<span class="help-block">
						{{Translator::transSmart('app.This field is optional only if source is admin/website.', 'This field is optional only if source is admin/website.')}}
					</span>
				@endif
				
			</div>
		
		</div>
	</div>
</div>