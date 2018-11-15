<?php

use Illuminate\Database\Seeder;
use App\Libraries\Model\ModelValidationException;
use App\Models\Mailchimp;

class MailchimpsTableSeeder extends Seeder
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

            
            $mailchimps = array(
                factory(Mailchimp::class)->states('default')->make(),
            );

            foreach($mailchimps as $mailchimp){
                
                $found = (new Mailchimp())
                    ->where('name', '=', $mailchimp->name)
                    ->where('is_default', '=', true)
                    ->first();

                if(!$found) {

                    Mailchimp::add($mailchimp->getAttributes());

                }

            }

        }catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }
}
