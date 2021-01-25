<?php

namespace Qatras\Surfdata\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Qatras\Surfdata\Http\Controllers\VisitTrackController;


class TrackProcess
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //dd("TrackProcess accessed");
        $response = $next($request);
        $track = new VisitTrackController;
        return $response;
    }

}
