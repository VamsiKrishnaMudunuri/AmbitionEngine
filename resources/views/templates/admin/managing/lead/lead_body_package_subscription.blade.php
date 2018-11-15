<div class="panel panel-default lead-body lead-body-status lead-body-{{Utility::constant('lead_status.follow-up.slug')}} lead-body-{{Utility::constant('lead_status.win.slug')}} lead-body-{{Utility::constant('lead_status.lost.slug')}} lead-body-{{Utility::constant('lead_status.win.slug')}} package-subscription">
	<div class="panel-heading">
		
		<div class="header-container">
			<div class="title">
				{{Translator::transSmart('app.Package(s) Subscription', 'Package(s) Subscription')}}
			</div>
			<div class="menu">
			
			</div>
		</div>
	
	</div>
	<div class="panel-body">
		
		<div class="action text-right">
			
			@php
				
				$title = Translator::transSmart('app.Add Member', 'Add Member');
				$attributes = array('title' => $title, 'class' => 'add-member', 'data-title' => $title, 'data-url' => URL::route('admin::managing::lead::add-member', [$property->getKey(), $lead->getKey()]));
			
				if(!$isWrite || !$lead->isAllowToEdit()){
					$attributes['disabled'] = 'disabled';
				}
			
			@endphp
			<br />
			{{Html::linkRoute(null, $title , array(), $attributes)}}
			<br /><br />
			
		</div>
		
		<div class="form-group">
					
					@php
						$field = $lead->user()->getForeignKey();
						$field1 = sprintf('_%s', $field);
						$name = $field;
						$name1 = $field1;
						$translate = Translator::transSmart('app.Member:', 'Member:');
						$translate1= Translator::transSmart('app.Search by name, username or email.', 'Search by name, username or email.');
					@endphp
					
					<label for="{{$name}}" class="col-sm-1 control-label">{{$translate}}</label>
					<div class="col-sm-11">
						{{Html::validation($lead, $field)}}
						{{Form::hidden($name, ($lead->getAttribute($field)) ? $lead->getAttribute($field): null , array('class' => sprintf('%s_hidden', $field)))}}
						<div class="twitter-typeahead-container">
							{{Form::text($name1, ($lead->user ? $lead->user->full_name : '') , array('id' => $name1, 'class' => sprintf('%s form-control twitter-typeahead-user-with-management', $field1), 'data-target' => $field, 'data-url' => URL::route('api::search::user-member'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => $translate1, 'data-member-write-right' => ($isWrite && $lead->isAllowToEdit()), 'data-edit-title' => Translator::transSmart('app.Update Member', 'Update Member'), 'data-edit-word' => Translator::transSmart('app.Edit', 'Edit'), 'data-edit-url' => URL::route('admin::managing::lead::edit-member', [$property->getKey(), $lead->getKey(), ''])))}}
						</div>
						<div>
							<span class="help-block">
								{{Translator::transSmart('app.Please select member before proceed to subscribe for any packages.', 'Please select member before proceed to subscribe for any packages.')}}
							</span>
						</div>
					</div>
				
		</div>

		<div class="action text-right">
			@php
				
				$title = Translator::transSmart('app.Add Subscription', 'Add Subscription');
				$attributes = array('title' => $title, 'class' => 'add-subscription', 'data-title' => $title, 'data-url' => URL::route('admin::managing::lead::check-availability-subscription', [$property->getKey(), $lead->getKey(), '']));
			
				$attributes['data-right'] = true;
				
				if(!$isWrite || !$lead->isAllowToEdit()){
					$attributes['disabled'] = 'disabled';
					$attributes['data-right'] = false;
				}else{
					if(!$lead->getAttribute($lead->user()->getForeignKey())){
						$attributes['disabled'] = 'disabled';
					}
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
						<th>{{Translator::transSmart('app.Member', 'Member')}}</th>
						<th>{{Translator::transSmart('app.Reference', 'Reference')}}</th>
						<th>{{Translator::transSmart('app.Building', 'Building')}}</th>
						<th>{{Translator::transSmart('app.Package', 'Package')}}</th>
						<th>{{Translator::transSmart('app.Subscribed', 'Subscribed')}}</th>
						<th>{{Translator::transSmart('app.Billing Cycle', 'Billing Cycle')}}</th>
						<th>{{Translator::transSmart('app.Complimentaries', 'Complimentaries')}}</th>
						<th>{{Translator::transSmart('app.Last Payment', 'Last Payment')}}</th>
						<th>{{Translator::transSmart('app.Person', 'Person')}}</th>
						<th>{{Translator::transSmart('app.Time', 'Time')}}</th>
						<th></th>
					</tr>
					</thead>
					
					<tbody>
						@php
							$subscriptions = $lead->subscriptions;
						@endphp
						@if($subscriptions->isEmpty())
							<tr>
								<td class="text-center empty" colspan="12">
									--- {{Translator::transSmart('app.Nil', 'Nil')}}  ---
								</td>
							</tr>
						@endif
						@foreach($subscriptions as $subscription)
							<tr>
								<td>{{$loop->iteration}}</td>
								<td>
									@php
										$user =  $subscription->users
											->where('pivot.is_default', '=', Utility::constant('status.1.slug'))
											->first();
									@endphp
									@if($user)
										@if($isReadMemberProfile)
											{{
											  Html::linkRoute(
											   'admin::managing::member::profile',
											   $user->full_name,
											   [
												'property_id' => $property->getKey(),
												'id' => $user->getKey()
											   ],
											   [
												'target' => '_blank'
											   ]
											  )
											}}
										@else
											<b>{{Translator::transSmart('app.Name', 'Name')}}</b>
											<hr />
											{{$user->full_name}}
											<hr />
											<b>{{Translator::transSmart('app.Username', 'Username')}}</b>
											<hr />
											{{$user->username}}
											<hr />
											<b>{{Translator::transSmart('app.Email', 'Email')}}</b>
											<hr />
											{{$user->email}}
										@endif
									@endif
								</td>
								<td>
									{{$subscription->ref}}
								</td>
								<td>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Block', 'Block')}}</h6>
										<span>{{$subscription->package_block}}</span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Level', 'Level')}}</h6>
										<span>{{$subscription->package_level}}</span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Unit', 'Unit')}}</h6>
										<span>{{$subscription->package_unit}}</span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Label', 'Label')}}</h6>
										<span>{{$subscription->package_label}}</span>
									</div>
								</td>
								<td>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
										<span>
                                                {{$subscription->package_name}}
                                            </span>
									</div>
									
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Category', 'Category')}}</h6>
										<span>
                                                 {{$subscription->package_category}}
                                            </span>
									</div>
									
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Seat(s)', 'Seat(s)')}}</h6>
										<span>
                                                @if(!is_null($subscription->getAttribute($subscription->package()->getForeignKey())))
												{{CLDR::showNil()}}
											@else
												{{ $subscription->seat }}
											@endif

                                            </span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Contract', 'Contract')}}</h6>
										<span>

                                                  {{$subscription->contract_month}} {{trans_choice('plural.month', intval($subscription->contract_month))}}


                                            </span>
									</div>
									
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Recurring Billing', 'Recurring Billing')}}</h6>
										<span>

                                              {{Utility::constant(sprintf('flag.%s.name', $subscription->is_recurring))}}

                                            </span>
									</div>
									
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Status', 'Status')}}</h6>
										<span>
                                                {{Utility::constant(sprintf('subscription_status.%s.name', $subscription->status))}}
                                            </span>
									</div>
								
								
								</td>
								
								<td>
									
									<b>{{Translator::transSmart('app.Start', 'Start')}}</b>
									<hr />
									{{CLDR::showDateTime($subscription->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
									<hr />
									<b>{{Translator::transSmart('app.End', 'End')}}</b>
									<hr />
									@if(is_null($subscription->end_date))
										{{Translator::transSmart('app.Nil', 'Nil')}}
									@else
										{{CLDR::showDateTime($subscription->end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
									@endif
								
								</td>
								<td>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Issued', 'Issued')}}</h6>
										<span>
                                                  {{CLDR::showDateTime($subscription->billing_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Next', 'Next')}}</h6>
										<span>
                                                 {{CLDR::showDateTime($subscription->next_billing_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
									</div>
								</td>
								<td>
									@foreach($subscription->complimentaries as $category => $value)
										
										<div class="child-col">
											<h6>{{Utility::constant(sprintf('facility_category.%s.name', $category))}}</h6>
											<span>
                                                 {{CLDR::showCredit($value)}}
                                                </span>
										</div>
									@endforeach
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Next Reset', 'Next Reset')}}</h6>
										<span>
                                              {{CLDR::showDateTime($subscription->next_reset_complimentaries_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
									</div>
								
								</td>
								<td>
									
									@if(!$subscription->lastPaidInvoiceQuery->isEmpty())
										
										@php
											$invoice = $subscription->lastPaidInvoiceQuery->first();
											$invoice->setupInvoice($property, $invoice->start_date, $invoice->end_date);
											$balanceSheet = $invoice->summaryOfBalanceSheet->first();
											$balancesheetKey = sprintf('balancesheet.%s', $invoice->getKey());
											$refund = null;

											 if(!is_null($subscription->refund)){
											   $refund = $subscription->refund;
											 }
										@endphp
										
										<a href="javascript:void(0);" class="subscription-show-price">
											{{CLDR::showPrice($balanceSheet->totalCharge(), $invoice->currency, Config::get('money.precision'))}}
										</a>
										
										@section($balancesheetKey)
											@include('templates.admin.managing.subscription.invoice_balancesheet', array('subscription' => $subscription, 'invoice' => $invoice, 'invoice_transactions' => $invoice->transactions, 'invoice_balancesheet' => $balanceSheet, 'refund' => (!is_null($refund )) ? $refund : null, 'refund_balancesheet' => (!is_null($refund )) ? $refund->subscription : null))
										@endsection
										
										@include('templates.widget.bootstrap.modal', array(
											'modal_title' => Translator::transSmart('app.Last Payment', 'Last Payment'),
											'modal_body_html' => $__env->yieldContent($balancesheetKey)
											)
										)
										
									@else
										
											{{CLDR::showPrice(0.00, $property->currency, Config::get('money.precision'))}}
										
									@endif
								</td>
								
								<td>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
										<span>{{$subscription->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
										<span>{{$subscription->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
									</div>
								</td>
								
								<td>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
										<span>
                                                   {{CLDR::showDateTime($subscription->getAttribute($subscription->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
									</div>
									<div class="child-col">
										<h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
										<span>
                                                     {{CLDR::showDateTime($subscription->getAttribute($subscription->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
									</div>
								
								</td>
								
								<td class="item-toolbox nowrap">
									
									@php
										
										$title = Translator::transSmart('app.Void', 'Void');
										$attributes = array(
										  'title' => $title,
										  'class' => 'btn btn-theme void-subscription',
										  'data-confirm-message' => Translator::transSmart('app.Are you sure to void?', 'Are you sure to void?'),
										  'data-title' => $title,
										  'data-url' => URL::route('admin::managing::lead::post-void-subscription', [$property->getKey(), $lead->getKey(), $subscription->getKey()])
										 );
									
										if(!$isWrite || !$lead->isAllowToEdit()){
											$attributes['disabled'] = 'disabled';
										}
									
										if(!in_array($subscription->status, $subscription->confirmStatus) ||
											($subscription->invoices->first() && $subscription->invoices->first()->number_of_invoice > $subscription->voidThresholdForInvoice)
										){
											$attributes['disabled'] = 'disabled';
										}
									
									@endphp
									
									{{
										 Html::linkRouteWithIcon(
										   null,
										  $title,
										  'fa-close',
										  [],
										  $attributes
										 )
								    }}
									
									@php
									
										$title = Translator::transSmart('app.Manage', 'Manage');
										$attributes = array(
										  'title' => $title,
										  'class' => 'btn btn-theme',
										  'target' => '_blank',
										  'data-title' => $title,
										  'data-url' => ''
										 );
									
										//if(!$isWrite || !$lead->isAllowToEdit()){
										if(!$isWrite){
											$attributes['disabled'] = 'disabled';
										}
									
									@endphp
									
									{{
										 Html::linkRouteWithIcon(
										   'admin::managing::subscription::index',
										  $title,
										  'fa-close',
										  [$property->getKeyName() => $property->getKey(), 'ref' => $subscription->ref],
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
