<?php

namespace App\Http\Livewire;

use App\Services\PlatformsService;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Platform;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class PlatformTable extends DataTableComponent
{
    protected $model = Platform::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function bulkActions(): array
    {
        return [
            'delete' => trans('delete_object' , ['object' => trans('platform')]),
        ];
    }

    public function delete()
    {
        try {
            $platforms = $this->getSelected();
            PlatformsService::deleteMultiply($platforms);
            $this->clearSelected();
        } catch (\Exception $exception) {}
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->searchable()
                ->sortable(),
            Column::make("Provider", "provider")
                ->searchable()
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            ButtonGroupColumn::make('Actions')
                ->attributes(function($row) {
                    return [
                        'class' => 'space-x-2',
                    ];
                })
                ->buttons([
                    LinkColumn::make('Edit')
                        ->title(fn($row) => trans('edit_object' , ['object' => $row->name]) )
                        ->location(fn($row) => route('platform.edit', $row))
                        ->attributes(function($row) {
                            return [
                                'class' => 'underline text-blue-500 hover:no-underline',
                            ];
                        }),
                ]),
        ];
    }
}
