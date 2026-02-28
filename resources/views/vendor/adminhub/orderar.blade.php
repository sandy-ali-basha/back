<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <title>فاتورة</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
      body {
          line-height:20px;
          font-size: 14px;
          direction: rtl;
          text-align: right;
      }
      .header{
          background-color:  #2d4e52;
      }
      .title{
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

      .table th, .table td {
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
          <h1 class="title" style="font-size: 20px">فاتورة</h1>
          <span class="title"> تاريخ الطلب:&nbsp;</span
          ><span dir="ltr"
            >{{
            \Carbon\Carbon::createFromTimeString($order->created_at)->format('d-m-Y
            h:m A') }}</span
          ><br />
          <span class="title">رقم الفاتورة:&nbsp;</span
          ><span dir="ltr">{{ @$order->reference }}</span><br />
          <span class="title">رقم الطلب:&nbsp;</span
          ><span dir="ltr">{{ @$order->id }}</span><br />
          <span class="title">طريقة الدفع:&nbsp;</span
          ><span dir="ltr"
            >{{ @$order->transactions->first()->driver=="coffline"?"Cash on
            Delivery":$order->transactions->first()->driver }}</span
          ><br />
        </div>
        <div>
          <span class="title">اسم الزبون: &nbsp;</span
          ><span dir="ltr"
            ><?php echo e(
    $customer
        ? $customer->first_name . ' ' . $customer->last_name
        : 'Admin'
); ?></span
          ><br />
          <span class="title">رقم الهاتف: &nbsp;</span
          ><span dir="ltr"
            ><?php echo e($customer->phone_number ?? '—'); ?></span
          ><br />
          <span class="title">المدينة: &nbsp;</span
          ><span dir="ltr">{{ $order->addresses()->first()->city }}</span><br />
          <span class="title">العنوان: &nbsp;</span
          ><span dir="ltr">
            {{ $order->addresses()->first()->country()->first()->name}} - {{
            $order->addresses()->first()->city}}
            @if($order->addresses()->first()->line_one) - {{
            $order->addresses()->first()->line_one}} @endif
          </span>

          <br />
          <span class="title">البريد الالكتروني:&nbsp;</span
          ><span dir="ltr"><?php echo e($order->user->email ?? $customer->email ?? '—'); ?></span><br />
        </div>
      </div>
      <h4 style="text-align: center;font-size: 18px" class="title">
        تفاصيل الطلب
      </h4>
      <br />
      <table class="table">
        <thead>
          <tr>
            <th class="title header">الاجمالي</th>
            <th class="title header">السعر</th>

            <th class="title header">المنتجات</th>
            <th class="title header">الكميّة</th>
            <th class="title header">x</th>
          </tr>
        </thead>
        <tbody>
          {{$weight=0}} {{$density=0}} @foreach($order->lines as $line)
          @if($line->type ==='physical')
          <tr>
            <td>
              {{
              $line->total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}
            </td>
            <td>
              {{
              $line->unit_price->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}
            </td>
            <td>{{$line->first()->purchasable->getDescription()['en']}}</td>
            <td>{{ $line->quantity }}</td>
          </tr>
          @endif @endforeach
        </tbody>
      </table>
      <br />
      <div class="summary" style="padding-top: 20%">
        <table>
          <tr>
            <td class="title">الإجمالي</td>
            <td>
              {{
              $order->sub_total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}
            </td>
          </tr>
          <tr>
            <td class="title">الشحن والتوصيل</td>
            <td>
              {{
              $order->shipping_total->decimal(true)}}&nbsp;{{$order->currency()->first()->code}}
            </td>
          </tr>
          @if(!empty($order->discount_breakdown) &&
          isset($order->discount_breakdown[0])) @php $discountId =
          $order->discount_breakdown[0]->discount_id; $discount =
          \Lunar\Models\Discount::find($discountId); $restriction =
          json_decode($discount->restriction, true); $value =
          collect($restriction)->first(); $isPercentage =
          collect($restriction)->has('percentage'); @endphp

          <tr>
            <td class="title">رمز الكوبون</td>
            <td>{{ $discount->name }}</td>
          </tr>

          <tr>
            <td class="title">حسم الكوبون % :</td>
            <td>
              {{ $isPercentage ? $value.'%' : $value.'
              '.$order->currency()->first()->code }}
            </td>
          </tr>
          @else
          <tr>
            <td class="title">لا يوجد كوبون</td>
            <td>—</td>
          </tr>
          @endif

          <tr>
            <td class="title">النقاط المخصومة:</td>
            <td>{{$order->points_used}}</td>
          </tr>
          <tr>
            <td class="title">قيمة النقاط المخصومة:</td>
            <td>
              {{$order->point_price*$order->points_used}}&nbsp;{{$order->currency()->first()->code}}
            </td>
          </tr>
          <tr class="total">
            <td class="title">المجموع الإجمالي:</td>
            <td>
              {{ $order->total->decimal(true)
              }}&nbsp;{{$order->currency()->first()->code}}
            </td>
          </tr>
        </table>
      </div>

      @if($order->addresses()->first()->delivery_instructions)
      <div class="note">
        <strong>تعليمات التسليم:</strong> {{
        $order->addresses()->first()->delivery_instructions }}
      </div>
      @endif
      <div class="note"><strong>الوزن الكلي: </strong>{{$weight}}</div>
      <div class="note">
        <strong>الكتلة الحجمية الكلية: </strong>{{$density}}
      </div>
      <div class="note"><strong>توقيع المستلم:</strong> __________________</div>
      <div class="note"><strong>تاريخ التسليم:</strong> __________________</div>
    </div>
  </body>
</html>
