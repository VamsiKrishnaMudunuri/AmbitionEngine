<div class="panel panel-default lead-header">
	<div class="panel-heading">
		<div class="header-container">
			<div class="title">
				{{Translator::transSmart('app.Lead', 'Lead')}}
			</div>
			<div class="menu">
				@php
					$activity_switch = (isset($activity_switch) && $activity_switch);
				    $activity_attributes = ['title' => Translator::transSmart('app.Activity', 'Activity'), 'class' => 'activity', 'data-title' => Translator::transSmart('app.Activity', 'Activity'), 'data-url' => URL::route('admin::managing::lead::activity', array($property->getKey(), $lead->getKey()))];
					if(!$activity_switch){
						$activity_attributes['disabled'] = 'disabled';
					}
				@endphp
				@if($activity_switch)
					{{ Html::linkRoute(null, Translator::transSmart('app.Activity', 'Activity'), [], $activity_attributes) }}
				@endif
			</div>
		</div>
	</div>
	<div class="panel-body">
		@include('templates.admin.managing.lead.lead_header_form_fields')
	</div>
</div>