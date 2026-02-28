<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Order</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            line-height: 20px;
            font-size: 14px;
            direction: ltr;
            text-align: left;
        }

        .header {
            background-color: #2d4e52;
        }

        .title {
            color: #2d4e52;
            font-weight: bold;

        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-header div {
            width: 33%;
        }

        .invoice-header h1 {
            font-size: 24px;
            margin: 0;
        }

        .invoice-header span {
            display: block;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .table th {
            background-color: #f0f0f0;
        }

        .table-footer {
            text-align: left;
            margin-top: 20px;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
        }

        .note {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e53935;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 30px;
        }

        .summary td {
            padding: 5px;
        }

        .summary .total td {
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="content">
        <?php
    $customerFromUser = $order->user?->customers?->first();
    $customerDirect   = $order->customer?->first();

    $customer = $customerFromUser ?? $customerDirect;
    ?>
        <div class="invoice-header">
            <div>
                <h1 class="title" style="font-size: 20px">Order</h1>
                <span class="title"> Date of Order: </span><span dir="ltr">{{
                    \Carbon\Carbon::createFromTimeString($order->created_at)->format('d-m-Y h:m A') }}</span><br>
                <span class="title">Invoice Number: </span><span dir="ltr">{{ @$order->reference }}</span><br>
                <span class="title">Order Number: </span><span dir="ltr">{{ @$order->id }}</span><br>
                <span class="title">Payment Method: </span><span dir="ltr">{{
                    @$order->transactions->first()->driver=="coffline"?"Cash on
                    Delivery":$order->transactions->first()->driver }}</span><br>

            </div>
            <div>
                <span class="title">Customer Name: </span><span dir="ltr"><?php echo e(
    $customer
        ? $customer->first_name . ' ' . $customer->last_name
        : 'Admin'
); ?></span><br>
                <span class="title">Phone Number: </span><span dir="ltr"><?php echo e($customer->phone_number ?? '—'); ?></span><br>
                <span class="title">City: </span><span dir="ltr">{{ $order->addresses()->first()->city }}</span><br>
                <span class="title">Address: </span><span dir="ltr">
                    {{ $order->addresses()->first()->country()->first()->name}} - {{
                    $order->addresses()->first()->city}}

                    @if($order->addresses()->first()->line_one)
                    - {{ $order->addresses()->first()->line_one}}
                    @endif
                </span>

                <br>
                <span class="title">Email: </span><span dir="ltr"><?php echo e($order->user->email ?? $customer->email ?? '—'); ?></span><br>
            </div>
        </div>
        <h4 style="text-align: center;font-size: 18px" class="title">Order Details:
        </h4>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th class="title header">Total</th>
                    <th class="title header">Price</th>

                    <th class="title header">Products</th>
                    <th class="title header">Quantity</th>


                </tr>
            </thead>
            <tbody>
                {{$weight=0}}
                {{$density=0}}
                @foreach($order->lines as $line)


                @if($line->type ==='physical')
                <tr>
                    <td>{{ $line->total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}</td>
                    <td>{{ $line->unit_price->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}</td>
                    <td> {{$line->first()->purchasable->getDescription()['en']}}</td>
                    <td>{{ $line->quantity }}</td>
                    @if(isset($line->purchasable->product->attribute_data['weight']))
                    {{$weight+=(int)$line->purchasable->product->attribute_data['weight']->getValue()}}
                    @endif
                    @if(isset($line->purchasable->product->attribute_data['density']))
                    {{$density+=(int)$line->purchasable->product->attribute_data['density']->getValue()}}
                    @endif

                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="summary" style="padding-top: 20%">
            <table>
                <tr>
                    <td class="title">Subtotal: </td>
                    <td>{{ $order->sub_total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}</td>
                </tr>
                <tr>
                    <td class="title">Shipping & Delivery: </td>
                    <td>{{ $order->shipping_total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}} </td>
                </tr>
                
                @if(!empty($order->discount_breakdown) && isset($order->discount_breakdown[0]))
                <tr>
                    <td class="title">Coupon Code: </td>
                    <td>
                        {{ \Lunar\Models\Discount::find($order->discount_breakdown[0]->discount_id)->name }}
                    </td>
                </tr>
                @endif
                
                @if(!empty($order->discount_breakdown) && isset($order->discount_breakdown[0]))
                <tr>
                    <td class="title">Coupon Discount %: </td>
                    <td>
                        @php
                        $discount = \Lunar\Models\Discount::find($order->discount_breakdown[0]->discount_id);
                        $restriction = collect(json_decode($discount->restriction, true));
                        @endphp

                        @if($restriction->has('percentage'))
                        {{ $restriction->get('percentage') }}%
                        @else
                        {{ $restriction->first() }} {{ $order->currency()->first()->code }}
                        @endif
                    </td>
                </tr>
                @endif

                <tr>
                    <td class="title">Redeemed Points:
                    </td>
                    <td>{{$order->points_used}} </td>
                </tr>
                <tr>
                    <td class="title">Value of Redeemed Points: </td>
                    <td>{{$order->point_price*$order->points_used}}&nbsp;{{$order->currency()->first()->code}}</td>
                </tr>
                <tr class="total">
                    <td class="title">Grand Total: </td>
                    <td>{{ $order->total->decimal(true) }}&nbsp;{{$order->currency()->first()->code}}</td>
                </tr>
            </table>
        </div>

        @if($order->addresses()->first()->notes)
        <div class="note">
            <strong>Delivery Instructions: </strong> {{ $order->addresses()->first()->notes }}
        </div>
        @endif
        <div class="note">
            <strong>Total Weight: </strong>{{$weight}}
        </div>
        <div class="note">
            <strong>Total Density: </strong>{{$density}}
        </div>
        <div class="note">
            <strong>Recipient Signature: </strong> __________________
        </div>
        <div class="note">
            <strong>Delivery Date: </strong> __________________
        </div>
    </div>
</body>

</html>