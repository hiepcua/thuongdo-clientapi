<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\ReportLevel;
use App\Models\ReportRevenue;
use Illuminate\Support\Facades\Auth;

class ReportService implements Service
{
    private OrderService $_orderService;

    public function __construct()
    {
        $this->_orderService = new OrderService();
    }

    /**
     * @param  string  $model
     * @param  string  $status
     */
    public function incrementStatus(string $model, string $status)
    {
        (new $model)::query()->first()->increment($status);
    }

    /**
     * @param  string  $model
     * @param  string  $status
     */
    public function decrementStatus(string $model, string $status)
    {
        (new $model)::query()->where('customer_id', Auth::user()->id)->first()->decrement($status);
    }

    /**
     * @param  string  $model
     * @param  string  $statusOld
     * @param  string  $statusNew
     */
    public function changeStatus(string $model, string $statusOld, string $statusNew)
    {
        $this->incrementStatus($model, $statusNew);
        $this->decrementStatus($model, $statusOld);
    }

    /**
     * @param  BaseModel  $model
     * @param  string  $report
     */
    public function updateReportWhenChangeStatus(BaseModel $model, string $report)
    {
        $status = $model->status;
        $oldStatus = $model->getOriginal('status');
        if ($oldStatus === $status) {
            return;
        }
        $this->changeStatus(
            $report,
            $oldStatus,
            $status
        );
    }

    /**
     * @param  int  $level
     * @param  int|null  $oldLevel
     */
    public function reportLevel(int $level, ?int $oldLevel = null): void
    {
        $query = ReportLevel::query()->where(
            ['organization_id' => getOrganization() ?? (new OrganizationService())->getOrganizationDefault()]
        );
        if (is_numeric($oldLevel)) {
            (clone $query)->where(['level' => $oldLevel])->where('quantity', '>', 0)->decrement('quantity');
        }
        (clone $query)->where(['level' => $level])->increment('quantity');
    }

    public function incrementByOrganization(string $model, string $column, ?int $number = 1, ?array $condition = [])
    {
        optional((new $model)::query()->where($condition)->first())->increment($column, $number);
    }

    public function decrementByOrganization(string $model, string $column, ?int $number = 1, ?array $condition = [])
    {
        optional((new $model)::query()->where($condition)->where($column, '>', 0)->first())->decrement($column, $number);
    }

    public function inDecrementByOrganization(string $model, string $inColumn, string $deColumn, ?int $number = 1, ?array $condition = [])
    {
        $this->incrementByOrganization($model, $inColumn, $number, $condition);
        $this->decrementByOrganization($model, $deColumn, $number, $condition);
    }

    /**
     * @param  float  $amount
     * @param  string  $key
     */
    public function incrementByReportRevenue(float $amount, string $key)
    {
        $this->storeReportRevenue($key);
        $this->incrementByOrganization(ReportRevenue::class, 'value', $amount, ['key' => $key]);
    }

    /**
     * @param  float  $amount
     * @param  string  $key
     */
    public function decrementByReportRevenue(float $amount, string $key)
    {
        $this->storeReportRevenue($key);
        $this->decrementByOrganization(ReportRevenue::class, 'value', $amount, ['key' => $key]);
    }

    /**
     * @param  string  $key
     */
    public function storeReportRevenue(string $key)
    {
        ReportRevenue::query()->firstOrCreate(
            ['key' => $key, 'organization_id' => getOrganization(), 'time' => date('Y')]
        );
    }
}