<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePierYardRequest;
use App\Http\Resources\PierYardResource;
use App\Models\PierYard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class PierYardController extends Controller
{


    public function __construct(Request $request)
    {
        $this->setConstruct($request, PierYardResource::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $pierYards = QueryBuilder::for(PierYard::class)
            ->allowedIncludes(['pier', 'yard'])
            ->allowedFilters(['id', 'distance'])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($pierYards);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePierYardRequest $request
     * @param int $id
     * @return void
     */
    public function update(UpdatePierYardRequest $request, $id)
    {
        $pierYard = PierYard::find($id);
        $pierYard->update($request->validated());

        return $this->resource($pierYard);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $pierYard = PierYard::find($id);
        $pierYard->delete();

        return $this->success([], 'deleted Successfully');
    }
}
