<?php

namespace App\Http\Controllers;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class ConcertsController
 * @package App\Http\Controllers
 */
class ConcertsController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        /** @var  $concert */
        $concert = Concert::find($id);
//        $concert = Concert::whereNotNull('published_at')->findOrFail($id);

        return view('concerts.show', [
            'concert' => $concert
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $concert = Concert::create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time')
            ])),
            'ticket_price' => request('ticket_price') * 100,
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
        ])->addtickets(request('ticket_quantity'));

        return redirect()->route('concerts.show', ['id' => $concert]);
    }
}
