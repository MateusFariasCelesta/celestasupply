<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereUpdatedAt($value)
 */
	class CostCenter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_id
 * @property int|null $order_number
 * @property string $original_name
 * @property string $path
 * @property string|null $notes
 * @property int $registered_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $registeredBy
 * @property-read \App\Models\SupplyRequest $supplyRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereRegisteredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereSupplyRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalOrder whereUpdatedAt($value)
 */
	class ExternalOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 */
	class Item extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_item_id
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size_kb
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SupplyRequestItem $item
 * @property-read \App\Models\User $uploadedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereSizeKb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereSupplyRequestItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttachment whereUploadedBy($value)
 */
	class ItemAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_item_id
 * @property numeric $quantity
 * @property string|null $notes
 * @property int $registered_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SupplyRequestItem $item
 * @property-read \App\Models\User $registeredBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereRegisteredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereSupplyRequestItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDelivery whereUpdatedAt($value)
 */
	class ItemDelivery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_id
 * @property \App\Enums\AttachmentType $type
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size_kb
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SupplyRequest $supplyRequest
 * @property-read \App\Models\User $uploadedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereSizeKb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereSupplyRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestAttachment whereUploadedBy($value)
 */
	class RequestAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_id
 * @property \App\Enums\RequestStatus|null $from_status
 * @property \App\Enums\RequestStatus $to_status
 * @property int $changed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $changedBy
 * @property-read \App\Models\SupplyRequest $supplyRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereChangedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereFromStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereSupplyRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereToStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestStatusHistory whereUpdatedAt($value)
 */
	class RequestStatusHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $contact
 * @property bool $isActive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $code
 * @property string $title
 * @property string|null $cost_center_id
 * @property int $user_id
 * @property \App\Enums\Urgency $urgency
 * @property \App\Enums\RequestStatus $status
 * @property string|null $previous_status
 * @property string|null $notes
 * @property string|null $cancellation_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\CostCenter|null $costCenter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExternalOrder> $externalOrders
 * @property-read int|null $external_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupplyRequestItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestStatusHistory> $statusHistory
 * @property-read int|null $status_history_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereCostCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest wherePreviousStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereUrgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequest whereUserId($value)
 */
	class SupplyRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supply_request_id
 * @property int $item_id
 * @property numeric $quantity
 * @property string|null $unit
 * @property string|null $notes
 * @property int|null $supplier_id
 * @property int|null $order_number
 * @property \App\Enums\ItemStatus $status
 * @property \App\Enums\ItemStatus|null $previous_status
 * @property numeric $delivered_quantity
 * @property string|null $cancel_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ItemAttachment|null $attachment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemDelivery> $deliveries
 * @property-read int|null $deliveries_count
 * @property-read \App\Models\Item $item
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\SupplyRequest $supplyRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereCancelReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereDeliveredQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem wherePreviousStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereSupplyRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplyRequestItem whereUpdatedAt($value)
 */
	class SupplyRequestItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $whatsapp_phone
 * @property bool $isActive
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWhatsappPhone($value)
 */
	class User extends \Eloquent {}
}

