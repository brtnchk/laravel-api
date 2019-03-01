<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 29.10.2018
 * Time: 03:16
 */

namespace App\Services;
use App\Models\Card;
use Stripe\Stripe;
use Stripe\Token;

class PaymentService
{
    // siteSubscriptionPlan => stripeSubscriptionPlan
    const SUBSCRIPTIONS = [
        'monthly_main' => 'monthly_main',
        'yearly_main' => 'yearly_main'
    ];

    public function __construct()
    {
        $this->setUpStripe();
    }

    public function signupSubscribe($user)
    {
        $token = $this->createStripeCard([
            'number' => '4000056655665556',
            "exp_month" => 10,
            "exp_year" => 2019,
            "cvc" => "314"
        ]);

        $user->newSubscription('monthly_main', self::SUBSCRIPTIONS['monthly_main'])->create($token->id);
        return $this->createCard($user, $token->card);
    }

    public function addAdditionalCard($user, $cardData)
    {
        $card = $this->addStripeCard([
            'number' => '4000056655665556',
            "exp_month" => 10,
            "exp_year" => 2019,
            "cvc" => "314"
        ], $user->stripe_id);
        return $this->createCard($user, $card);
    }

    public function updateDefaultCard($user, $card)
    {
        if ($card->default) {
            throw new \DomainException('Card already default.');
        }
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $customer->default_source = $card->stripe_id;
        $customer->save();

        Card::where('default', true)->update(['default' => false]);
        return $card->update(['default' => true]);
    }

    public function updateUserPlan($user, $newPlan)
    {
        $subscription = $user->subscriptions()->first();
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_id);

        $result = \Stripe\Subscription::update($subscription->stripe_id, [
            'cancel_at_period_end' => false,
            'items' => [
                [
                    'id' => $stripeSubscription->items->data[0]->id,
                    'plan' => self::SUBSCRIPTIONS[$newPlan],
                ],
            ],
        ]);

        $subscription->update(['name' => $newPlan]);
        return $result;
    }

    protected function createStripeCard($cardData)
    {
        $data = [
            "card" => [
                "number" => $cardData['number'],
                "exp_month" => $cardData['exp_month'],
                "exp_year" => $cardData['exp_year'],
                "cvc" => $cardData['cvc']
            ]
        ];

        return Token::create($data);
    }

    protected function addStripeCard($cardData, $stripeCustomerId)
    {
        $customer = \Stripe\Customer::retrieve($stripeCustomerId);
        $token = $this->createStripeCard($cardData);
        $customer->sources->create(['source' => $token->id]);
        return $token->card;
    }

    protected function createCard($user, $stripeCard)
    {

         $card = new Card([
             'stripe_id' => $stripeCard['id'],
             'card_brand' => $stripeCard['brand'],
             'card_last_four' => $stripeCard['last4'],
         ]);

         if (0 == $user->cards()->count()) {
             $card->default = true;
         }

         return $user->cards()->save($card);
    }

    protected function setUpStripe()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
}