@php
    $success = session('success');
    $failed = session('failed');
    $error = session('error');
    $warning = session('warning');
    $info = session('info');
    $status = session('status');
    if ($success) {
        $color = 'success';
        $alert = $success;
    } elseif($failed) {
        $color = 'danger';
        $alert = $failed;
    } elseif($error) {
        $color = 'danger';
        $alert = $error;
    } elseif($warning) {
        $color = 'warning';
        $alert = $warning;
    } elseif($info) {
        $color = 'info';
        $alert = $info;
    } elseif($status) {
        $color = 'info';
        $alert = $status;
    }
@endphp
@if ($success || $failed || $error || $warning || $info || $status)
<div class="alert alert-{{ $color }} alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{ $alert }}
</div>
@endif
