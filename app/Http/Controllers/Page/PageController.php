<?php

namespace App\Http\Controllers\Page;

use App;
use URL;
use Cms;
use CLDR;
use View;
use Domain;
use Utility;
use SmartView;
use Exception;
use Translator;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Libraries\Model\IntegrityException;
use Illuminate\Database\Eloquent\Collection;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Temp;
use App\Models\Company;
use App\Models\Property;
use App\Models\Facility;
use App\Models\FacilityPrice;
use App\Models\Meta;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Sandbox;
use App\Models\Blog;
use App\Models\Career;
use App\Models\CareerAppointment;
use App\Models\Subscription;
use App\Models\User;

class PageController extends Controller
{

    public function index(Request $request, $slug = null)
    {

        $currentDomain = Cms::landingCCTLDDomain();

        $temp = new Temp();
        $meta = new Meta();
        $company = new Company();
        $property = new Property();
        $facility = new Facility();
        $facility_price = new FacilityPrice();
        $slug = str_replace('-', '_', $slug);

        $property_menus = $temp->getPropertyMenuCountrySortByOccupancy($currentDomain);

        $property_id = Arr::first(array_keys(Arr::first($property_menus, null, array())));

        $facility_category_slug = Arr::last(explode($meta->delimiter, $slug));
        $facility_category = null;
        $location = $property->getLocationsMenuWithStatePlaceByCountry($currentDomain); // passed in $currentDomain to get data by default country
        $sandbox = new Sandbox();

        $view = sprintf('%s', (strcasecmp($slug, $meta->delimiter) == 0) ? 'index' : $slug);
        
        $country_needle = $request->segment(1);
        
        if(Cms::isSupportCCTLDDomain($country_needle)){
        	
        	$redirect_url = Cms::cctldDomainInfo($country_needle);
        	return redirect($redirect_url['url']);
        	
        }
	    
        if(!View::exists(SmartView::createPath($view))){
            return Utility::httpExceptionHandler(404);
        }

        foreach( Utility::constant('facility_category') as $category ){

            if( strcasecmp($category['view']['package'], $facility_category_slug ) == 0 ){

                $facility_category = $category['slug'];

            }

        }


        $facility_price = $property->getActiveFacilitySubscriptionPriceForPackagePage($property_id, $facility_category);

        $officeContainerByState = [];

        foreach ($location as $country) {
            foreach ($country['states'] as $state) {

                // Search for existing key and get the index if exist.
                $key = array_search($state['name'], array_column($officeContainerByState, 'name'));

                // Set default data if not exist.
                if (is_bool($key) && $key === FALSE) {
                    array_push($officeContainerByState, [
                        'name' => $state['name'],
                        'office_count' => 0
                    ]);

                    $key = count($officeContainerByState) - 1;
                }

                // Iterate over all the offices by state, as one state got more than one offices,
                // find the active office(but not coming soon)
                foreach ($state['office'] as $office) {
                    // if the office is active(not coming soon) really exist, increase the count,
                    // as we need to show them to the front page.
                    if ($office->status == 1) {
                        $officeContainerByState[$key]['office_count']++;
                    }
                }
            }
        }

        // Sum all the count, if got more than 0, then we knew that the active office is exist
        $isGotActiveOffice = !empty($officeContainerByState) ? collect($officeContainerByState)->sum('office_count') : null;

        $booking = new Booking();
        $booking->type = 0;

        return SmartView::render($view, compact($temp->singular(), $company->singular(), $property->singular(), 'property_menus', $facility->singular(), $facility_price->singular(), 'location', 'sandbox', 'isGotActiveOffice', 'booking'));

    }

