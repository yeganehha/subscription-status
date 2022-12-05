@if ( $value == \App\Enums\StatusEnum::Active)
    <span class="badge bg-success">Active</span>
@elseif ( $value == \App\Enums\StatusEnum::Pending)
    <span class="badge bg-warning">Pending</span>
@else
    <span class="badge bg-danger">Expired</span>
@endif
