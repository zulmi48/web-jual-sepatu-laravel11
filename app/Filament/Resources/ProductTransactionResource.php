<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTransactionResource\Pages;
use App\Filament\Resources\ProductTransactionResource\RelationManagers;
use App\Models\ProductTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductTransactionResource extends Resource
{
    protected static ?string $model = ProductTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and Price')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('shoe_id ')
                                        ->relationship('shoe', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->afterStateUpdated(
                                            function ($state, callable $get, callable $set) {
                                                $shoe = \App\Models\Shoe::find($state);
                                                $price = $shoe ? $shoe->price : 0;
                                                $quantity = $get('quantity') ?? 1;
                                                $subTotalAmount = $price * $quantity;

                                                $set('price', $price);
                                                $set('sub_total_amount', $subTotalAmount);

                                                $discount = $get('discount_amount') ?? 0;
                                                $grandTotalAmount = $subTotalAmount - $discount;
                                                $set('grand_total_amount', $grandTotalAmount);

                                                $sizes = $shoe ? $shoe->sizes->pluck('size', 'id')->toArray() : [];
                                                $set('shoe_sizes', $sizes);
                                            }
                                        )
                                        ->afterStateHydrated(function (callable $get, callable $set, $state) {
                                            $shoeId = $state;
                                            if ($shoeId) {
                                                $shoe = \App\Models\Shoe::find($shoeId);
                                                $sizes = $shoe ? $shoe->sizes->pluck('size', 'id')->toArray() : [];
                                                $set('shoe_sizes', $sizes);
                                            }
                                        }),
                                    Forms\Components\Select::make('shoe_size_id')
                                        ->label('Shoe Size')
                                        ->options(function (callable $get) {
                                            $sizes = $get('shoe_sizes');
                                            return is_array($sizes) ? $sizes : [];
                                        })
                                        ->required()
                                        ->live(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->required()
                                        ->prefix('Qty')
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $price = $get('price');
                                            $quantity = $state;
                                            $ubTotalAmount = $price * $quantity;

                                            $set('sub_total_amount', $ubTotalAmount);

                                            $discount = $get('discount_amount') ?? 0;
                                            $grandTotalAmount = $ubTotalAmount - $discount;
                                            $set('grand_total_amount', $grandTotalAmount);
                                        }),
                                    Forms\Components\Select::make('promo_code_id')
                                        ->relationship('promoCode', 'code')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $subTotalAmount = $get('sub_total_amount');
                                            $promoCode = \App\Models\PromoCode::find($state);
                                            $discount = $promoCode ? $promoCode->discount_amount : 0;

                                            $set('discount_amount', $discount);

                                            $grandTotalAmount = $subTotalAmount - $discount;
                                            $set('grand_total_amount', $grandTotalAmount);
                                        }),
                                    Forms\Components\TextInput::make('sub_total_amount')
                                        ->required()
                                        ->readonly()
                                        ->numeric()
                                        ->prefix('IDR'),
                                    Forms\Components\TextInput::make('grand_total_amount')
                                        ->required()
                                        ->readonly()
                                        ->numeric()
                                        ->prefix('IDR'),
                                    Forms\Components\TextInput::make('discount_amount')
                                        ->required()
                                        ->readonly()
                                        ->numeric()
                                        ->prefix('IDR'),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->maxLength(255)
                                        ->required(),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->maxLength(255)
                                        ->required(),
                                    Forms\Components\TextInput::make('phone')
                                        ->tel()
                                        ->required(),
                                    Forms\Components\TextInput::make('address')
                                        ->required(),
                                    Forms\Components\TextInput::make('city')
                                        ->required(),
                                    Forms\Components\TextInput::make('post_code')
                                        ->required(),
                                ]),
                        ]),
                    Forms\Components\Wizard\Step::make('Payment Information')
                        ->schema([
                            Forms\Components\TextInput::make('booking_trx_id')
                                ->label('Booking Transaction ID')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\ToggleButtons::make('is_paid')
                                ->label('Apakah sudah membayar?')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    'heroicon-o-x-mark' => false,
                                    'heroicon-o-check' => true,
                                ])
                                ->required(),
                            Forms\Components\FileUpload::make('proof')
                                ->label('Bukti Pembayaran')
                                ->image()
                                ->directory('payment-proofs')
                                ->visibility('private')
                                ->required(fn(callable $get) => $get('is_paid') === true),
                        ]),
                ])
                    ->columnSpanFull()
                    ->columns(1)
                    ->skippable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('shoe.thumbnail'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')
                    ->label('Booking Transaction ID')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->label('Terverifikasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shoe_id')
                    ->label('Shoe')
                    ->relationship('shoe', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Approve')
                    ->label('Approve Payment')
                    ->action(function (ProductTransaction $record) {
                        $record->is_paid = true;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Order Approved')
                            ->body('The payment for transaction ' . $record->booking_trx_id . ' has been approved.')
                            ->success()
                            ->send();
                    })
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ProductTransaction $record) => !$record->is_paid),
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
            'index' => Pages\ListProductTransactions::route('/'),
            'create' => Pages\CreateProductTransaction::route('/create'),
            'edit' => Pages\EditProductTransaction::route('/{record}/edit'),
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
