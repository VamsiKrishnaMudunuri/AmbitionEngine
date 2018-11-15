<div class="panel panel-default lead-body lead-body-requested-packages requested-packages">
	<div class="panel-heading">
		
		<div class="header-container">
			<div class="title">
				{{Translator::transSmart('app.Requested Package(s)', 'Requested Package(s)')}}
			</div>
			<div class="menu">
			
			</div>
		</div>
		
	</div>
	<div class="panel-body">
		<div class="help-block">
			{{Translator::transSmart('app.Customer might be interesting on following package(s):', 'Customer might be interesting on following package(s):')}}
		</div>
		<div class="table-responsive">
			<table class="table table-condensed table-crowded">
				
				<thead>
					<tr>
						<th>{{Translator::transSmart('app.#', '#')}}</th>
						<th>{{Translator::transSmart('app.Type', 'Type')}}</th>
						<th>{{Translator::transSmart('app.Seat(s)', 'Seat(s)')}}</th>
					</tr>
				</thead>
				
				<tbody>
					@if($lead->packages->isEmpty())
						<tr>
							<td class="text-center empty" colspan="10">
								--- {{Translator::transSmart('app.Nil', 'Nil')}}  ---
							</td>
						</tr>
					@endif
					@foreach($lead->packages as $package)
						<tr>
							<td>
								{{$loop->iteration}}
							</td>
							<td>
								{{Utility::constant(sprintf('facility_category.%s.name', $package->category))}}
							</td>
							<td>
								{{$package->quantity}}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		
	</div>
	
</div>
