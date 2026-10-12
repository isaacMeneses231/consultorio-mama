<?php

namespace App\Filament\Resources\Records;

use App\Filament\Resources\Records\Pages\CreateRecord;
use App\Filament\Resources\Records\Pages\EditRecord;
use App\Filament\Resources\Records\Pages\ListRecords;
use App\Filament\Resources\Records\Schemas\RecordForm;
use App\Filament\Resources\Records\Tables\RecordsTable;
use App\Models\Record;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecordResource extends Resource
{
    protected static ?string $model = Record::class;

    protected static ?string $modelLabel = 'Expediente';

    protected static ?string $pluralModelLabel = 'Expedientes';

    protected static ?string $navigationLabel = 'Expedientes';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('patient_id')
                    ->label('Elegir Paciente')
                    ->relationship('patient', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('appointment_id')
                    ->label('Elegir Cita')
                    ->relationship('appointment', 'date')
                    ->required(),
                \Filament\Forms\Components\RichEditor::make('session_notes')
                    ->label('Notas Privadas')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('expediente_num')
                    ->label('N° Expediente')
                    ->getStateUsing(function ($record) {
                        // Contamos cuántos expedientes tiene este paciente ANTES O IGUAL a este registro
                        // para darle un número secuencial (1, 2, 3...)
                        return 'Expediente ' . \App\Models\Record::where('patient_id', $record->patient_id)
                            ->where('id', '<=', $record->id)
                            ->count();
                    })
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('appointment.date')
                    ->label('Fecha de Cita')
                    ->date()
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecords::route('/'),
            'create' => CreateRecord::route('/create'),
            'edit' => EditRecord::route('/{record}/edit'),
        ];
    }
}
