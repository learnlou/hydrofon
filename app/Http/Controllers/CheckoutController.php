<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Checkout;
use App\Http\Requests\CheckoutDestroyRequest;
use App\Http\Requests\CheckoutStoreRequest;

class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CheckoutStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutStoreRequest $request)
    {
        $booking = Booking::findOrFail($request->get('booking_id'));
        $booking->checkout()->create([
            'user_id' => $request->user()->id,
        ]);

        flash('Booking was checked out.');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Checkout                             $checkout
     * @param \App\Http\Requests\CheckoutDestroyRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checkout $checkout, CheckoutDestroyRequest $request)
    {
        $checkout->delete();

        flash('Checkout was deleted.');

        return redirect()->back();
    }
}
