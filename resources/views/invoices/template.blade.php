<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<title>Invoice - {{$invoice->ref_no}}</title>

<style type="text/css">
    * {
      font-family: Verdana, Arial, sans-serif;
    }
    table{
      font-size: 15px;
      border-collapse: collapse;
      padding: 15px;
    }
    tfoot tr td{
      font-weight: bold;
      font-size: 15px;
    }

    @page {
      margin: 0px;
    }

    body {
      margin: 0px;
    }
    .information {
      background-color: #2975f0;
      color: #FFF;
    }

    .information .logo {
      padding-left: 15px;
    }
    .currency {
      font-family: DejaVu Sans, sans-serif, Verdana, Arial;
    }

    hr.divider{
        margin-left: 20px;
        margin-right: 20px;
        color: #2975f0;
    }
    .invoice_total {
        color: #2975f0;
        font-size: 30px;
    }
    .invoice_text{
        font-size: 45px;
        text-transform:uppercase;
    }
    .company_address {
      width: 20%;
      margin-top: 19px;
    }

    .blue {
        color: #2975f0;
    }
    .grey {
        color: #9E9E9E;
    }
    .column {
      float: left;
      width: 33.33%;
    }
  /* Clear floats after the columns */
  .row:after {
    display: table;
    clear: both;
  }
  .terms {
    width: 50%;
    float: left;
    margin-left: 20px;
  }
  .tr_border {
    border-bottom: 1px solid #9E9E9E;
    margin-left: 20px;
    margin-right: 20px;
    padding-top: 18px;
    padding-bottom: 18px;
  }
  table#invoice {
    margin-left: 18px;
    margin-right: 18px;
    font-size: 15px;
    border-collapse: collapse;
  }
  .invoice_footer {
    padding-top:50px;
  }
  .v_align {
    vertical-align:top;
  }
</style>

</head>
<body>

