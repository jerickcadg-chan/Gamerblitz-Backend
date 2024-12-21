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
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BalanceHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Balance whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBalance {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $balance_id
 * @property string $balanceable_type
 * @property int $balanceable_id
 * @property string $description
 * @property float $amount
 * @property float $latest_balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Balance $balance
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereLatestBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBalanceHistory {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $host
 * @property string $logo
 * @property string|null $description
 * @property string $user_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductItemClient> $productItemClients
 * @property-read int|null $product_item_clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUserToken($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClient {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $payment_method_id
 * @property float $amount
 * @property float $unique_code
 * @property float $total_amount
 * @property string $status
 * @property string|null $paid_at
 * @property string $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_raw
 * @property-read mixed $status_translated
 * @property-read \App\Models\PaymentMethod $paymentMethod
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereUniqueCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deposit whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDeposit {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $client_id
 * @property string $name
 * @property string|null $code
 * @property string|null $description
 * @property float $nominal
 * @property string $disc_type
 * @property string $product_type
 * @property string $start_date
 * @property string $end_date
 * @property int $is_active
 * @property int $maximum
 * @property int $used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read mixed $discount
 * @property-read mixed $product_type_desc
 * @property-read string $status
 * @property-read mixed $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DiscountProduct> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount active()
 * @method static \Database\Factories\DiscountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereDiscType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUsed($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDiscount {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $discount_id
 * @property string $productable_type
 * @property int $productable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Discount $discount
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $productable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereProductableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereProductableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDiscountProduct {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $client_id
 * @property string $code
 * @property int|null $user_id
 * @property int $product_item_id
 * @property int|null $discount_id
 * @property string|null $cust_account
 * @property int $cust_phone_number
 * @property string|null $cust_email
 * @property string|null $payment_method
 * @property string $payment_status
 * @property string $order_status
 * @property int $qty
 * @property string $price
 * @property string $capital
 * @property string $admin_fee
 * @property string $discount_price
 * @property string $total_price
 * @property string $total_income
 * @property string|null $payment_url
 * @property string|null $payment_code
 * @property string|null $payment_id
 * @property string|null $vexa_invoice
 * @property string|null $note
 * @property string|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Discount|null $discount
 * @property-read mixed $cust_account_format
 * @property-read mixed $order_status_raw
 * @property-read mixed $order_status_translated
 * @property-read mixed $payment_status_raw
 * @property-read mixed $payment_status_translated
 * @property-read mixed $payment_url_full
 * @property-read mixed $settlement_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \App\Models\ProductItem $productItem
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Voucher|null $voucher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Voucher> $vouchers
 * @property-read int|null $vouchers_count
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order settlement()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAdminFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustPhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereVexaInvoice($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrder {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property string $status
 * @property string $type
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrderHistory {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $admin_fee
 * @property string $admin_type
 * @property string|null $vendor
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $admin_fee_translated
 * @property-read mixed $display_name
 * @property-read \App\Models\Picture $picture
 * @property-read \App\Models\Picture $picture_order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures_order
 * @property-read int|null $pictures_order_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereAdminFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereAdminType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereVendor($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPaymentMethod {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $pictureable_id
 * @property string $pictureable_type
 * @property string $path
 * @property string $file_name
 * @property string|null $caption
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $url
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $pictureable
 * @method static \Database\Factories\PictureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture wherePictureableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture wherePictureableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Picture whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPicture {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $input_format
 * @property string $category
 * @property string $description
 * @property string|null $company
 * @property string $how_to_order
 * @property string $slug
 * @property string $status
 * @property float $markup_reseller
 * @property float $markup_user
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_slug
 * @property-read mixed $status_view
 * @property-read \App\Models\Picture $picture
 * @property-read \App\Models\Picture $picture_order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures_order
 * @property-read int|null $pictures_order_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductItem> $productItems
 * @property-read int|null $product_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereHowToOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereInputFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMarkupReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMarkupUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProduct {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $client_id
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Picture $picture
 * @property-read \App\Models\Picture $picture_order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures_order
 * @property-read int|null $pictures_order_count
 * @method static \Database\Factories\ProductClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductClient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductClient {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string|null $code
 * @property int $stock
 * @property string $price
 * @property float|null $price_reseller
 * @property string $capital
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductItemClient|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read mixed $discount_price
 * @property-read mixed $real_price
 * @property-read mixed $total_price
 * @property-read float $margin_percentage
 * @property-read float $margin_price
 * @property-read float $margin_price_reseller
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductItemClient> $productItemClients
 * @property-read int|null $product_item_clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Voucher> $vouchers
 * @property-read int|null $vouchers_count
 * @method static \Database\Factories\ProductItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem wherePriceReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductItem {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_item_id
 * @property int $client_id
 * @property string $margin
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\ProductItem $productItem
 * @method static \Database\Factories\ProductItemClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductItemClient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductItemClient {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $client_id
 * @property string $name
 * @property string $url
 * @property string $start_date
 * @property string $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Picture $picture
 * @property-read \App\Models\Picture $picture_order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture> $pictures_order
 * @property-read int|null $pictures_order_count
 * @method static \Database\Factories\SliderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSlider {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $client_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property int|null $phone_number
 * @property string|null $address
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Balance|null $balance
 * @property-read \App\Models\Client|null $client
 * @property-read mixed $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User customer()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User nonCustomer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_item_id
 * @property string $serial_number
 * @property string $password
 * @property string $capital
 * @property string|null $vendor
 * @property string $status
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $password_decrypted
 * @property-read mixed $status_label
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\ProductItem $productItem
 * @method static \Database\Factories\VoucherFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher ready()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voucher whereVendor($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperVoucher {}
}

