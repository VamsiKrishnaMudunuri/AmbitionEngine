@extends('layouts.admin')
@section('title', Translator::transSmart('app.Batch Upload', 'Batch Upload'))

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::upload-batch', [$property->getKey()],  URL::route('admin::managing::subscription::upload-batch', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Batch Upload', 'Batch Upload'), [], ['title' =>  Translator::transSmart('app.Batch Upload', 'Batch Upload')]],


        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-upload-batch">
        
        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Batch Upload', 'Batch Upload')}}
                    </h3>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="col-sm-12">
    
                {{ Form::open(array('route' => array('admin::managing::subscription::upload-batch', $property->getKey()), 'files' => true,  'class' => 'form-horizontal form-search')) }}
    
                    <div class="row">
                        
                        <div class="col-sm-12">
                            <div class="form-group">
                                
                                {{ Html::validation($sandbox, $sandbox->field()) }}
                                <label for="{{$sandbox->field()}}" class="col-sm-0 col-md-0 col-lg-0"></label>
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                      <span class="help-block">
                                        {{ Translator::transSmart('app.1. Only %s extensions are supported.', sprintf('1. Only %s extensions are supported.', join(',', $sandboxConfig['mimes'])), true, ['mimes' => join(',',$sandboxConfig['mimes'])]) }} <br />
                                        {{ Translator::transSmart('app.2. Maximum upload file size if %sMB.', sprintf('2. Maximum upload file size if %sMB', $sandboxConfig['size'] / 1000), true, ['size' => $sandboxConfig['size'] / 1000]) }} <br />
                                        {{ Translator::transSmart('app.3. You are only allowed to upload up to %s record(s).', sprintf('3. You are only allowed to upload up to %s record(s).', $supportRecords), false, ['record' => $supportRecords])}}
                            
                                        </span>
                                    
                                        {{ Form::file($sandbox->field(), array('id' =>  $sandbox->field(), 'class' => '', 'title' => Translator::transSmart('app.Upload Excel Sample', 'Upload Excel Sample'))) }}
                                </div>
                                
                            </div>
                        </div>
                     
                    </div>
    
                    <div class="row">
                        <div class="col-sm-12 toolbar">
                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
                                    {{
                                        Html::linkRouteWithIcon(
                                            null,
                                            Translator::transSmart('app.Upload', 'Upload'),
                                            'fa-upload',
                                           array(),
                                           [
                                               'title' => Translator::transSmart('app.Upload', 'Upload'),
                                               'class' => 'btn btn-theme search-btn',
                                               'onclick' => "$(this).closest('form').submit();"
                                           ]
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                
                {{ Form::close() }}
                
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                
                <br /><br />
                
                {{ Html::success() }}
                {{ Html::error() }}
    
                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">
            
                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                <th>{{Translator::transSmart('app.Message', 'Message')}}</th>
                                <th>{{Translator::transSmart('app.User ID', 'User ID')}}</th>
                                <th>{{Translator::transSmart('app.Package ID', 'Package ID')}}</th>
                                <th>{{Translator::transSmart('app.Seat ID', 'Seat ID')}}</th>
                                <th>{{Translator::transSmart('app.Package Type', 'Package Type')}}</th>
                                <th>{{Translator::transSmart('app.Seat', 'Seat')}}</th>
                                <th>{{Translator::transSmart('app.Complimentary Credit (Meeting Room)', 'Complimentary Credit (Meeting Room)')}}</th>
                                <th>{{Translator::transSmart('app.Complimentary Credit (Printer)', 'Complimentary Credit (Printer)')}}</th>
                                <th>{{Translator::transSmart('app.Contract Month', 'Contract Month')}}</th>
                                <th>{{Translator::transSmart('app.Start Date', 'Start Date')}}</th>
                                <th>{{Translator::transSmart('app.End Date', 'End Date')}}</th>
                                <th>{{Translator::transSmart('app.Price', 'Price')}}</th>
                                <th>{{Translator::transSmart('app.Discount', 'Discount')}}</th>
                                <th>{{Translator::transSmart('app.Deposit', 'Deposit')}}</th>
                                <th>{{Translator::transSmart('app.Tax Name', 'Tax Name')}}</th>
                                <th>{{Translator::transSmart('app.Tax Value', 'Tax Value')}}</th>
                                <th>{{Translator::transSmart('app.Grand Total', 'Grand Total')}}</th>
                            </tr>
                        </thead>
            
                        <tbody>
                            @if($result->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="19">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif

                            @foreach($result as $rkey => $row)
                                <tr>
                                    <td>
                                    {{ $loop->iteration }}
							        </td>
                                     @foreach($row as $ckey => $cell)
                                         
                                         <td>
                                             @if($ckey == 0)
                                                 {{$cell ? Translator::transSmart('app.Success', 'Success') : Translator::transSmart('app.Failed', 'Failed')}}
                                             @elseif($ckey == 1)
                                                 {!! $cell !!}
                                             @else
                                                 {{$cell}}
                                             @endif
                                         </td>
                                        
                                     @endforeach
                                </tr>
                            @endforeach
                        
                        </tbody>
                        
                    </table>
                    
                </div>
                
            </div>
        </div>
    
      
        
    </div>

@endsection