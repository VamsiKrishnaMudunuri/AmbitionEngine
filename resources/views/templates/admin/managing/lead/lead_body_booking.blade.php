<div class="panel panel-default lead-body lead-body-status lead-body-{{Utility::constant('lead_status.booking.slug')}} lead-body-{{Utility::constant('lead_status.tour.slug')}} booking">
	<div class="panel-heading">
		
		<div class="header-container">
			<div class="title">
				{{Translator::transSmart('app.Site Visit Booking', 'Site Visit Booking')}}
			</div>
			<div class="menu">
			
			</div>
		</div>
		
		
	</div>
	<div class="panel-body">
		<div class="action text-right">
			@php
			
				$title = Translator::transSmart('app.Add Site Visit', 'Add Site Visit');
			    $attributes = array('title' => $title, 'class' => 'add-site-visit', 'data-title' => $title, 'data-url' => URL::route('admin::managing::lead::add-booking-site-visit', [$property->getKey(), $lead->getKey()]));
			
				if(!$isWrite || !$lead->isAllowToEdit()){
					$attributes['disabled'] = 'disabled';
				}
			
			@endphp
			<br />
			{{Html::linkRoute(null, $title , array(), $attributes)}}
			<br /><br />
		
		</div>
		<div class="listing">
			<div class="table-responsive">
				<table class="table table-condensed table-crowded">
					
					<thead>
					<tr>
						<th>{{Translator::transSmart('app.#', '#')}}</th>
						<th>{{Translator::transSmart('app.Name', 'Name')}}</th>
						<th>{{Translator::transSmart('app.Company', 'Company')}}</th>
						<th>{{Translator::transSmart('app.Email', 'Email')}}</th>
						<th>{{Translator::transSmart('app.Contact', 'Contact')}}</th>
						<th>{{Translator::transSmart('app.Office', 'Office')}}</th>
						<th>{{Translator::transSmart('app.Schedule', 'Schedule')}}</th>
						<th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
						<th>{{Translator::transSmart('app.Created', 'Created')}}</th>
						<th>{{Translator::transSmart('app.Modified', 'Modified')}}</th>
						<th></th>
					</tr>
					</thead>
					
					<tbody>
					@if($lead->bookings->isEmpty())
						<tr>
							<td class="text-center empty" colspan="11">
								--- {{Translator::transSmart('app.Nil', 'Nil')}}  ---
							</td>
						</tr>
					@endif
					@foreach($lead->bookings as $booking)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$booking->name}}</td>
							<td>{{$booking->company}}</td>
							<td>{{$booking->email}}</td>
							<td>{{$booking->contact}}</td>
							<td>
								<div class="child-col">
									<h6>{{Translator::transSmart('app.Location', 'Location')}}</h6>
									@if($booking->isOldVersion())
										<span> {{$booking->nice_location}}</span>
									@else
										<span>
                                                @if($booking->property && $booking->property->exists)
												
												{{$booking->property->smart_name}}
											
											@endif
                                            </span>
									@endif
								</div>
								<div class="child-col">
									<h6>{{Translator::transSmart('app.Membership Type', 'Membership Type')}}</h6>
									<span>{{Utility::constant(sprintf('package.%s.name', $booking->office))}}</span>
								</div>
								<div class="child-col">
									<h6>{{Translator::transSmart('app.Pax', 'Pax')}}</h6>
									<span>{{($booking->pax > 10) ? '10+' : $booking->pax}}</span>
								</div>
							</td>
							<td>
								
								@if($booking->type == 1)
									@if($booking->isOldVersion())
										
										{{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->defaultTimezone)}} {{ CLDR::getTimezoneByCode($booking->defaultTimezone, true)}}
									
									@else
										
										@if($booking->property && $booking->property->exists)
											{{CLDR::showDateTime($booking->schedule, config('app.datetime.datetime.format'), $booking->property->timezone, null)}} {{ CLDR::getTimezoneByCode($booking->property->timezone, true)}}
										@endif
									
									@endif
								@else
								
								@endif
							
							</td>
							<td>
								{{$booking->request}}
							</td>
							<td>
								{{CLDR::showDateTime($booking->getAttribute($booking->getCreatedAtColumn()), config('app.datetime.datetime.format'))}}
							</td>
							<td>
								{{CLDR::showDateTime($booking->getAttribute($booking->getUpdatedAtColumn()), config('app.datetime.datetime.format'))}}
							</td>
							<td class="item-toolbox nowrap">
								@php
									
									$title = Translator::transSmart('app.Edit', 'Edit');
									$attributes = array(
									  'title' => $title,
									  'class' => 'btn btn-theme edit-site-visit',
									  'data-title' => $title,
									  'data-url' => URL::route('admin::managing::lead::edit-booking-site-visit', [$property->getKey(), $lead->getKey(), $booking->getKey()])
								     );
								
									if(!$isWrite || !$lead->isAllowToEdit()){
										$attributes['disabled'] = 'disabled';
									}
								
								@endphp
								
								{{
									 Html::linkRouteWithIcon(
									   null,
									  $title,
									  'fa-pencil',
									  [],
									  $attributes
									 )
							   }}
								
								@php
									
									$title = Translator::transSmart('app.Delete', 'Delete');
									$attributes = array(
									  'title' => $title,
									  'class' => 'btn btn-theme delete-site-visit',
									  'data-confirm-message' => Translator::transSmart('app.Are you sure to delete?', 'Are you sure to delete?'),
									  'data-title' => $title,
									  'data-url' => URL::route('admin::managing::lead::post-delete-booking-site-visit', [$property->getKey(), $lead->getKey(), $booking->getKey()])
								     );
								
									if(!$isWrite || !$lead->isAllowToEdit()){
										$attributes['disabled'] = 'disabled';
									}
								
								@endphp
								
								{{
									 Html::linkRouteWithIcon(
									   null,
									  $title,
									  'fa-trash',
									  [],
									  $attributes
									 )
							   }}
								
								
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
