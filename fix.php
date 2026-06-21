<?php

$tuvs = DB::table('monitoring_chemical_tuvs')->get();
foreach ($tuvs as $tuv) {
    DB::table('monitoring_chemical_details')
        ->where('id', $tuv->monitoring_chemical_detail_id)
        ->update(['chemical_qc_tuv_id' => $tuv->chemical_qc_tuv_id]);
}
DB::statement('DROP TABLE monitoring_chemical_tuvs');
echo 'Done';