<!-- company Address -->
  <table width="100%" class="information">
    <tr>
      <td align="left" style="width: 50%;">
        @if(!empty($system['logo']))
          <img src="uploads/{{$system['logo']}}" alt="logo" width="150" class="logo"/>
        @else
            <span class="invoice_text">
                {{trans('messages.invoice')}}
            </span>
        @endif
      </td>
      <td align="center" style="width: 20%;">
            {{ config('app.name') }} <br>
        @if(!empty($system['email']))
            {{$system['email']}} <br>
        @endif
        @if(!empty($system['mobile']))
            {{$system['mobile']}} <br>
        @endif
        @if(!empty($system['alternate_contact_no']))
            {{$system['alternate_contact_no']}} <br>
        @endif
      </td>
      <td align="right" class="company_address" style="width: 30%;">
            @if(!empty($system['address_line_1']))
                {{$system['address_line_1']}} <br>
            @endif
            @if(!empty($system['address_line_2']))
                {{$system['address_line_2']}} <br>
            @endif
            @if(!empty($system['city']))
                {{$system['city']}} <br>
            @endif
            @if(!empty($system['state']))
                {{$system['state']}} <br>
            @endif
            @if(!empty($system['country']))
                {{$system['country']}} <br>
            @endif
            @if(!empty($system['zip_code']))
                {{$system['zip_code']}} <br>
            @endif
      </td>
    </tr>
  </table>

  <br/><br/>

  @php
    $currency = $customer_currency->currency->symbol;

    $invoice_total = $currency.' '.(number_format($invoice->total, 2));

    $discount_amount = $currency.' '.(number_format($invoice->discount_amount, 2));
  @endphp
  <!-- Customer Address -->
  <div class="row">
    <div class="column" style="margin-left: 22px;">
      <span class="grey">
          {{trans('messages.billed_to')}} <br>
      </span>
        {{$invoice->customer->company}} <br>
        @if(!empty($invoice->customer->billing_address))
            {{$invoice->customer->billing_address}} <br>
        @endif
        @if(!empty($invoice->customer->tax_number))
            {{$invoice->customer->tax_number}} <br>
        @endif
        @if(!empty($invoice->customer->mobile))
            {{$invoice->customer->mobile}} <br>
        @endif
        @if(!empty($invoice->customer->city))
            {{$invoice->customer->city}}
        @endif
        @if(!empty($invoice->customer->state))
            , {{$invoice->customer->state}}
        @endif
        @if(!empty($invoice->customer->country))
            , {{$invoice->customer->country}} <br>
        @endif
        @if(!empty($invoice->customer->zip_code))
          {{$invoice->customer->zip_code}} <br>
        @endif
    </div>

    <div class="column">
      <span class="grey">
        {{trans('messages.invoice_number')}} <br>
      </span>
      {{$invoice->ref_no}}<br><br>
      <span class="grey">
        {{trans('messages.date_of_issue')}}
      </span><br>
      {{$invoice->transaction_date}}
    </div>

    <div class="column">
      <span class="grey" style="margin-right: 22px;">
          {{trans('messages.invoice_total')}} <br>
      </span>
        <span class="currency invoice_total">
            {{$invoice_total}}
        </span>
    </div>
  </div>
  <br><br>
  <hr class="divider">
  <!-- invoice description -->
  <table width="100%" id="invoice">
    <thead class="blue">
      <tr>
        <th> # </th>
        <th> {{trans('messages.description')}} </th>
        <th> 
          {{trans('messages.rate')}} 
          <span class="currency">
          ({{$currency}})
          </span>
        </th>
        <th> {{trans('messages.quantity')}} </th>
        <th> {{trans('messages.tax')}} (%) </th>
        <th> {{trans('messages.total')}}
          <span class="currency">
            ({{$currency}})
          </span>
        </th>
      </tr>
    </thead>

    <tbody>
      @php
        $total_tax = 0;
        $subtotal = 0;
        $line_total = 0;
        $line_tax = 0;
      @endphp
      @foreach($invoice->invoiceLines as $line)
        @php
            $line_tax += $line->tax;
            $line_total += $line->total;
            $currency = $customer_currency->currency->symbol;

            $subtotal = $currency.' '.(number_format($line_total, 2));

            $total_tax = $currency.' '.(number_format($line_tax, 2));

            $rate = number_format($line->rate, 2);
            $tax = number_format($line->tax, 2);
            $total = number_format($line->total, 2);
            $quantity = number_format($line->quantity, 2) .' '. $line->unit;

        @endphp
        <tr>
          <th class="tr_border v_align"> {{$loop->iteration}} </th>
          <td class="tr_border"> 
              {{$line->short_description}} 
              @if($line->long_description)
                <br>
                <span class="grey">
                    <small>{{$line->long_description}}</small>
                </span>
              @endif
          </td>
          <td class="tr_border">
            <span class="currency">
              {{$rate}}
            </span>
          </td>
          <td class="tr_border">
            {{$quantity}}
          </td>
          <td class="tr_border">
            <span class="currency">
              {{$tax}}
            </span>
          </td>
          <td class="tr_border"> 
            <span class="currency">
              {{$total}}
            </span>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4"></td>
            <th class="invoice_footer"> 
                <span class="blue">
                    {{trans('messages.subtotal')}}
            </th>
            <td class="invoice_footer"> 
              <span class="currency">
                {{$subtotal}}
              </span>
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <th>
                <span class="blue">
                    {{trans('messages.discount')}}
                </span>
            </th>
            <td> 
              <span class="currency">
                {{$discount_amount}}
              </span>
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <th>
                <span class="blue">
                    {{trans('messages.tax')}}
                </span>
            </th>
            <td> 
              <span class="currency">
                {{$total_tax}}
              </span>
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <th>
                <span class="blue">
                    {{trans('messages.total')}}
                </span>
            </th>
            <td> 
              <span class="currency">
                {{$invoice_total}}
              </span>
            </td>
        </tr>
    </tfoot>
  </table>

  <div class="row">
    <div class="terms">
      @if(!empty($invoice->terms))
        <span class="grey">
            {{trans('messages.invoice_terms')}} <br>
          </span>
          {{$invoice->terms}}
      @endif
    </div>
  </div>

</body>
</html>