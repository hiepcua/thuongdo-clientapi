<?php

use App\Constants\CustomerConstant;

return [
    'withdrawal_not_cancel' => 'Chỉ hủy được yêu cầu rút tiền khi trạng thái là đang '.CustomerConstant::WITHDRAWAL_STATUSES[CustomerConstant::KEY_WITHDRAWAL_STATUS_PENDING]
];