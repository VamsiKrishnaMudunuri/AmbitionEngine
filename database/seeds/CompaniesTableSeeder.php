<?php

use Illuminate\Database\Seeder;

use App\Libraries\Model\ModelValidationException;

use App\Models\Company;
use App\Models\Meta;
use App\Models\User;
use App\Models\CompanyUser;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    
        try{
        
            
            $companies = array(
                factory(Company::class)->states('default')->make()
            );
        
            foreach($companies as $company){

                $companyModel = new Company();
                $found = $companyModel
                    ->where('name', '=', $company->getAttributes()[$companyModel->getTable()]['name'])
                    ->where('is_default', '=', true)
                    ->first();
            
                if(!$found) {
                
                    Company::register($company->getAttributes());
                
                }
            
            }
        
        
        
        }catch(ModelValidationException $e){
            
            throw $e;
        
        }catch(Exception $e){
        
        
            throw $e;
        
        }
        
    }
}
