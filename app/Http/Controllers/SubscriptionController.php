<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Service;
use App\Subscription;
use Validator;


class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Subscription::get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $isValid = $this->validateData($data);
        if($isValid->fails()){
            return $isValid->errors();
        }
        $data['subscription_date'] = ($data['subscription_date']) ? $data['subscription_date'] : date("Y-m-d");
        $newSubscription = Subscription::create($data);
        return $newSubscription;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $subscription = Subscription::whereId($id)->first();

        if($subscription->is_subscribed){

            $subscription->unsubscription_date = date("Y-m-d");
            $subscription->is_subscribed = false;
            $subscription->save();

        }

        return $subscription;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function report($date = NULL){

        $time = strtotime($date);
        $reponse = '';

        // VALIDATE DATE
        if($time){

            $day = date('Y-m-d',$time);
            $reponse = "Fecha -> $day\n";

            // GET THE DAY'S SUBSCRIPTIONS
            $subscriptions = Subscription::whereSubscriptionDate($day)->get();
            $totalSubscriptions = count($subscriptions);

            // GET THE DAY'S UNSUBSCRIPTIONS
            $unsubscriptions = Subscription::whereUnsubscriptionDate($day)->count();


            $totalSubscriptionsActive = 0;
            foreach ($subscriptions as $sub) {
                // if continue subscripted or they unsubscripted after subscription.
                if($sub->is_subscribed || $sub->unsubscription_date > $sub->subscription_date){
                    $totalSubscriptionsActive++;
                }
            }

            $reponse .= "Suscripciones del día: $totalSubscriptions\n";
            $reponse .= "Suscripciones canceladas del día: $unsubscriptions\n";
            $reponse .= "Suscripciones activas al final del día: $totalSubscriptionsActive\n";

        }else{
            $reponse = "Formato de fecha incorrecto ($date).\nDebe ser 'YYYY-mm-dd.'\n";
        }

        return $reponse;
    }

    private function validateData($data) {

		$rules = [
            'user_id' => 'required',
            'service_id' => 'required',
            'subscription_date' => 'date_format:"Y-m-d"|date'
        ];

        $niceNames = [
			'user_id' => 'User',
			'service_id' => 'Service'
		];

        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($niceNames);

        if(!$validator->fails())
        $validator->after(function($validator) {
			$data = $validator->getData();

            $itExistsUser = User::whereId($data['user_id'])->count();
            if(!$itExistsUser){
                $validator->errors()->add('user_id', 'There is no user with id:'.$data['user_id']);
            }

            $itExistsService = Service::whereId($data['service_id'])->count();
            if(!$itExistsService){
                $validator->errors()->add('service_id', 'There is no service with id:'.$data['service_id']);
            }


            $itExists = Subscription::whereUserId($data['user_id'])->whereServiceId($data['service_id'])->whereIsSubscribed(true)->count();
            if($itExists){
                $validator->errors()->add('error', 'There is a subscription with the same userid and serviceid ');
            }

		});

        return $validator;
    }


}
