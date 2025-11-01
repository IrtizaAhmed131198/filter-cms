<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('log_activity')) {
    function log_activity($action, $entity, $entityId, $description = null, $changes = null)
    {
        ActivityLog::create([
            'admin_id'     => Auth::id(),
            'entity_type'  => is_object($entity) ? get_class($entity) : $entity,
            'entity_id'    => $entityId,
            'action'       => $action,
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'changes'      => $changes,
        ]);
    }
}
?>
