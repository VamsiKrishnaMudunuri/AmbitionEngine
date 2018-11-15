<?php

namespace App\Http\Controllers\Admin\Lead;

use CLDR;
use Utility;
use SmartView;
use Exception;
use App\Models\Lead;
use App\Http\Controllers\Controller;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LeadController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Lead());
    }

    public function index()
    {
        try {
            $defaultCountries = collect(CLDR::getCountries());
            ${$this->plural()} = $this->getModel()->showFromAllOffices();

        } catch (InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->plural(), 'defaultCountries'));
    }
}