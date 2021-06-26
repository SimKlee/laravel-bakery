<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AbstractModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AbstractResource extends JsonResource
{
    protected bool $withDates = false;

    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return $this->withTimestamps($request, parent::toArray($request));
    }

    private function withTimestamps(Request $request, array $result): array
    {
        if ($this->withDates || ($request->has('timestamps') && $request->get('timestamps')) == 1) {
            $result[ AbstractModel::CREATED_AT ] = $this->created_at->format('Y-m-d H:i:s');
            $result[ AbstractModel::UPDATED_AT ] = $this->updated_at->format('Y-m-d H:i:s');

            return $result;
        }

        /** @var AbstractModel $this */
        if (isset($this->timestamps) && $this->timestamps) {
            unset($result[ AbstractModel::CREATED_AT ]);
            unset($result[ AbstractModel::UPDATED_AT ]);
        }

        return $result;
    }
}
