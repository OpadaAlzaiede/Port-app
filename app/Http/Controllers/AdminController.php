<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetUserRegisterationNotificationRequest;
use App\Models\PayloadRequest;
use App\Models\Pier;
use App\Models\PortRequest;
use App\Models\ProcessType;
use App\Models\TugBoat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function getNotifications(GetUserRegisterationNotificationRequest $request)
    {

        $notifications = Auth::user()->unreadNotifications;

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return $notifications;
    }

    public function getStochastic(Request $request)
    {
        $data = [];
        $start_date = $request->get('start_Date') ? $request->get('start_date') : PayloadRequest::query()->min('date');
        $end_date = $request->get('end_date') ?: Carbon::now();
        $processTypes = ProcessType::all();
        $processTypeData = [];
        foreach ($processTypes as $processType) {
            $payloadRequest = PayloadRequest::query()->where('process_type_id', '=', $processType->id)->whereBetween('date', [$start_date, $end_date])->get();
            $processTypeData[$processType->name] = $payloadRequest;
            $enterPortRequest = PortRequest::query()->where('process_type_id', '=', $processType->id)->whereBetween('created_at', [$start_date, $end_date])->get();
            $processTypeData[$processType->name] = $enterPortRequest;
        }
        $data['process_types'] = $processTypeData;
        $payloadRequestCount = PayloadRequest::query()->count('id');
        $enterPortRequestCount = PortRequest::query()->count('id');
        $data['payload_request_count'] = $payloadRequestCount;
        $data['enter_port_request_count'] = $enterPortRequestCount;
        $emptyPiers = Pier::query()->where('status', '=', 1)->count();
        $emptyTugboats = TugBoat::query()->where('status', '=', 1)->count();
        $data['empty_piers'] = $emptyPiers;
        $data['empty_tugboats'] = $emptyTugboats;

        return $data;
    }
}
