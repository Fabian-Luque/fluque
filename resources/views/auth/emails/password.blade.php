Haga click aqui para restablecer : 
<a href="{{ $link = url('reset/password', $request->token).'?email='.urlencode($request->destino) }}"> 
{{ $link }} 
</a>
