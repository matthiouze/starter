<?php

namespace App\Filament\Traits;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

trait HasBreadcrumbLabel
{
    public function getBreadcrumb(): string
    {
        return match (true) {
            $this instanceof ListRecords => __('breadcrumbs.list'),
            $this instanceof CreateRecord => __('breadcrumbs.create'),
            $this instanceof EditRecord => __('breadcrumbs.edit'),
            default => parent::getBreadcrumb(),
        };
    }
}
