<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnterPortPierResource;
use App\Models\PayloadType;
use App\Models\Pier;
use App\Models\PortPier;
use App\Models\PortRequest;
use App\Models\Yard;
use Carbon\Carbon;
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
        $enterPortPier = DB::table('enter_port_pier')
            ->where('enter_port_request_id', '=', $request->get('enter_port_request_id'))->first();
        $enterPortPierObjectResource = PortPier::find($enterPortPier->id);
        $pier = Pier::find($enterPortPier->pier_id);
        $enterPortPier = DB::table('enter_port_pier')
            ->where('enter_port_request_id', '=', $request->get('enter_port_request_id'))
            ->update(['order' => mt_rand(1, 100) * -1]);
        $currentDateHours = Carbon::now();
        $pierPorts = $pier->enterPortPiers()->get();
        foreach ($pierPorts as $pierPort) {
            $pierPort->order = --$pierPort->order;
            $pierPort->save();
        }
        $currentPortPier = $pier->enterPortPiers()->where('order', '>', 0)->first();
        if ($currentDateHours->diffInRealHours($currentPortPier->enter_date) > 0) {
            $differenceInHours = $currentDateHours->diffInRealHours($currentPortPier->enter_date);
            foreach ($pierPorts as $pierPort) {
                $enter_date = Carbon::parse($pierPort->enter_date);
                $leave_date = Carbon::parse($pierPort->leave_date);
                $pierPort->enter_date = $enter_date->addHours(-$differenceInHours);
                $pierPort->leave_date = $leave_date->addHours(-$differenceInHours);
                $pierPort->save();
            }
        }

        if ($currentDateHours->diffInRealMinutes($currentPortPier->enter_date) > 30 && $currentDateHours->diffInRealMinutes($currentPortPier->enter_date) < 60) {
            $differenceInHours = $currentDateHours->diffInRealMinutes($currentPortPier->enter_date);
            foreach ($pierPorts as $pierPort) {
                $enter_date = Carbon::parse($pierPort->enter_date);
                $leave_date = Carbon::parse($pierPort->leave_date);
                $pierPort->enter_date = $enter_date->addMinutes(-$differenceInHours);
                $pierPort->leave_date = $leave_date->addMinutes(-$differenceInHours);
                $pierPort->save();
            }
        }
        /**
         * to min or max the current capacity of yard
         */
        $toKnowPayloadType = $enterPortPierObjectResource->PortRequest()->first();
        $enterPortRequest = PortRequest::find($toKnowPayloadType->id);
        $enterPortRequestItem = $enterPortRequest->portRequestItems()->get()->count('amount');
        $payloadType = PayloadType::find($toKnowPayloadType->payload_type_id);
        $yard = Yard::find($enterPortPierObjectResource->yard_id);
        if ($payloadType->id === 1) {
            $yard->current_capacity -= $enterPortRequestItem;
        } else {
            $yard->current_capacity += $enterPortRequestItem;
        }
        return $this->resource($enterPortPierObjectResource);
    }
}
