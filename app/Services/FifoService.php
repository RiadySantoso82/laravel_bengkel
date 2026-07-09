<?php

namespace App\Services;

use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockMovementAllocation;
use Illuminate\Support\Facades\DB;

class FifoService
{
    public static function createBatch($partId, $qty, $buyPrice, $receivedDate)
    {
        return StockBatch::create([
            'part_id' => $partId,
            'qty_in' => $qty,
            'qty_remaining' => $qty,
            'buy_price' => $buyPrice,
            'received_date' => $receivedDate,
        ]);
    }

    public static function allocateOut(StockMovement $movement, $partId, $qtyNeeded)
    {
        $batches = StockBatch::where('part_id', $partId)
            ->where('qty_remaining', '>', 0)
            ->orderBy('received_date')
            ->orderBy('id')
            ->get();

        $remaining = $qtyNeeded;
        $totalCost = 0;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $take = min($remaining, $batch->qty_remaining);
            $costForThis = $take * $batch->buy_price;

            StockMovementAllocation::create([
                'movement_id' => $movement->id,
                'batch_id' => $batch->id,
                'qty_taken' => $take,
                'cost_price' => $costForThis,
            ]);

            $batch->decrement('qty_remaining', $take);
            $totalCost += $costForThis;
            $remaining -= $take;
        }

        return ['total_cost' => $totalCost, 'remaining' => $remaining];
    }
}
