<?php

use Illuminate\Database\Seeder;
use App\Libraries\Model\ModelValidationException;
use App\Models\User;

class UsersTableSeeder extends Seeder
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



            $roots = array(
                factory(User::class)->states('martin')->make(),
                factory(User::class)->states('kean')->make()
            );

            foreach($roots as $root){
                $found = (new User())->where('email', '=', $root->email)->first();

                if(!$found) {

                    User::add($root->getAttributes());

                }

            }



        }catch(ModelValidationException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

    }
}
