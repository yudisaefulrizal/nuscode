<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Mail\OrderPaid;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('orderable.name')
                    ->label('Produk'),
                TextColumn::make('buyer_name')
                    ->searchable(),
                TextColumn::make('buyer_email')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'cancelled', 'expired' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('payment_type'),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('resendEmail')
                    ->label('Kirim Ulang Email')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn ($record) => $record->status === 'paid')
                    ->action(function ($record) {
                        Mail::to($record->buyer_email)->send(new OrderPaid($record));

                        Notification::make()
                            ->title('Email berhasil dikirim ulang')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
