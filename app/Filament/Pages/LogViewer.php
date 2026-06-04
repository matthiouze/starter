<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewer extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.log-viewer';

    /**
     * Taille maximale lue lors de la consultation d'un fichier (256 Ko).
     */
    protected const int MAX_PREVIEW_BYTES = 262144;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Utils::getSuperAdminName()) ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('shield.navigation_group');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::DocumentText;
    }

    public static function getNavigationLabel(): string
    {
        return __('logs.plural_label');
    }

    public function getTitle(): string
    {
        return __('logs.plural_label');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prune')
                ->label(__('logs.prune'))
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $deleted = $this->pruneOldLogs();

                    Notification::make()
                        ->success()
                        ->title(__('logs.pruned', ['count' => $deleted]))
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (?string $search): Collection => $this->getLogFiles($search))
            ->columns([
                TextColumn::make('name')
                    ->label(__('logs.name'))
                    ->searchable()
                    ->icon(Heroicon::OutlinedDocumentText),
                TextColumn::make('size')
                    ->label(__('logs.size'))
                    ->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
                TextColumn::make('modified_at')
                    ->label(__('logs.modified_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                $this->viewAction(),
                $this->downloadAction(),
                $this->deleteAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    $this->deleteBulkAction(),
                ]),
            ])
            ->emptyStateHeading(__('logs.empty'))
            ->emptyStateIcon(Heroicon::OutlinedDocumentText);
    }

    /**
     * @return Collection<string, array{__key: string, name: string, size: int, modified_at: Carbon}>
     */
    protected function getLogFiles(?string $search = null): Collection
    {
        $directory = storage_path('logs');

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn ($file): bool => $file->getExtension() === 'log')
            ->when(filled($search), fn (Collection $files): Collection => $files->filter(
                fn ($file): bool => str_contains(strtolower($file->getFilename()), strtolower($search))
            ))
            ->sortByDesc(fn ($file): int => $file->getMTime())
            ->mapWithKeys(fn ($file): array => [
                $file->getFilename() => [
                    '__key' => $file->getFilename(),
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified_at' => Carbon::createFromTimestamp($file->getMTime()),
                ],
            ]);
    }

    protected function viewAction(): Action
    {
        return Action::make('view')
            ->label(__('actions.view'))
            ->icon(Heroicon::Eye)
            ->modalHeading(fn (array $record): string => $record['name'])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('actions.close'))
            ->fillForm(fn (array $record): array => ['content' => $this->readLogContent($record['name'])])
            ->schema([
                Textarea::make('content')
                    ->hiddenLabel()
                    ->readOnly()
                    ->rows(25)
                    ->extraInputAttributes(['class' => 'font-mono text-xs', 'style' => 'white-space: pre;'])
                    ->columnSpanFull(),
            ]);
    }

    protected function downloadAction(): Action
    {
        return Action::make('download')
            ->label(__('actions.download'))
            ->icon(Heroicon::ArrowDownTray)
            ->action(fn (array $record): BinaryFileResponse => response()->download($this->logPath($record['name'])));
    }

    protected function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('actions.delete'))
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $record): void {
                File::delete($this->logPath($record['name']));

                Notification::make()
                    ->success()
                    ->title(__('logs.deleted'))
                    ->send();
            });
    }

    protected function deleteBulkAction(): BulkAction
    {
        return BulkAction::make('delete')
            ->label(__('actions.delete'))
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $records->each(fn (array $record) => File::delete($this->logPath($record['name'])));

                Notification::make()
                    ->success()
                    ->title(__('logs.deleted'))
                    ->send();
            });
    }

    protected function logPath(string $name): string
    {
        return storage_path('logs/'.basename($name));
    }

    /**
     * Supprime les fichiers de log de plus d'un mois et retourne le nombre supprimé.
     */
    protected function pruneOldLogs(): int
    {
        $directory = storage_path('logs');

        if (! File::isDirectory($directory)) {
            return 0;
        }

        $threshold = now()->subMonth()->getTimestamp();
        $deleted = 0;

        foreach (File::files($directory) as $file) {
            if ($file->getExtension() === 'log' && $file->getMTime() < $threshold) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }

    protected function readLogContent(string $name): string
    {
        $path = $this->logPath($name);

        if (! File::exists($path)) {
            return '';
        }

        $size = File::size($path);

        if ($size <= self::MAX_PREVIEW_BYTES) {
            return File::get($path);
        }

        $handle = fopen($path, 'rb');
        fseek($handle, -self::MAX_PREVIEW_BYTES, SEEK_END);
        $content = stream_get_contents($handle);
        fclose($handle);

        return '… (fichier tronqué, derniers '.Number::fileSize(self::MAX_PREVIEW_BYTES).')'.PHP_EOL.PHP_EOL.$content;
    }
}
