<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShoeResource\Pages;
use App\Filament\Resources\ShoeResource\RelationManagers;
use App\Models\Shoe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShoeResource extends Resource
{
    protected static ?string $model = Shoe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Shoe Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('IDR '),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->required(),
                        Forms\Components\Repeater::make('photos')
                        ->relationship('shoe_photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->required()
                                    ->image()                                    ,
                            ]),
                        Forms\Components\Repeater::make('sizes')
                        ->relationship('shoe_sizes')
                            ->schema([
                                Forms\Components\TextInput::make('size')
                                    ->required()                                ,
                            ])
                    ]),
                Forms\Components\Fieldset::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('about')
                        ->columnSpan(2)
                            ->required(),
                        Forms\Components\Select::make('is_popular')
                            ->options([
                                true => 'Popular',
                                false => 'Not Popular',
                            ])
                            ->default(false)
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->default(0)
                            ->required()
                            ->numeric()
                            ->prefix('Qty: '),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('thumnail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListShoes::route('/'),
            'create' => Pages\CreateShoe::route('/create'),
            'edit' => Pages\EditShoe::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
