<?php

namespace App\Constants;

class GatewayFeeConstant
{
    // VAT rate in Philippines
    public const VAT_RATE = 0.12; // 12%

    // Xendit Gateway Fees — verified from https://www.xendit.co/en-ph/pricing/ (Jul 2026)
    // NOTE: Xendit also charges a fixed PHP 11.00 processing fee per transaction on top of
    //       the payment method fee below. That fixed fee is NOT included here because the
    //       dashboard deducts fees from aggregate profit (not per-transaction), so the
    //       percentage-based portion is the meaningful figure for profit reporting.
    // Format: ['percentage' => rate, 'fixed' => amount, 'min' => minimum_fee]
    public const XENDIT_FEES = [
        // E-Wallets
        'gcash'          => ['percentage' => 0.030, 'fixed' => 0, 'min' => 0],    // 3.0% (E-Wallet)
        'grab-pay'       => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
        'grabpay'        => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
        'shopeepay'      => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
        'shopee-pay'     => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
        'paymaya'        => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0% (Maya E-Wallet)
        'maya'           => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0% (Maya E-Wallet)
        // QR Code
        'qrph'           => ['percentage' => 0.015, 'fixed' => 0, 'min' => 15],   // 1.5% min ₱15
        'qr-ph'          => ['percentage' => 0.015, 'fixed' => 0, 'min' => 15],   // 1.5% min ₱15
        // Direct Debit (Online Banking)
        'bpi'            => ['percentage' => 0.013, 'fixed' => 0, 'min' => 15],   // 1.3% min ₱15
        'ubp'            => ['percentage' => 0.013, 'fixed' => 0, 'min' => 15],   // 1.3% min ₱15
        'rcbc'           => ['percentage' => 0.013, 'fixed' => 0, 'min' => 15],   // 1.3% min ₱15
        'chinabank'      => ['percentage' => 0.013, 'fixed' => 0, 'min' => 15],   // 1.3% min ₱15
        // Cards
        'credit-card'    => ['percentage' => 0.035, 'fixed' => 0, 'min' => 0],    // 3.5% (domestic)
        'debit-card'     => ['percentage' => 0.035, 'fixed' => 0, 'min' => 0],    // 3.5% (domestic)
        'card'           => ['percentage' => 0.035, 'fixed' => 0, 'min' => 0],    // 3.5% (domestic)
        // Over The Counter
        'lbc'            => ['percentage' => 0, 'fixed' => 25, 'min' => 0],       // ₱25 flat
        'cebuana'        => ['percentage' => 0, 'fixed' => 25, 'min' => 0],       // ₱25 flat
        // Buy Now Pay Later
        'billease'       => ['percentage' => 0.015, 'fixed' => 0, 'min' => 0],    // 1.5%
    ];

    // MPay Gateway Fees — verified from mPay Philippines (Jul 2026)
    // mPay GCash: 1.5% per transaction (user-confirmed)
    public const MPAY_FEES = [
        'ph_gcash_url'   => ['percentage' => 0.015, 'fixed' => 0, 'min' => 0],    // 1.5% (mPay GCash)
        'gcash'          => ['percentage' => 0.015, 'fixed' => 0, 'min' => 0],    // 1.5% (mPay GCash fallback)
        'qrph'           => ['percentage' => 0.015, 'fixed' => 0, 'min' => 15],   // 1.5% min ₱15
        'paymaya'        => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
        'grabpay'        => ['percentage' => 0.020, 'fixed' => 0, 'min' => 0],    // 2.0%
    ];

    // Manual payments have no gateway fees
    public const MANUAL_FEES = [
        'gpds-coin'      => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'balance'        => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'wise'           => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'usdt'           => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'paypal'         => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'cod'            => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
    ];

    /**
     * Calculate gateway fee for a given amount and payment method
     */
    public static function calculateGatewayFee(float $amount, ?string $vendor, ?string $slug): float
    {
        if (!$vendor || !$slug) {
            return 0;
        }

        $slug = strtolower($slug);
        $vendor = strtolower($vendor);

        // Get fee structure based on vendor
        $feeStructure = null;

        if ($vendor === 'xendit') {
            $feeStructure = self::findFeeStructure(self::XENDIT_FEES, $slug);
        } elseif ($vendor === 'mpay') {
            $feeStructure = self::findFeeStructure(self::MPAY_FEES, $slug);
        } elseif ($vendor === 'manual') {
            return 0; // No gateway fees for manual / GamerBlitz Coin payments
        }

        if (!$feeStructure) {
            // Default to 2% if unknown vendor/method (conservative estimate)
            return $amount * 0.02;
        }

        // Calculate fee: (amount * percentage) + fixed
        $calculatedFee = ($amount * $feeStructure['percentage']) + $feeStructure['fixed'];

        // Apply minimum fee if applicable
        if ($feeStructure['min'] > 0 && $calculatedFee < $feeStructure['min']) {
            $calculatedFee = $feeStructure['min'];
        }

        return round($calculatedFee, 2);
    }

    /**
     * Calculate VAT on gateway fee
     */
    public static function calculateVatOnFee(float $gatewayFee): float
    {
        return round($gatewayFee * self::VAT_RATE, 2);
    }

    /**
     * Calculate net profit after gateway fees and VAT
     */
    public static function calculateNetProfit(float $grossProfit, float $turnover, ?string $vendor, ?string $slug): array
    {
        $gatewayFee = self::calculateGatewayFee($turnover, $vendor, $slug);
        $vatOnFee = self::calculateVatOnFee($gatewayFee);
        $totalDeductions = $gatewayFee + $vatOnFee;
        $netProfit = $grossProfit - $totalDeductions;

        return [
            'gross_profit'     => round($grossProfit, 2),
            'gateway_fee'      => $gatewayFee,
            'vat_on_fee'       => $vatOnFee,
            'total_deductions' => round($totalDeductions, 2),
            'net_profit'       => round($netProfit, 2),
        ];
    }

    /**
     * Find fee structure by slug (supports partial matching)
     * Checks direct match first, then checks if the slug contains a key or vice versa.
     */
    private static function findFeeStructure(array $fees, string $slug): ?array
    {
        // Direct match (most specific — always wins)
        if (isset($fees[$slug])) {
            return $fees[$slug];
        }

        // Partial match: slug contains the key (e.g. "ubp_direct_debit" contains "ubp")
        foreach ($fees as $key => $structure) {
            if (str_contains($slug, $key)) {
                return $structure;
            }
        }

        // Partial match: key contains the slug (e.g. key "grab-pay" contains slug "grab")
        foreach ($fees as $key => $structure) {
            if (str_contains($key, $slug)) {
                return $structure;
            }
        }

        return null;
    }
}
