<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class LatestActivities extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('dashboard.latest_activities.heading'))
            ->query(fn (): Builder => Activity::query()->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('description')
                    ->label(__('dashboard.latest_activities.description'))
                    ->wrap(),
                TextColumn::make('event')
                    ->label(__('dashboard.latest_activities.event'))
                    ->badge(),
                TextColumn::make('causer.email')
                    ->label(__('dashboard.latest_activities.causer'))
                    ->placeholder('—'),
                TextColumn::make('subject_type')
                    ->label(__('dashboard.latest_activities.subject'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                TextColumn::make('created_at')
                    ->label(__('dashboard.latest_activities.date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->emptyStateHeading(__('dashboard.latest_activities.empty'));
    }
}
