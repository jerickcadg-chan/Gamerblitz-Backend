<?php

namespace App\Constants;

class GatewayFeeConstant
{
    // VAT rate in Philippines
    public const VAT_RATE = 0.12; // 12%

    // Xendit Gateway Fees (based on their pricing page)
    // Format: ['percentage' => rate, 'fixed' => amount, 'min' => minimum_fee]
    public const XENDIT_FEES = [
        'gcash' => ['percentage' => 0.023, 'fixed' => 0, 'min' => 0],           // 2.3%
        'grabpay' => ['percentage' => 0.02, 'fixed' => 0, 'min' => 0],          // 2.0%
        'grab-pay' => ['percentage' => 0.02, 'fixed' => 0, 'min' => 0],         // 2.0%
        'shopeepay' => ['percentage' => 0.02, 'fixed' => 0, 'min' => 0],        // 2.0%
        'shopee-pay' => ['percentage' => 0.02, 'fixed' => 0, 'min' => 0],       // 2.0%
        'paymaya' => ['percentage' => 0.018, 'fixed' => 0, 'min' => 0],         // 1.8%
        'maya' => ['percentage' => 0.018, 'fixed' => 0, 'min' => 0],            // 1.8%
        'qrph' => ['percentage' => 0.014, 'fixed' => 0, 'min' => 15],           // 1.4% or ?15
        'qr-ph' => ['percentage' => 0.014, 'fixed' => 0, 'min' => 15],          // 1.4% or ?15
        'bpi' => ['percentage' => 0.01, 'fixed' => 0, 'min' => 15],             // 1% or ?15
        'ubp' => ['percentage' => 0.01, 'fixed' => 0, 'min' => 15],             // 1% or ?15
        'rcbc' => ['percentage' => 0.01, 'fixed' => 0, 'min' => 15],            // 1% or ?15
        'chinabank' => ['percentage' => 0.01, 'fixed' => 0, 'min' => 15],       // 1% or ?15
        'credit-card' => ['percentage' => 0.032, 'fixed' => 10, 'min' => 0],    // 3.2% + ?10
        'debit-card' => ['percentage' => 0.032, 'fixed' => 10, 'min' => 0],     // 3.2% + ?10
        'card' => ['percentage' => 0.032, 'fixed' => 10, 'min' => 0],           // 3.2% + ?10
        'lbc' => ['percentage' => 0, 'fixed' => 25, 'min' => 0],                // ?25
        'cebuana' => ['percentage' => 0, 'fixed' => 25, 'min' => 0],            // ?25
        'billease' => ['percentage' => 0.015, 'fixed' => 0, 'min' => 0],        // 1.5%
    ];

    // MPay Gateway Fees (adjust based on actual MPay pricing)
    public const MPAY_FEES = [
        'gcash' => ['percentage' => 0.023, 'fixed' => 0, 'min' => 0],           // Assuming similar to Xendit
        'qrph' => ['percentage' => 0.014, 'fixed' => 0, 'min' => 15],
        'paymaya' => ['percentage' => 0.018, 'fixed' => 0, 'min' => 0],
        'grabpay' => ['percentage' => 0.02, 'fixed' => 0, 'min' => 0],
    ];

    // Manual payments have no gateway fees
    public const MANUAL_FEES = [
        'gpds-coin' => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'wise' => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'usdt' => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'paypal' => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
        'cod' => ['percentage' => 0, 'fixed' => 0, 'min' => 0],
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
            return 0; // No gateway fees for manual payments
        }

        if (!$feeStructure) {
            // Default to 2% if unknown (conservative estimate)
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
            'gross_profit' => round($grossProfit, 2),
            'gateway_fee' => $gatewayFee,
            'vat_on_fee' => $vatOnFee,
            'total_deductions' => round($totalDeductions, 2),
            'net_profit' => round($netProfit, 2),
        ];
    }

    /**
     * Find fee structure by slug (supports partial matching)
     */
    private static function findFeeStructure(array $fees, string $slug): ?array
    {
        // Direct match
        if (isset($fees[$slug])) {
            return $fees[$slug];
        }

        // Partial match
        foreach ($fees as $key => $structure) {
            if (str_contains($slug, $key) || str_contains($key, $slug)) {
                return $structure;
            }
        }

        return null;
    }
}