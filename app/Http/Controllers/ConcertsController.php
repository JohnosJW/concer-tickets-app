<?php

namespace App\Http\Controllers;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class ConcertsController
 * @package App\Http\Controllers
 */
class ConcertsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('concerts.index', ['concerts' => Auth::user()->concerts]);
    }

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
        $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'numeric', 'min:5'],
        ]);

        /** @var  $concert */
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

        $concert->publish();

        return redirect()->route('concerts.show', ['id' => $concert]);
    }

    public function update($id)
    {
        /** @var  $concert */
        $concert = Concert::findOrFail($id);

        /** @var  $concert */
        $concert = Concert::update([
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
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}
