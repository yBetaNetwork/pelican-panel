<?php

namespace App\Http\Controllers\Api\Client\Servers;

use App\Http\Controllers\Api\Client\ClientApiController;
use App\Http\Requests\Api\Client\Servers\GetServerRequest;
use App\Models\Server;
use App\Repositories\Daemon\DaemonServerRepository;
use App\Transformers\Api\Client\StatsTransformer;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Cache\Repository;
use Illuminate\Http\Client\ConnectionException;

#[Group('Server', weight: 3)]
class ResourceUtilizationController extends ClientApiController
{
    /**
     * ResourceUtilizationController constructor.
     */
    public function __construct(private Repository $cache, private DaemonServerRepository $repository)
    {
        parent::__construct();
    }

    /**
     * View resources
     *
     * Return the current resource utilization for a server. This value is no longer cached
     * and provides real-time data.
     *
     * @return array<array-key, mixed>
     *
     * @throws ConnectionException
     */
    public function __invoke(GetServerRequest $request, Server $server): array
    {
        $stats = $this->repository->setServer($server)->getDetails();

        return $this->fractal->item($stats)
            ->transformWith($this->getTransformer(StatsTransformer::class))
            ->toArray();
    }
}
