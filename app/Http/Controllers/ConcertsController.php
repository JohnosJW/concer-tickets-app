<?php

namespace App\Http\Controllers;

use App\Concert;
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
}
