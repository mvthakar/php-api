<?php

class GstUtils
{
    public static function calculate($products): array
    {
        $totalPriceWithTax = 0;
        $totalPriceWithoutTax = 0;
        $totalCgstAmount = 0;
        $totalSgstAmount = 0;

        $cgstPercentage = 9;
        $sgstPercentage = 9;
        $totalGstPercentage = $cgstPercentage + $sgstPercentage;

        foreach ($products as $product) 
        {
            $priceWithTax = round($product->quantity * $product->price, 2);
            $gstAmount = round($priceWithTax - $priceWithTax / (1.0 + ($totalGstPercentage / 100)), 2);
            $cgstAmount = round($gstAmount * $cgstPercentage / $totalGstPercentage, 2);
            $sgstAmount = round($gstAmount * $sgstPercentage / $totalGstPercentage, 2);
            $priceWithoutTax = round($priceWithTax - $cgstAmount - $sgstAmount, 2);

            $totalPriceWithTax += $priceWithTax;
            $totalCgstAmount += $cgstAmount;
            $totalSgstAmount += $sgstAmount;
            $totalPriceWithoutTax += $priceWithoutTax;
        }

        return [
            "totalPriceWithoutTax" => $totalPriceWithoutTax,
            "cgstPercentage" => $cgstPercentage,
            "totalCgstAmount" => $totalCgstAmount,
            "sgstPercentage" => $sgstPercentage,
            "totalSgstAmount" => $totalSgstAmount,
            "totalPriceWithTax" => $totalPriceWithTax,
        ];
    }
}
