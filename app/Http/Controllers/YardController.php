<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreYardRequest;
use App\Http\Requests\UpdateYardRequest;
use App\Http\Resources\YardResource;
use App\Models\Yard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\QueryBuilder;

class YardController extends Controller
{
    public function __construct(Request $request)
    {
        $this->setConstruct($request, YardResource::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $yard = QueryBuilder::for(Yard::class)
            ->allowedFilters(['id', 'size', 'function'])
            ->allowedIncludes(['piers'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($yard);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreYardRequest $request
     * @return JsonResponse
     */
    public function store(StoreYardRequest $request)
    {
        $yard = Yard::create($request->all());

        return $this->resource($yard->load('piers'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $yard = Yard::find($id);

        if (!$yard)
            return $this->error(404, Config::get('constants.errors.not_found'));

        return $this->resource($yard->load('piers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateYardRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateYardRequest $request, $id)
    {
        $yard = Yard::find($id);

        if (!$yard)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $yard->update($request->all());

        return $this->resource($yard->load('piers'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $yard = Yard::find($id);

        if (!$yard)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $yard->delete();

        return $this->success([], Config::get('constants.success.delete'));
    }
}
