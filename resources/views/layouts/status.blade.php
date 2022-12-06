@if ( $value == \App\Enums\StatusEnum::Active)
    <span class="badge bg-success">Active</span>
@elseif ( $value == \App\Enums\StatusEnum::Pending)
    <span class="badge bg-warning">Pending</span>
@elseif ( $value == \App\Enums\StatusEnum::Expired)
    <span class="badge bg-danger">Expired</span>
@elseif ( $value == \App\Enums\RunStatusEnum::Finished)
    <span class="badge bg-success">Finished</span>
@elseif ( $value == \App\Enums\RunStatusEnum::Pending)
    <span class="badge bg-warning">Pending</span>
@endif
