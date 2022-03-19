<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessTypeRequest;
use App\Http\Requests\UpdateProcessTypeRequest;
use App\Http\Resources\ProcessTypeResource;
use App\Models\ProcessType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class ProcessTypeController extends Controller
{

    /**
     * ProcessTypeController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->setConstruct($request, ProcessTypeResource::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $processTypes = QueryBuilder::for(ProcessType::class)
            ->allowedIncludes(['enterPortRequest', 'payloadRequests'])
            ->allowedFilters('name')
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($processTypes);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreProcessTypeRequest $request
     * @return Response
     */
    public function store(StoreProcessTypeRequest $request)
    {
        $processType = ProcessType::create($request->validated());

        return $this->resource($processType->load([
            'enterPortRequest',
            'payloadRequests'
        ]));
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $processType = ProcessType::find($id);
        if (!$processType) {
            return $this->error('401', 'NOT FOUND!');
        }

        return $this->resource($processType->load([
            'enterPortRequest',
            'payloadRequests'
        ]));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateProcessTypeRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateProcessTypeRequest $request, $id)
    {
        $processType = ProcessType::find($id);
        if (!$processType) {
            return $this->error('401', 'NOT FOUND!');
        }

        $processType->update($request->validated());
        return $this->resource($processType->load([
            'enterPortRequest',
            'payloadRequests'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $processType = ProcessType::find($id);
        if (!$processType) {
            return $this->error('401', 'NOT FOUND!');
        }

        $processType->delete();
        return $this->success([], 'deleted Successfully');
    }
}
