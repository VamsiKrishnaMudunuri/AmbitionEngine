<div class="panel panel-default lead-body lead-body-status lead-body-{{Utility::constant('lead_status.lead.slug')}} customer">
	<div class="panel-heading">
		
		<div class="header-container">
			<div class="title">
				{{Translator::transSmart('app.Customer', 'Customer')}}
			</div>
			<div class="menu">
			
			</div>
		</div>
		
	</div>
	<div class="panel-body">
		@include('templates.admin.managing.lead.lead_body_customer_form_fields')
	</div>
</div>
