<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePierRequest;
use App\Http\Requests\StorePierYardRequest;
use App\Http\Requests\UpdatePierRequest;
use App\Http\Resources\PierResource;
use App\Models\Pier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\QueryBuilder;

class PierController extends Controller
{
    public function __construct(Request $request)
    {
        $this->setConstruct($request, PierResource::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pier = QueryBuilder::for(Pier::class)
            ->allowedFilters(['id', 'name'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($pier);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @paraam StorePierRequest $request
     * @return Response
     */
    public function store(StorePierRequest $request)
    {
        $pier = Pier::create($request->all());

        return $this->resource($pier);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $pier = Pier::find($id);

        if (!$pier)
            return $this->error(404, Config::get('constants.errors.not_found'));

        return $this->resource($pier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePierRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePierRequest $request, $id)
    {
        $pier = Pier::find($id);

        if (!$pier)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $pier->update($request->all());

        return $this->resource($pier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $pier = Pier::find($id);

        if (!$pier)
            return $this->error(404, Config::get('constants.errors.not_found'));

        $pier->delete();

        return $this->success([], Config::get('constants.success.delete'));
    }

    /**
     * @param StorePierYardRequest $request
     * @return mixed
     */
    public function addDistanceBetweenPierAndYards(StorePierYardRequest $request)
    {
        $pier = Pier::find($request->get('pier_id'));
        $yards = $request->get('yards');
        foreach ($yards as $yard) {
            $pier->yards()->attach($pier->id, [
                'yard_id' => $yard['id'],
                'distance' => $yard['distance']
            ]);
        }
        return $this->resource($pier);
    }
}
