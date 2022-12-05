<?php

namespace App\Http\Livewire;

use App\Models\App;
use App\Models\Run;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Subscription;

class SubscriptionTable extends DataTableComponent
{
    public Run|App $object ;

    public function mount(mixed $object)
    {
        $this->object = $object;
    }

    public function builder(): Builder
    {
        return $this->object->subscriptions()->getQuery();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable()
                ->sortable(),
            Column::make("Application", "app_id")
                ->format(
                    fn($value, $row, Column $column) => $row->app->name
                )
                ->sortable(),
            Column::make("Run", "run_id")
                ->format(
                    fn($value, $row, Column $column) => $row->run->name
                )
                ->sortable(),
            Column::make("Status", "status")
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => view('layouts.status', compact('value'))
                )
                ->sortable(),
        ];
    }
}
