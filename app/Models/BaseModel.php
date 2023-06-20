<?php

namespace App\Models;

use App\Scopes\CustomerCurrentScope;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use Uuid, HasFactory, HasSortDescByCreated;

    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected string $_tableNameFriendly = 'Bản ghi';

    /**
     * @var string
     */
    protected string $_colorLog = '#2B75CC';

    /**
     * Lấy tên bảng dạng thân thiện để hiển thị message
     * @return string
     */
    public function getTableFriendly(): string
    {
        return $this->_tableNameFriendly;
    }

    public function getColorLog(): string
    {
        return $this->_colorLog;
    }

    /**
     * @return ?string
     */
    public function getPrefixRoute(): ?string
    {
        return $this->_prefixRoute ?? null;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CustomerCurrentScope());
    }
}
