<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->disabled(),
                TextInput::make('orderable_type')
                    ->disabled(),
                TextInput::make('orderable_id')
                    ->disabled(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled(),
                TextInput::make('buyer_name')
                    ->disabled(),
                TextInput::make('buyer_email')
                    ->disabled(),
                TextInput::make('buyer_phone')
                    ->disabled(),
                TextInput::make('amount')
                    ->numeric()
                    ->disabled(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->helperText('Ubah manual hanya untuk kasus khusus/sengketa pembayaran.'),
                TextInput::make('payment_type')
                    ->disabled(),
                DateTimePicker::make('account_provisioned_at')
                    ->disabled(),
                DateTimePicker::make('paid_at')
                    ->disabled(),
            ]);
    }
}
