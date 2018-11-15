@section('styles')
	@parent
	{{ Html::skin('app/modules/admin/managing/subscription/booking-matrix.css') }}
@endsection

@section('scripts')
	@parent
	{{ Html::skin('app/modules/admin/managing/subscription/booking-matrix.js') }}
@endsection

<div class="row">
	<div class="col-sm-12">
		
		{{ Form::open(array('route' => $form_search_route, 'class' => 'form-search')) }}
		
			<div class="row">
				
				<div class="col-sm-3">
					<div class="form-group">
						@php
							$name = 'category';
							$translate = Translator::transSmart('app.Package', 'Package');
						@endphp
						<label for="{{$name}}" class="control-label">{{$translate}}</label>
						{{Form::select($name, $subscription->getPackagesList(), $category, array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
					</div>
				</div>
				
				<div class="col-sm-3">
					<div class="form-group">
						@php
							$name = 'start_date';
							$translate = Translator::transSmart('app.Check-In', 'Check-In');
						@endphp
						
						<label for="{{$name}}" class="control-label">{{$translate}}</label>
						<div class="input-group schedule">
							{{Form::text($name, $start_date , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => ''))}}
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>
				
				<div class="col-sm-12 toolbar">
					
					<div class="btn-toolbar pull-right">
						<div class="btn-group">
							{{
								Html::linkRouteWithIcon(
									null,
									Translator::transSmart('app.Search', 'Search'),
									'fa-search',
								   array(),
								   [
									   'title' => Translator::transSmart('app.Search', 'Search'),
									   'class' => 'btn btn-theme search-btn',
									   'onclick' => "$(this).closest('form').submit();"
								   ]
								)
							}}
						</div>
					</div>
				
				</div>
			
			
			</div>
			
			<div class="row">
				<div class="col-sm-12 toolbar">
				
				
				</div>
			</div>
			
		{{ Form::close() }}
	
	</div>
	
</div>


<div class="row">
	<div class="col-sm-12">
		<hr />
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		
		{{ Html::success() }}
		{{ Html::error() }}
		
		<div class="table-responsive">
			<table class="table table-bordered table-condensed booking-matrix">
				
				<tbody>
				
				@php
					
					$subscription->syncFromProperty($property);
					$subscription->syncFromPrice($package);
					$subscription->setupInvoice($property, $start_date);
				
				@endphp
				
				<tr class="package">
					<th colspan="11">
						{{$package->name}}
					</th>
				</tr>
				<tr class="facilities">
					<td colspan="11">
						<table class="table table-condensed">
							<colgroup>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col>
								<col width="10%">
								<col>
							</colgroup>
							<tr>
								<th></th>
								<th class="text-center">{{Translator::transSmart('app.Name', 'Name')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Label', 'Label')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Seat', 'Seat')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Regular Price', 'Regular Price')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Prorated Price', 'Prorated Price')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Taxable Amount', 'Taxable Amount')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($property->tax_value)), true, ['tax' => CLDR::showTax($property->tax_value)])}}</th>
								<th class="text-center">{{Translator::transSmart('app.Deposit', 'Deposit')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Total', 'Total')}}</th>
								<th class="text-center">{{Translator::transSmart('app.Member', 'Member')}}</th>
								<th class="text-center"></th>
							</tr>
							<tr>
								<td>
									<div class="photo">
										<div class="photo-frame md">
											<a href="javascript:void(0);">
											
											</a>
										</div>
									</div>
								</td>
								<td class="text-center">
									{{$package->name}}
								</td>
								<td class="text-center">
									{{Translator::transSmart('app.Nil', 'Nil')}}
								</td>
								<td class="text-center">
									{{Translator::transSmart('app.Nil', 'Nil')}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->proratedPrice(), $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->taxableAmount(), $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->tax($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center">
									{{CLDR::showPrice($subscription->grossPriceAndDeposit($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
								</td>
								<td class="text-center"></td>
								<td class="text-center">
									
									@if($subscription->grossPriceAndDeposit($property->tax_value) <= 0)
										<span class="label label-default">{{Translator::transSmart('app.Price Not Set Up', 'Price Not Set Up')}}</span>
									@elseif(!$property->isActive() || !$package->isActive())
										<span class="label label-default">{{Utility::constant('status.0.name')}}</span>
									@elseif($property->coming_soon)
										<span class="label label-default">{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}</span>
									@else
										
										@if($isWrite)
											{{
												   Html::linkRouteWithIcon(
												   	$book_package_route['name'],
													Translator::transSmart('app.Book', 'Book'),
													'',
													 array_merge($book_package_route['parameters'], array('package_id' => $package->getKey(), 'start_date' => Crypt::encrypt($start_date))),
													[
													'title' => Translator::transSmart('app.Book', 'Book'),
													'class' => 'btn btn-theme'
													]
												   )
											 }}
										@endif
									@endif
								
								
								</td>
							
							
							</tr>
						</table>
					</td>
				
				</tr>
				
				@if($facilities->isEmpty())
					<tr>
						<td class="text-center" colspan="12">
							--- {{ Translator::transSmart('app.No Other Packages Found.', 'No Other Packages Found.') }} ---
						</td>
					</tr>
				@endif
				
				@foreach($facilities as $category => $categories)
					
					<tr class="package">
						<th colspan="12">
							{{Utility::constant(sprintf('facility_category.%s.name', $category))}}
						</th>
					</tr>
					
					@foreach($categories as $unit => $units)
						
						<tr class="unit">
							<th colspan="12">
								
								{{
								   Html::linkRouteWithIcon(
									   null,
									   $unit,
									   'fa-minus',
									  array(),
									  [
										  'title' => $unit,
										  'class' => 'unit-toggle',
										  'data-unit' => $unit
									  ]
								   )
							 }}
							
							</th>
						</tr>
						<tr class="facilities" data-unit="{{$unit}}">
							<td colspan="11">
								<table class="table table-condensed">
									<colgroup>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col>
										<col width="10%">
										<col>
									</colgroup>
									<tr>
										<th></th>
										<th class="text-center">{{Translator::transSmart('app.Name', 'Name')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Label', 'Label')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Seat', 'Seat')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Regular Price', 'Regular Price')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Prorated Price', 'Prorated Price')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Taxable Amount', 'Taxable Amount')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($property->tax_value)), true, ['tax' => CLDR::showTax($property->tax_value)])}}</th>
										<th class="text-center">{{Translator::transSmart('app.Deposit', 'Deposit')}}</th>
										<th class="text-center">{{Translator::transSmart('app.Total', 'Total')}}</th>
										<th class="text-center"></th>
										<th class="text-center"></th>
									</tr>
									@foreach($units as $facility)
										
										@foreach($facility->units as $gunit)
											@php
												$price = $facility->prices->first();
												$subscription->syncFromProperty($property);
												$subscription->syncFromPrice($price);
												$subscription->setupInvoice($property, $start_date);
											@endphp
											<tr>
												<td>
													
													<?php
													$config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
													$sandbox->magicSubPath($config, [$property->getKey()]);
													$mimes = join(',', $config['mimes']);
													$minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
													$dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
													?>
													
													<div class="photo">
														<div class="photo-frame md">
															<a href="javascript:void(0);">
																{{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension)}}
															</a>
														</div>
													</div>
												
												</td>
												<td class="text-center">
													{{$facility->name}}
												</td>
												<td class="text-center">
													{{$gunit->name}}
												</td>
												<td class="text-center">
													{{$facility->seat}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->proratedPrice(), $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->taxableAmount(), $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->tax($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													{{CLDR::showPrice($subscription->grossPriceAndDeposit($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
												</td>
												<td class="text-center">
													@php
														$member = null;
													@endphp
													
													@if( $gunit->subscribing->count() > 0 )
														
														@php
															
															$member = $gunit->subscribing->first()->users->where('pivot.is_default', '=', Utility::constant('status.1.slug'))->first();
														
														@endphp
													
													@endif
													
													@if( $gunit->reserving->count() > 0 )
														@php
															$member = $gunit->reserving->first()->user;
														@endphp
													@endif
													
													@if(!is_null($member))
														@if($isReadMemberProfile)
															{{
															  Html::linkRoute(
															   'admin::managing::member::profile',
															   $member->full_name,
															   [
																'property_id' => $property->getKey(),
																'id' => $member->getKey()
															   ],
															   [
																'target' => '_blank'
															   ]
															  )
															}}
														@else
															{{$member->full_name}}
														@endif
													@endif
												</td>
												<td class="text-center">
													
													@if($gunit->subscribing->count() > 0 || $gunit->reserving->count() > 0)
														<span class="label label-success">{{Translator::transSmart('app.Reserved', 'Reserved')}}</span>
													@elseif($subscription->grossPriceAndDeposit($property->tax_value) <= 0)
														<span class="label label-success">{{Translator::transSmart('app.Price Not Set Up', 'Price Not Set Up')}}</span>
													@elseif(
													  !$property->isActive() ||
													  !$facility->isActive() ||
													  !$gunit->isActive() ||
													  !$price->isActive()
													)
														<span class="label label-default">{{Utility::constant('status.0.name')}}</span>
													@elseif($property->coming_soon)
														<span class="label label-default">{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}</span>
													@else
														
														@if($isWrite)
															{{
																   Html::linkRouteWithIcon(
																   $book_facility_route['name'],
																	Translator::transSmart('app.Book', 'Book'),
																	'',
																	array_merge( $book_facility_route['parameters'], array('facility_id' => $facility->getKey(), 'facility_unit_id' => $gunit->getKey(), 'start_date' => Crypt::encrypt($start_date))),
																	[
																	'title' => Translator::transSmart('app.Book', 'Book'),
																	'class' => 'btn btn-theme'
																	]
																   )
															 }}
														@endif
													
													@endif
												
												</td>
											
											
											</tr>
										@endforeach
									@endforeach
								</table>
							</td>
						</tr>
					
					@endforeach
				@endforeach
				
				
				</tbody>
			
			
			</table>
		</div>
	
	</div>
</div>