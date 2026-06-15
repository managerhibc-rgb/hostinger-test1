<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttachmentResource\Pages;
use App\Models\Attachment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $navigationGroup = 'Work';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attachable_type')
                    ->options([
                        'App\\Models\\Task' => 'Task',
                        'App\\Models\\Project' => 'Project',
                        'App\\Models\\Comment' => 'Comment',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('attachable_id')
                    ->label('Attachable')
                    ->options(function (Forms\Get $get) {
                        $type = $get('attachable_type');
                        if (! $type) {
                            return [];
                        }
                        return $type::query()->pluck(match ($type) {
                            'App\\Models\\Task' => 'title',
                            'App\\Models\\Project' => 'name',
                            'App\\Models\\Comment' => 'content',
                            default => 'id',
                        }, 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory('attachments')
                    ->preserveFilenames()
                    ->maxSize(10240),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attachable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('attachable_type')
                    ->options([
                        'App\\Models\\Task' => 'Task',
                        'App\\Models\\Project' => 'Project',
                        'App\\Models\\Comment' => 'Comment',
                    ])
                    ->label('Type'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-arrow-down-tray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }
}
