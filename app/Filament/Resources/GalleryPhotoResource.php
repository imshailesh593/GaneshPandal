<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryPhotoResource\Pages;
use App\Models\GalleryPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryPhotoResource extends Resource
{
    protected static ?string $model = GalleryPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Events & Content';

    protected static ?string $navigationLabel = 'Gallery';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Photo')
                    ->image()
                    ->disk('public')
                    ->directory('gallery')
                    ->imageEditor()
                    ->required(),
                Forms\Components\TextInput::make('caption')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->helperText('Lower numbers show first.')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('caption')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListGalleryPhotos::route('/'),
            'create' => Pages\CreateGalleryPhoto::route('/create'),
            'edit' => Pages\EditGalleryPhoto::route('/{record}/edit'),
        ];
    }
}
