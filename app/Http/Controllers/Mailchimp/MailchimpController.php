<?php

namespace App\Http\Controllers\Mailchimp;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;

use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


use App\Models\Mailchimp;

class MailchimpController extends Controller
{

    public function __construct()
    {
        
        parent::__construct(new Mailchimp());

    }

    public function postSubscribe(Request $request, $id = null){
    
        try {
        
            Mailchimp::subscribe($id, $request->all());
            $message = Translator::transSmart('app.Thank you for Subscribing!', 'Thank you for Subscribing!');
            
        }catch(ModelNotFoundException $e){
    
            return Utility::httpExceptionHandler(404, $e);
    
        }catch(ModelValidationException $e){
    
    
            $this->throwValidationException(
                $request, $e->validator
            );
    
        }catch(IntegrityException $e) {
        
            $this->throwIntegrityException(
                $request, $e
            );
        
        } catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }
    
        return SmartView::render(null, compact('message'));

    }

    public function subscribeThankYou()
    {
        // Require to use view() helper instead of smartview
        // as the thank you page are located under 'page' folder
        // for consistency placed along with other's page.
        // See resources/views/page/thank_you/<page_name>.blade.php
        return view('page.thank_you.subscribe');
    }
    

}
