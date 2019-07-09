<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\User;
use App\Service;
use App\Subscription;
use DateTime;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function createData(){

        $response = array();
        $countUsers = User::count();
        if($countUsers < 50){

            $json = json_decode(file_get_contents('https://randomuser.me/api/?results=300&nat=us&inc=email,name'), true);
            foreach ($json['results'] as $usr) {
                $newUser = new User();
                $newUser->name = $usr['name']['first'];
                $newUser->email = $usr['email'];
                $response[] = array('name' => $newUser->name, 'email' => $newUser->email);
                $newUser->save();
            }
        }

        $countServices = Service::count();
        if($countServices < 1){
            $newService = new Service();
            $newService->name = 'Servicio Test';
            $newService->save();
        }

        $users = User::get();
        $subscriptions = Subscription::get();

        if(count($subscriptions) < 50){

            $service = Service::first();
            $date = '2019-01-01';
            $i = 0;
            foreach ($users as $user) {

                $newSubscription = new Subscription();
                $newSubscription->user_id = $user->id;
                $newSubscription->service_id = $service->id;
                $newSubscription->subscription_date = $date;
                $newSubscription->save();
                $response[] = $newSubscription->id;
                $i++;
                if($i >= rand(3,5)){
                    $date = date('Y-m-d', strtotime($date. ' + 1 day'));
                    $i = 0;
                }

            }

        }

        foreach($subscriptions as $subs){
            $response[] = $subs->id;
        }

        for ($i=0; $i < 100; $i++) {

            $subs = Subscription::inRandomOrder()->first();

            $date1 = new DateTime($subs->subscription_date);
            $date2 = new DateTime();
            $diff = $date1->diff($date2);
            $randDays = rand(1,$diff->days);
            $subs->unsubscription_date = (rand(0,1)) ? date('Y-m-d', strtotime($subs->subscription_date. " + $randDays day")) : $subs->subscription_date;
            $subs->is_subscribed = false;
            $subs->save();

        }





        return json_encode($response);

    }
}
