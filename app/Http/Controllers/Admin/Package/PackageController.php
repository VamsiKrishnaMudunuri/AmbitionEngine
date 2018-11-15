<?php

namespace App\Http\Controllers\Admin\Package;


use CLDR;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\PackagePrice;


class PackageController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request){

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

            $packagePrice = new PackagePrice();
            // Get available package on db
            $existingPackageCountry = $packagePrice->showAll([], false);
            $packageByCountry = $existingPackageCountry->groupBy('country');
            $countries = new Collection();

            foreach ($availableCountries as $key => $value) {

                $collection = new Collection();
                $collection->put('name', $value);
                $collection->put('code', $key);

                // If no country registered, then create default package price
                if (!$packageByCountry->has($key)) {
                    $collection->put('is_active', Utility::constant('status.0.slug'));
                } else {
                    $collection->put('is_active', $packageByCountry->get($key)->first()->status);
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
     * Show list of available package by particular country.
     *
     * @param Request $request
     * @param $countryCode
     * @return mixed
     */
    public function packageCountry(Request $request, $countryCode){

        try {

            $country = collect(CLDR::getCountries())->mapWithKeys(function ($item, $index) use ($countryCode) {
                if ($index === $countryCode) {
                    return [
                        'code' => $countryCode,
                        'name' => $item
                    ];
                }
            })->all();

            $package_prices = (new PackagePrice())->getByCountryCode($country['code']);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact('country', 'countryCode', 'package_prices'));

    }

    /**
     * Activate/deactivate country's package status.
     *
     * @param Request $request
     * @param $countryCode
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postPackageCountry(Request $request, $countryCode)
    {
        try {
            $packagePrice = new PackagePrice();
            $packageCountry = $packagePrice->getByCountryCode($countryCode);

            if ($packageCountry->isEmpty()) {
                $packagePrice->setup($countryCode);

            } else {
                foreach ($packageCountry->pluck('status', 'id') as $key => $value) {
                    $packagePrice->edit($key, [
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
            $message = Translator::transSmart("app.Country's package price has been enabled.", "Country's package price has been enabled.");

            return redirect()->route('admin::package::country', ['country' => $countryCode])->with(Sess::getKey('success'), $message);
        }

    }

    public function edit(Request $request, $id){

        try {

            $package_price = PackagePrice::retrieve($id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($package_price->singular()));

    }

    public function postEdit(Request $request, $id){

        try {

            PackagePrice::edit($id, $request->all());

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

        return redirect()->route('admin::package::edit', $id)->with(Sess::getKey('success'), Translator::transSmart('app.Package has been updated.', 'Package has been updated.'));

    }


}