<?php

namespace App\Http\Controllers\Member\Affiliate;

use App\Models\Commission;
use Sess;
use Utility;
use SmartView;
use Exception;
use Translator;

use App\Models\Temp;
use App\Models\Lead;
use App\Models\Member;
use App\Models\Booking;
use App\Models\Property;
use App\Models\LeadPackage;
use App\Models\LeadActivity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AffiliateController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Member());
    }

    /**
     * Showing listing of resources.
     *
     * @return mixed
     */
    public function index()
    {
        try {
            $lead = new Lead();
            ${$this->getModel()->singular()} = auth()->user();
            ${$lead->plural()} = $lead->showAllByReferrer(${$this->getModel()->singular()}->id, Utility::constant('commission_schema.user.slug'));

        } catch (\InvalidArgumentException $e) {

            return Utility::httpExceptionHandler(500, $e);

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->getModel()->singular(), $lead->plural()));
    }

    /**
     * Show affiliate(refer friend) form.
     *
     * @return mixed
     */
    public function affiliate()
    {
        try {
            $temp = new Temp();
            $lead = new Lead();
            $leadPackage = new LeadPackage();
            $leadActivity = new LeadActivity();

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('temp', 'lead', 'leadPackage', 'leadActivity'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws IntegrityException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postAffiliate(Request $request)
    {
        try {
            $lead = new Lead();
            $property = new Property();
            $attributes = $request->all();
            $propertyId = $request->input($lead->getTable())[$property->getForeignKey()];
            $userId = auth()->user()->id;
            (new Lead())->refer($propertyId, $userId, Utility::constant('lead_source.member.slug'),Utility::constant('commission_schema.user.slug'), $attributes);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart("app.Successfully refer to your friend. Your Friend will hear from us shortly", "Successfully refer to your friend. Your Friend will hear from us shortly");

        return redirect()->route('member::affiliate::index', [])->with(Sess::getKey('success'), $message);
    }

    public function fees()
    {
        try {
            $commissions = (new Commission())->showAllForActive([], false);

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('commissions'));
    }


    public function affiliateThankYou()
    {
        return SmartView::render(null);
    }

}