    public function officeState(Request $request, $country, $state)
    {
        try {

            $property = new Property();

            $try_to_get_country_code = CLDR::getCountryCodeByName($country);

            if(Utility::hasString($try_to_get_country_code)){
                $country = $try_to_get_country_code;
            }

            $country_code = strtolower($country);
            $country_name = CLDR::getCountryByCode($country);
            $state_name = $property->convertFriendlyUrlToName($state);
            ${$property->plural()} = $property->showAllActiveByCountryAndState($country, $state, ['price' => 'DESC', 'occupancy' => 'ASC']);
            $sandbox = new Sandbox();

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('locations.state', compact('country', 'country_code', 'country_name', 'state', 'state_name', $property->plural(), $sandbox->singular()));

    }

    public function searchSpace(Request $request)
    {
        try {

//            $property = new Property();
//
//            $locationId = $request->input('location');
//            $location = $property->getOneOrFail($locationId);
//            $country = $location->country;
//            $state = $location->state_slug;
//            $try_to_get_country_code = CLDR::getCountryCodeByName($country);
//
//            if(Utility::hasString($try_to_get_country_code)){
//                $country = $try_to_get_country_code;
//            }
//
//            $country_code = strtolower($country);
//            $country_name = CLDR::getCountryByCode($country);
//            $state_name = $property->convertFriendlyUrlToName($state);
//            ${$property->plural()} = $property->showAllActiveByCountryAndState($country, $state, ['price' => 'DESC', 'occupancy' => 'ASC'],true, $locationId );
//
//            $sandbox = new Sandbox();


//            $property = new Property();
//            $meta = new Meta();
//
//            $location = $property->getOneOrFail($locationId);
//            $country = $location->country;
//            $state = $location->state_slug;
//
//            $try_to_get_country_code = CLDR::getCountryCodeByName($country);
//
//            if(Utility::hasString($try_to_get_country_code)){
//                $country = $try_to_get_country_code;
//            }
//
//            $country_code = strtolower($country);
//            $country_name = CLDR::getCountryByCode($country);
//            $state_name = $property->convertFriendlyUrlToName($state);
//            ${$property->singular()} = $property->getOneOrFailBySuffixSlug(join($meta->delimiter, [$country, $state, $slug]));
//            $sandbox = new Sandbox();
            $property = new Property();
            $locationId = $request->input('location');

            $location = $property->getOneOrFail($locationId);


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->to($location->metaWithQuery->full_url_with_current_root);
    }

    public function officeHome(Request $request, $country, $state, $slug)
    {
        try {

            $property = new Property();
            $meta = new Meta();

            $try_to_get_country_code = CLDR::getCountryCodeByName($country);

            if(Utility::hasString($try_to_get_country_code)){
                $country = $try_to_get_country_code;
            }

            $country_code = strtolower($country);
            $country_name = CLDR::getCountryByCode($country);
            $state_name = $property->convertFriendlyUrlToName($state);
            ${$property->singular()} = $property->getOneOrFailBySuffixSlug(join($meta->delimiter, [$country, $state, $slug]));
            $sandbox = new Sandbox();

            $booking = new Booking();

            $booking->setupForNewEntry($property->timezone);

            if( !$property->readyForSiteVisitBooking() ){
                $booking->type = 0;
            }else{
                $booking->type = 1;
            }

        }catch (ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }


        return SmartView::render('locations.property', compact('country', 'country_code', 'country_name', 'state', 'state_name', 'slug', $property->singular(), $sandbox->singular(), $booking->singular()));

    }

    public function booking(Request $request, $id = null, $list = false){
        
        try {
            
            $booking = new Booking();
            $property = (new Property())->findOrFail($id);
            $temp = new Temp();

            $booking->setupForNewEntry($property->timezone);

            if( !$property->readyForSiteVisitBooking() ){
                $booking->type = 0;
            }else{
                $booking->type = 1;
            }

        }catch(ModelNotFoundException $e){
            
            return Utility::httpExceptionHandler(404, $e);
            
        }
        
        return SmartView::render('booking', compact($booking->singular(), $property->singular(), $temp->singular()));
        
    }

    public function bookingAllReadyForSiteVisitOffice(Request $request){

        try {

            $booking = new Booking();
            $property = new Property();
            $temp = new Temp();

            $property->country = $booking->defaultCountry;
            $booking->setupForNewEntry($property->timezone);
            $booking->type = 1;
            $booking->all_site_visit = true;

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render('booking', compact($booking->singular(), $property->singular(), $temp->singular()));

    }

    public function postBooking(Request $request){
        
        try {
            Booking::add($request->except('is_modal'), true, false, true,  Utility::constant('lead_source.website.slug'));

            $property = (new Property())->findOrFail($request->input('location'));
            $bookATour = url($property->metaWithQuery->full_url_with_current_root .'/booking/thank-you');
            $bookATourModal = url($property->metaWithQuery->full_url_with_current_root .'/modal/booking/thank-you');
            $findOutMore = url($property->metaWithQuery->full_url_with_current_root .'/find-out-more/thank-you');

            // As venue's page and booking's popup using the same post route
            // we need to differentiate between them for redirection wise
            // because both functionality use same javascript for inserting record
            // is_modal is just a hidden input type exist inside booking form.
            if ($request->exists('is_modal') && $request->has('is_modal')) {
                $link = $bookATourModal;
            } else {
                // differentiate between find out more and book a tour
                if ($request->input('type')) {
                    $link = $bookATour;
                } else {
                    $link = $findOutMore;
                }
            }
            
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){
            
            $this->throwValidationException(
                $request, $e->validator
            );
            
        }catch (IntegrityException $e){

            $this->throwIntegrityException(
                $request, $e
            );

        }catch (Exception $e){
            
            return Utility::httpExceptionHandler(500, $e);
            
        }
    
        //return SmartView::render('booking_success');
        return SmartView::render(null, compact('link'));
    }

    public function postBookingTour(Request $request)
    {
        try {
            Booking::addQuickLead($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch (IntegrityException $e){

            $this->throwIntegrityException(
                $request, $e
            );

        }catch (Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }

    public function contactUs(Request $request){

        try {

            $company = (new Temp())->getCompanyDefault();
            $contact = new Contact();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($company->singular(), $contact->singular()));

    }

    public function postContactUs(Request $request){

        try {

            Contact::addWithCaptcha($request->all());

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, array('message' => Translator::transSmart("app.THANK YOU FOR CONTACTING US. WE WILL RESPOND TO YOU AS SOON AS POSSIBLE.", "THANK YOU FOR CONTACTING US. WE WILL RESPOND TO YOU AS SOON AS POSSIBLE.")));

    }

    public function packagePrice(Request $request, $property_id = null, $category = null){

    	$property = (new Property())->find($property_id);
        $facility_price = (new Property())->getActiveFacilitySubscriptionPriceForPackagePage($property_id, $category);
		
        if(is_null($property)){
        	$property = new Property();
        }
        
        return SmartView::render(true, compact($property->singular(), $facility_price->singular()));

    }

    public function privacy(Request $request){

        try {



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null);

    }

    public function term(Request $request){

        try {



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null);

    }

    public function signIn(Request $request){

        $user = new User();

        return SmartView::render(null,  compact('user'));

    }

    public function signupPrimeMember()
    {
        $user = new User();
        $temp = new Temp();
        $property = new Property();
        $subscription = new Subscription();

        return SmartView::render(null, compact('user', $temp->singular(), $property->singular(), $subscription->singular()));
    }

    public function thankYou()
    {
        return SmartView::render(null);
    }

    public function enterprise(Request $request){

        try {
            $company = (new Temp())->getCompanyDefault();
            $contact = new Contact();

        } catch (ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($company->singular(), $contact->singular()));

    }

    public function postEnterprise(Request $request){

        try {
            Contact::add($request->all());

        } catch (ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);

    }

    public function locations(Request $request){

        try {

            $currentDomain = Cms::landingCCTLDDomain();

            $property = new Property();
            $popularLocations = $property->showAllActiveByPopularByCountry( $currentDomain );
            $newestSpaces = $property->showAllActiveByNewestByCountry( $currentDomain );
            $comingSoon = $property->showAllComingSoonByCountry( $currentDomain );
            $sandbox = new Sandbox();
            $temp = new Temp();

            $location = $property->getLocationsMenuWithStatePlaceByCountry( $currentDomain );
            $filteredState = new Collection();

            if(isset($location[Str::upper($currentDomain)]['states'])){


                $filteredState = collect($location[Str::upper($currentDomain)]['states'])->filter(function($value, $key) {
                    if($value['state_model']->active_status) {
                        return $value;
                    }
                })->all();

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact(['popularLocations', 'newestSpaces', 'comingSoon', 'sandbox', 'property', 'temp', 'location', 'filteredState']));
    }

    public function primeMemberThankYou()
    {
        return Smartview::render('page.thank_you.signup_prime_member');
    }

    public function agentThankYou()
    {
        return Smartview::render('thank_you.agents');
    }

    public function enterpriseThankYou()
    {
        return Smartview::render('thank_you.enterprise');
    }

    public function locationThankYou()
    {
        return Smartview::render('thank_you.book_a_tour');
    }

    public function modalLocationThankYou()
    {
        return Smartview::render('thank_you.modal_book_a_tour');
    }

    public function bookingThankYou()
    {
        return Smartview::render('thank_you.booking');
    }

    public function findOutMoreThankYou()
    {
        return Smartview::render('thank_you.find_out_more');
    }

    public function blogs(Request $request)
    {
        try {
            $blogs = (new Blog())->showActiveOrInactiveBlog();
            $blogObject = new Blog;
            $sandbox = new Sandbox;

        } catch (ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('blogs', 'blogObject', 'sandbox'));
    }

    public function blogDetail($slug)
    {
        try {
            $blog = (new Blog())->getOneOrFailBySuffixSlug($slug);
            $sandbox = new Sandbox;

        } catch (ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('blog', 'sandbox'));
    }

    public function careers(Request $request){

        try {

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null);

    }

    public function jobs(Request $request)
    {
        try {

            $jobs = (new Career())->showActiveOrInactiveJob();
            $jobObject = new Career();
            $sandbox = new Sandbox;

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('jobs', 'jobObject', 'sandbox'));
    }

    public function jobDetail($slug)
    {
        try {
            $job = (new Career())->getOneOrFailBySuffixSlug($slug);
            $sandbox = new Sandbox;

        } catch (ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact('job', 'sandbox'));
    }

    public function jobContact($id)
    {
        try {
            $job = Career::retrieve($id);
            $careerAppointment = new CareerAppointment();

        } catch (ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('job', 'careerAppointment'));
    }

    public function postJobContact(Request $request)
    {
        try {
            CareerAppointment::add($request->all());

        } catch(ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }

    public function jobThankYou()
    {
        return Smartview::render('thank_you.jobs');
    }


    public function agents()
    {
	    $user = new User();
	    $company = new Company();
	
	    $activeMenus = (new Temp())->getPropertyMenuAll();
	
	    $properties = [];
	
	    foreach ($activeMenus as $countries) {
		    foreach ($countries as $office) {
			    $properties[] = $office;
		    }
	    }
	
	
	    return Smartview::render(null, compact($user->singular(), $company->singular(), 'properties'));
	
    }
    
    public function bookingTourThankYou()
    {
        return Smartview::render('thank_you.booking_tour');

    }
    
}
