<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Example 2</title>
    <style type="text/css">
      @font-face {
        font-family: SourceSansPro;
        src: url(SourceSansPro-Regular.ttf);
      }

      .clearfix:after {
        content: "";
        display: table;
        clear: both;
      }

      a {
        color: #0087C3;
        text-decoration: none;
      }

      body {
        position: relative;
        width: 21cm;  
        height: 29.7cm; 
        margin: 0 auto; 
        color: #555555;
        background: #FFFFFF; 
        font-family: Arial, sans-serif; 
        font-size: 14px; 
        font-family: SourceSansPro;
      }

      header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
      }

      #logo {
        float: right;
        margin-top: 8px;
      }

      #logo img {
        height: 70px;
      }

      #company {
        float: left;
        text-align: left;
      }


      #details {
        margin-bottom: 50px;
      }

      #client {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
        float: left;
      }

      #client .to {
        color: #777777;
      }

      h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
      }

      #invoice {
        float: right;
        text-align: right;
      }

      #invoice h1 {
        color: #0087C3;
        font-size: 2.4em;
        line-height: 1em;
        font-weight: normal;
        margin: 0  0 10px 0;
      }

      #invoice .date {
        font-size: 1.1em;
        color: #777777;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
      }

      table th,
      table td {
        padding: 5px;
        background: #EEEEEE;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
      }

      table th {
        white-space: nowrap;        
        font-weight: normal;
      }

      table td {
        text-align: right;
      }

      table td h3{
        color: #fff;
        font-size: 1.2em;
        font-weight: normal;
        margin: 0 0 0 0;
      }

      table .no {
        color: #FFFFFF;
        font-size: 1.2em;
        background: #252525;
        text-align: left;
        margin-left: 5px;
      }

      table .desc {
        text-align: left;
      }

      table .unit {
        background: #DDDDDD;
      }

      table .qty {
      }

      table .total {
        background: #4a90e2;
        color: #FFFFFF;
      }

      table td.unit,
      table td.qty,
      table td.total {
        font-size: 1.2em;
      }

      table tbody tr:last-child td {
        border: none;
      }

      table tfoot td {
        padding: 10px 20px;
        background: #FFFFFF;
        border-bottom: none;
        font-size: 1.2em;
        white-space: nowrap; 
        border-top: 1px solid #AAAAAA; 
      }

      table tfoot tr:first-child td {
        border-top: none; 
      }

      table tfoot tr:last-child td {
        color: #4a90e2;
        font-size: 1.4em;
        border-top: 1px solid #57B223; 

      }

      table tfoot tr td:first-child {
        border: none;
      }

      #thanks{
        font-size: 2em;
        margin-bottom: 50px;
      }

      #notices{
        padding-left: 6px;
        border-left: 6px solid #0087C3;  
      }

      #notices .notice {
        font-size: 0em;
      }

      footer {
        color: #777777;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
       
        padding: 8px 0;
        text-align: center;
      }

    </style>
  </head>
  <body>
<header class="clearfix">
<div id="company">
    <div id="client">
      <div class="to">$propiedades</div>
      <h2 class="name">John Doe</h2>
      <div class="address">796 Silver Harbour, TX 79273, US</div>
      <div class="email"><a href="mailto:john@example.com">john@example.com</a></div>
    </div>
</div>
</header>
<div>&nbsp; &nbsp;</div>
<div>&nbsp; &nbsp;</div>
<table style="height: 143px; width: 792px; float: left;" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th class="unit" style="width: 73px;"><strong>Reserva</strong></th>
<th class="unit" style="width: 454px;">&nbsp;</th>
</tr>
</thead>
<tbody>
<tr>
<td class="no" style="width: 73px;">Nombre</td>
<td class="desc" style="width: 454px;">&nbsp; &nbsp;Nombre&nbsp;</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Apellido</td>
<td class="desc" style="width: 454px;">&nbsp; &nbsp;Apellido</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Direccion</td>
<td class="desc" style="width: 454px;">&nbsp;</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Ciudad</td>
<td class="desc" style="width: 454px;">&nbsp;</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Pais</td>
<td class="desc" style="width: 454px;">&nbsp;</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Telefono</td>
<td class="desc" style="width: 454px;">&nbsp;</td>
</tr>
<tr>
<td class="no" style="width: 73px;">Email</td>
<td class="desc" style="width: 454px;">&nbsp;</td>
</tr>
</tbody>
</table>
<div>&nbsp; &nbsp;</div>
<h3><strong>Detalle de reserva, y estadia</strong></h3>
<table style="height: 143px; width: 792px; float: left;" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th class="unit" style="width: 398px; text-align: justify;">Reserva No 56 - Tipo de habitaci&oacute;n: W Individual - 1 Hu&eacute;spedes - 1 Noches - Checkin 22-12-<br />2017 - Checkout 23-12-2017 - Incluye:</th>
<th class="total" style="width: 398px; text-align: justify;">&nbsp;</th>
</tr>
<tr>
<th class="unit" style="width: 398px; text-align: right;"><strong>Subtotal</strong></th>
<th class="total" style="width: 398px;">&nbsp;</th>
</tr>
<tr>
<th class="unit" style="width: 398px; text-align: right;"><strong>IVA</strong></th>
<th class="total" style="width: 398px;">&nbsp;</th>
</tr>
<tr>
<th class="unit" style="width: 398px; text-align: right;"><strong>Total</strong></th>
<th class="total" style="width: 398px;">holaa</th>
</tr>
<tr>
<th class="unit" style="width: 398px; text-align: right;"><strong>Por pagar</strong></th>
<th class="total" style="width: 398px;">&nbsp;</th>
</tr>
</thead>
</table>
<div>&nbsp; &nbsp;</div>
<div id="thanks">Gracias a ti!</div>
<footer>Este invoice fue creado con la informacion de tu reserva</footer>