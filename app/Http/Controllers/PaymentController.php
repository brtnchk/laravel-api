<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    *
    * @SWG\Get(
    *   path="/api/payment/method",
    *   summary="Get all user cards",
    *   operationId="api.cards.index",
    *   tags={"User Cards"},
    *   @SWG\Parameter(
    *     name="Authorization",
    *     in="header",
    *     description="An authorization header",
    *     required=true,
    *     type="string"
    *   ),
    *   @SWG\Response(response="default", description="successful operation")
    * )
    */
    public function index()
    {
        $cards = [];
        $user = auth()->user();

        if($user->stripe_id)
            $customer = $user->asStripeCustomer();
        else
            return response()->json([], 200);

        foreach ($customer['sources']['data'] as $key => $card)
        {
            $cards[$key] = $card;
            if($customer['default_source'] === $card['id'])
                $cards[$key]['default'] = true;
            else
                $cards[$key]['default'] = false;
        }

        return response()->json($cards, 200);
    }



    /**
    * Store a new user card.
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    *
    * @SWG\Post(
    *   path="/api/payment/method",
    *   summary="Create card",
    *   operationId="api.cards.create",
    *   tags={"User Cards"},
    *   @SWG\Parameter(
    *     name="token",
    *     in="formData",
    *     description="Card token",
    *     required=true,
    *     type="string"
    *   ),
    *   @SWG\Parameter(
    *     name="Authorization",
    *     in="header",
    *     description="An authorization header",
    *     required=true,
    *     type="string"
    *   ),
    *   @SWG\Response(response="default", description="successful operation")
    * )
    */
    public function create(Request $request)
    {
        $user = auth()->user();

        if(!$user->stripe_id)
            $user->createAsStripeCustomer(env('STRIPE_KEY'));

        \Stripe\Stripe::setApiKey( config('services.stripe.secret') );

        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $response = $customer->sources->create(["source" => $request->token]);
        SubscriptionService::createIfNotExist($user, $request->token);

        return response()->json($response, 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Delete(
     *   path="/api/payment/method/{id}",
     *   summary="Delete the card",
     *   operationId="api.cards.destroy",
     *   tags={"User Cards"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of card",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function destroy(Request $request)
    {
        $result = false;

        foreach (auth()->user()->cards() as $card)
        {
            if($card->id === $request->id)
                $result = $card->delete();
        }

        return $result ?
            response()->json($result, 200) :
            response()->json('failed', 404);
    }
}
