<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTugboatRequest;
use App\Http\Requests\UpdateTugboatRequest;
use App\Http\Resources\TugboatResource;
use App\Models\TugBoat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\QueryBuilder;

class TugboatController extends Controller
{
    public function __construct(Request $request)
    {
        $this->setConstruct($request, TugBoatResource::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $tugboat = QueryBuilder::for(TugBoat::class)
            ->allowedFilters(['id', 'name'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($tugboat);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTugBoatRequest $request
     * @return Response
     */
    public function store(StoreTugBoatRequest $request)
    {
        $tugboat = TugBoat::create($request->all());

        return $this->resource($tugboat);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $tugboat = TugBoat::find($id);

        if(!$tugboat)
            return $this->error(404, Config::get('constants.errors.not_found'));

        return $this->resource($tugboat);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTugboatRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTugBoatRequest $request, $id)
    {
        $tugboat = TugBoat::find($id);

        if(!$tugboat)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $tugboat->update($request->all());

        return $this->resource($tugboat);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $tugboat = TugBoat::find($id);

        if(!$tugboat)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $tugboat->delete();

        return $this->success([], Config::get('constants.success.delete'));
    }
}
