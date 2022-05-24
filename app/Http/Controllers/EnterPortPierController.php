<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnterPortPierResource;
use App\Models\Pier;
use App\Models\PortPier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class EnterPortPierController extends Controller
{


    public function __construct(Request $request)
    {
        $this->setConstruct($request, EnterPortPierResource::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $portPiers = QueryBuilder::for(PortPier::class)
            ->allowedIncludes(['PortRequest', 'Pier'])
            ->allowedFilters(['id', 'enter_date', 'leave_date'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($portPiers);
    }

    public function detachEnterPortPier(Request $request)
    {
        $enterPortPier = DB::table('enter_port_pier')->where('enter_port_request_id', '=', $request->get('enter_port_request_id'))->first();
        $pier = Pier::find($enterPortPier->pier_id);
        $enterPortPier = DB::table('enter_port_pier')->where('enter_port_request_id', '=', $request->get('enter_port_request_id'))->delete();
        $pierPorts = $pier->enterPortPiers()->get();
        foreach ($pierPorts as $pierPort) {
            $pierPort->order = --$pierPort->order;
            $pierPort->save();
        }
    }
}
