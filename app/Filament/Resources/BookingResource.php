<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('service.name'),
                Tables\Columns\TextColumn::make('mechanic.user.name'),
                Tables\Columns\TextColumn::make('time_slot.date')->date(),
                Tables\Columns\TextColumn::make('time_slot.start_time'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                    }),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'confirmed']);
                })
                ->requiresConfirmation()
                ->visible(fn (Booking $record): bool => $record->status === 'pending'),
            Tables\Actions\Action::make('complete')
                ->action(function (Booking $record) {
                    $record->update(['status' => 'completed']);
                })
                ->requiresConfirmation()
                ->visible(fn (Booking $record): bool => $record->status === 'confirmed'),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
