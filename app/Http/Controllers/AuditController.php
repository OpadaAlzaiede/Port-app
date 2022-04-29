<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditResource;
use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuditController extends Controller
{
    /**
     * AuditController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->setConstruct($request, AuditResource::class);
    }

    public function getAudits(Request $request)
    {
        $startDate = $request->start_date ?: Audit::min('created_at');
        $endDate = $request->end_date ?: Carbon::now();
        $audits = QueryBuilder::for(Audit::class)
            ->allowedFilters([AllowedFilter::partial('auditable_type'), 'event'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->defaultSort('-id')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        return $this->collection($audits);
    }
}
