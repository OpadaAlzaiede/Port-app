<?php

namespace App\Http\Controllers;

use App\constants\DataBaseConstants;
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
        $processDataDetails = [];
        foreach ($processTypes as $processType) {
            $processData = [];
            $processData["name"] = $processType->name;
            $payloadRequestsCount = PayloadRequest::query()->where('process_type_id', '=', $processType->id)->whereBetween('date', [$start_date, $end_date])->count();
            $processData["payload_requests_count"] = $payloadRequestsCount;
            $enterPortRequestsCount = PortRequest::query()->where('process_type_id', '=', $processType->id)->whereBetween('created_at', [$start_date, $end_date])->count();
            $processData["enter_port_requests_count"] = $enterPortRequestsCount;
            $totalWeight = PortRequest::query()->where('process_type_id', '=', $processType->id)->whereBetween('created_at', [$start_date, $end_date])->sum("payload_weight");
            $processData["total_weight"] = $totalWeight;
            array_push($processDataDetails, $processData);
        }
        $data["process_types"] = $processDataDetails;
        $payloadRequestCount = PayloadRequest::query()->count('id');
        $enterPortRequestCount = PortRequest::query()->count('id');
        //$data['payload_request_count'] = $payloadRequestCount;
        //$data['enter_port_request_count'] = $enterPortRequestCount;
        $emptyPiers = Pier::query()->where('status', '=', 1)->count();
        $emptyTugboats = TugBoat::query()->where('status', '=', 1)->count();
        $data['active_piers'] = $emptyPiers;
        $data['active_tugboats'] = $emptyTugboats;

        /**
         *
         */
        $piers = Pier::all();
        $piersArray = array();
        foreach ($piers as $pier) {
            $pierDetails = array();
            $countOfServedShips = $pier->enterPortPiers()->where('order', '<', 0)->get()->count();
            //$countOfUnServedShips = $pier->enterPortPiers()->where('order', '>', 0)->get()->count();
            $pierDetails['number_of_served_ship'] = $countOfServedShips;
            //$pierDetails['number_of_un_served_ship'] = $countOfUnServedShips;
            $enterPortPiers = $pier->enterPortPiers()->get();
            $countOfLoading = 0;
            $countOfUnLoading = 0;
            $countOfUnLoadingAndLoading = 0;
            foreach ($enterPortPiers as $enterPortPier) {
                if ($enterPortPier->PortRequest->process_type_id === 1) {
                    ++$countOfLoading;
                }
                if ($enterPortPier->PortRequest->process_type_id === 2) {
                    ++$countOfUnLoading;
                }
                if ($enterPortPier->PortRequest->process_type_id === 3) {
                    ++$countOfUnLoadingAndLoading;
                }
            }
            $pierDetails['number_of_loading_operation'] = $countOfLoading;
            $pierDetails['number_of_un_loading_operation'] = $countOfUnLoading;
            $pierDetails['number_of_loading_and_unloading_operation'] = $countOfUnLoadingAndLoading;
            $piersArray[$pier->name] = $pierDetails;
        }
        $data ['piers'] = $piersArray;
        return $data;
    }
}
