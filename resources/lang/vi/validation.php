<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => ':attribute cần bằng hoặc lớn hơn :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => ':attribute chưa đúng định dạng mảng',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => ':attribute cần nằm trong khoảng giá trị từ :min đến :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => ':attribute cần phải có giá trị là true hoặc false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => ':attribute không phải ngày.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute không đúng định dạng :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => ':attribute đã có giá trị trùng lặp.',
    'email' => ':attribute chưa đúng dịnh dạng.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => ':attribute không tồn tại.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => ':attribute không chính xác.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => ':attribute không được lơn hơn :max.',
        'file' => ':attribute không được lớn hơn :max kilobytes.',
        'string' => ':attribute không được nhiều hơn :max ký tự.',
        'array' => ':attribute không được nhiều hơn :max phần tử.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute cần có giá trị từ :min trở lên.',
        'file' => ':attribute giá trị nhỏ nhất là :min kilobytes.',
        'string' => ':attribute giá trị nhỏ nhất là :min ký tự.',
        'array' => ':attribute cần phải ít nhất :min phần tử.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute cần phải là dạng số.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => ':attribute không đúng định dạng.',
    'required' => ':attribute không được để trống.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':attribute không được để trông khi :other có giá trị là :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => ':attribute cần :size ký tự.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => ':attribute cần phải bắt đầu bằng: :values',
    'string' => ':attribute cần phải là định dạng chuỗi.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ':attribute đã tồn tại.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => ':attribute chưa đúng định dạng URL.',
    'uuid' => ':attribute cần phải là định dạng UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'password' => 'Mật khẩu',
        'email' => 'Email',
        'verify_code' => 'Mã xác nhận',
        'name' => 'Tên',
        'phone_number' => 'Số điện thoại',
        'warehouse_id' => 'Kho',
        'country' => 'Quốc gia',
        'start_date' => 'Ngày bắt đầu',
        'end_date' => 'Ngày kết thúc',
        'bod' => 'Ngày sinh',
        'status' => 'Trạng thái',
        'facebook_link' => 'Facebook cá nhân',
        'skype_link' => 'Skype cá nhân',
        'remember' => 'Duy trì tài khoản',
        'service' => 'Dịch vụ',
        'customer_delivery_id' => 'Địa chỉ nhận hàng',
        'is_woodworking' => 'Đóng gỗ',
        'is_tax' => 'Khai thuế',
        'is_shock_proof' => 'Chống shock',
        'note_for_organization' => 'Ghi chú cho Thương Đô',
        'private_note' => 'Ghi chú riêng',
        'delivery_type' => 'Loại vận chuyển',
        'is_inspection' => 'Kiểm hàng',
        'order_cost' => 'Giá trị đơn hàng',
        'total_amount' => 'Tổng giá trị',
        'inspection_cost' => 'Phí kiểm hàng',
        'woodworking_cost' => 'Phí đóng gỗ',
        'discount_cost' => 'Triết khấu',
        'order_fee' => 'Phí đặt hàng',
        'products' => 'Sản phẩm',
        'products.*.name' => 'Tên sản phẩm',
        'products.*.url' => 'Link sản phẩm',
        'products.*.image' => 'Hình ảnh sản phẩm',
        'products.*.note' => 'Ghi chú sản phẩm',
        'products.*.unit_price_cny' => 'Đơn giá sản phẩm',
        'products.*.quantity' => 'Số lượng sản phẩm',
        'products.*.amount_cny' => 'Giá sản phẩm',
        'products.*.supplier' => 'Nhà cung cấp sản phẩm',
        'products.*.category_id' => 'Danh mục sản phẩm',
        'products.*.order_detail_id' => 'Sản phẩm',
        'supplier' => 'Nhà cung cấp',
        'district_id' => 'Quận/Huyện',
        'province_id' => 'Tỉnh/Thành',
        'receiver' => 'Người nhận',
        'address' => 'Địa chỉ',
        'images_received' => 'Ảnh hàng nhận được',
        'images_received.*' => 'Ảnh hàng nhận được',
        'images_bill' => 'Ảnh hàng vận đơn',
        'images_bill.*' => 'Ảnh hàng vận đơn',
        'solution_id' => 'Giải pháp',
        'complain_type_id' => 'Loại khiếu nại',
        'packages' => 'Danh sách kiện hàng',
        'packages.*' => 'Kiện hàng',
        "type" => 'Hình thức vận chuyển',
        "transporter_id" => 'Loài hình vận chuyển',
        "transporter_detail_id" => 'Nhà xe',
    ],

];
