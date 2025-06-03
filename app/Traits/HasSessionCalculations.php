<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\DoctorPatient;

trait HasSessionCalculations
{

public function hasSessionCalculations($template) {
    $total = 0;

    if ($template->recurrence === 'none') {
        return 1;
    }

    if (
        $template->recurrence === 'weekly' &&
        $template->recurrence_days &&
        $template->recurrence_end_date
    ) {
        $start = Carbon::parse($template->created_at)->startOfDay();
        $end = Carbon::parse($template->recurrence_end_date)->endOfDay();

        $recurrenceDays = is_string($template->recurrence_days)
            ? json_decode($template->recurrence_days)
            : $template->recurrence_days;

        while ($start->lte($end)) {
            if (in_array($start->format('l'), $recurrenceDays)) {
                $total++;
            }
            $start->addDay();
        }
    }

    return $total;
}

}
