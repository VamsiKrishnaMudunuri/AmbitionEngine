<?php

namespace App\Http\Controllers\Admin\Commission;

use Sess;
use CLDR;
use Utility;
use Exception;
use SmartView;
use Translator;

use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\CommissionItem;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommissionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        try {
            // Get default countries from punic package
            $defaultCountries = collect(CLDR::getCountries());
            $availableCountries = $defaultCountries;

            if ($request->has('country') && $request->exists('country')) {
                // Filter countries colllection based on search param
                $availableCountries = $availableCountries->filter(function($item, $key) {
                    return $key == request('country') ? $item : false ;
                });
            }

            $commisions = new Commission();

            //Get available commissions on db
            $existingCommissionCountry = $commisions->showAll([], false);
            $commisionsByCountry = $existingCommissionCountry->groupBy('country');
            $countries = new Collection();

            foreach ($availableCountries as $key => $value) {

                $collection = new Collection();
                $collection->put('name', $value);
                $collection->put('code', $key);

                //If no country registered, then create default commision structure
                if (!$commisionsByCountry->has($key)) {
                    $collection->put('is_active', Utility::constant('status.0.slug'));
                } else {
                    $collection->put('is_active', $commisionsByCountry->get($key)->first()->status);
                }

                $countries->push($collection);
            }

            // If for some reason client want limit the result per page
            $page = request()->has('page') ? request('page') : 1;
            $perPage = request()->has('per_page') ? request('per_page') : 15;
            $offset = ($page * $perPage) - $perPage;
            $newCollection = collect($countries);

            // Set custom pagination to result set as the data
            // is not an instance of eloquent
            $countries = new LengthAwarePaginator(
                $newCollection->slice($offset, $perPage),
                $newCollection->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('countries', 'defaultCountries'));
    }

    /**
     * Activate/deactivate country's commission status.
     *
     * @param Request $request
     * @param $countryCode
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postCommissionCountry(Request $request, $countryCode)
    {
        try {
            $commission = new Commission();
            $commissionCountry = $commission->getByCountryCode($countryCode);

            if ($commissionCountry->isEmpty()) {
                $commission->setup($countryCode);

            } else {
                foreach ($commissionCountry->pluck('status', 'id') as $key => $value) {
                    $commission->edit($key, [
                        'status' => $value ? Utility::constant('status.0.slug') : Utility::constant('status.1.slug')
                    ]);
                }
            }

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){
            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }

        if ($request->ajax()) {
            return SmartView::render(null);

        } else {
            $message = Translator::transSmart("app.Country's commission price has been enabled.", "Country's commission price has been enabled.");

            return redirect()->route('admin::commission::country', ['country' => $countryCode])->with(Sess::getKey('success'), $message);
        }
    }

    /**
     * Show list of available commission by particular country and roles.
     *
     * @param Request $request
     * @param $countryCode
     * @return mixed
     */
    public function commissionCountry(Request $request, $countryCode)
    {
        try {

            $country = collect(CLDR::getCountries())->mapWithKeys(function ($item, $index) use ($countryCode) {
                if ($index === $countryCode) {
                    return [
                        'code' => $countryCode,
                        'name' => $item
                    ];
                }
            })->all();

            $commissions = (new Commission())->getByCountryCode($country['code']);

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('country', 'countryCode', 'commissions'));

    }

    public function edit(Request $request, $id)
    {
        try {
            $commissionItem = CommissionItem::retrieve($id);

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('commissionItem'));
    }

    public function postEdit(Request $request, $id)
    {
        try {
            $commissionItem = CommissionItem::edit($id, $request->all());

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelVersionException $e) {
            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::commission::country', $commissionItem->commission->country)->with(Sess::getKey('success'), Translator::transSmart('app.Commission has been updated.', 'Commission has been updated.'));
    }
}