<p style="float: left">Kepada Yth.</p> <p style="float: right">{{ @$date }}</p>
<br>
<p style="float: left">
	Pemilik toko {{ @$store }}<br>
	di tempat,<br><br>

	Dengan Hormat,<br>
	Berikut kami informasikan kebutuhan produk yang harus di-order dikarenakan saat ini terdapat produk yang OOS :<br>
</p>
	<table border="1px" bordercolor="#E0E0E0" style="float: left;">
		<tr>
			<th width="40px" style="padding: 5px;">No.</th>
			<th width="200px" align="left" style="padding: 5px;">Model</th>
			<th width="200px" align="left" style="padding: 5px;">Name</th>
			<th width="200px">Unit Price</th>
			<th width="90px">Quantity</th>
			<th width="200px">Value</th>
		</tr>
		@php
			$total = 0;
			$total_qty = 0;
		@endphp
	@foreach($data as $key => $value)
		@php
			$total += $value['value'];
			$total_qty += $value['request_qty'];
			$price = $value['value'] / $value['request_qty'];
		@endphp
		<tr>
			<td style="padding: 5px;" align="center">{!! @$key+1 !!}</td>
			<td style="padding: 5px;">{{ @$value['model'] }}</td>
			<td style="padding: 5px;">{{ @$value['name'] }}</td>
			<td align="center">{!! number_format($price) !!}</td>
			<td align="center">{!! number_format($value['request_qty']) !!}</td>
			<td align="center">{!! number_format($value['value']) !!}</td>
		</tr>
	@endforeach
		<tr>
			<th colspan="4" align="center" style="padding: 5px;">Total</th>
			<th>{!! number_format($total_qty) !!}</th>
			<th>{!! number_format($total) !!}</th>
		</tr>
	</table>
<p style="float: left">
	<br>
	Terima kasih atas kerjasamanya & kami menungu SURAT PESANAN ORDER (SP0)<br>
	Hormat saya,<br><br><br>
	{{ @$user }}
	<br><br>
	Mengetahui:<br>
	{{ @$dm }}<br>
	{{ @$supervisor }}<br>
	<br><br>
	*note: {{ @$note }}
</p>