<?php

namespace App\Http\Livewire;

use App\Services\AppsService;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\App;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class AppTable extends DataTableComponent
{
    protected $model = App::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function bulkActions(): array
    {
        return [
            'delete' => trans('delete_object' , ['object' => trans('app')]),
        ];
    }

    public function delete()
    {
        try {
            $apps = $this->getSelected();
            AppsService::deleteMultiply($apps);
            $this->clearSelected();
        } catch (\Exception $exception) {}
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable()
                ->sortable(),
            Column::make("Uid", "uid")
                ->searchable()
                ->sortable(),
            Column::make("Name", "name")
                ->searchable()
                ->sortable(),
            Column::make("Platform", "platform.name")
                ->searchable()
                ->sortable(),
            Column::make("Status", "status")
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => view('layouts.status', compact('value'))
                )
                ->sortable(),
            Column::make("Updated at", "updated_at")
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
                        ->location(fn($row) => route('app.edit', $row))
                        ->attributes(function($row) {
                            return [
                                'class' => 'btn btn-outline-warning',
                            ];
                        }),
                    LinkColumn::make('Subscriptions')
                        ->title(fn($row) => trans('Subscriptions') )
                        ->location(fn($row) => route('subscription.app.index', $row))
                        ->attributes(function($row) {
                            return [
                                'class' => 'btn btn-outline-info',
                            ];
                        }),
                ]),
        ];
    }
}
