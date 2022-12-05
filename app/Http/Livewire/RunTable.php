<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Run;

class RunTable extends DataTableComponent
{
    protected $model = Run::class;

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
            Column::make("Name", "id")
                ->format(
                    fn($value, $row, Column $column) => $row['name']
                )
                ->sortable(),
            Column::make("Expired count", "expired_count")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->searchable()
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => $value->format('Y-m-d')
                ),
        ];
    }
}
