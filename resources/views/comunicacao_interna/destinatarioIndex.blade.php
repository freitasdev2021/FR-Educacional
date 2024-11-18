<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        @foreach($Comunicacoes as $c)
          <div class="card">
             <div class="card-header bg-fr text-white">
                {{$c->Assunto}}
             </div>
             <div class="card-body">
                {!! $c->Mensagem !!}
                <hr>
                <ul>
                    @foreach(json_decode($c->Mensagens) as $m)
                    <li>{{$m->Mensagem}}</li>
                    @endforeach
                </ul>
             </div>
          </div>
        @endforeach
       </div>
    </div>
 </x-educacional-layout>