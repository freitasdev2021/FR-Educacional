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
                {{$c->Assunto}} - {{($c->STComunicacao == 1) ? 'Ativa' : 'Encerrada'}}
             </div>
             <div class="card-body">
                {!! $c->Mensagem !!}
                <hr>
                <label>Mensagens</label>
                <ul>
                    @foreach(json_decode($c->Mensagens) as $m)
                    <li>{{$m->Mensagem}}</li>
                    @endforeach
                </ul>
                <hr>
                <form action="{{route('CI/Resposta',['IDUser'=>$IDUser,'IDComunicacao'=>$c->id])}}" method="POST">
                  @method("PATCH")
                  @csrf
                  <div>
                     <label>Resposta</label>
                     <textarea name="Mensagem" class="form-control"></textarea>
                  </div>
                  <br>
                  <button class="btn btn-fr">Responder</button>
                </form>
                <br>
                <label>Respostas</label>
                <ul>
                  @foreach($Respostas as $r)
                  <li>{{$r->Resposta}}</li>
                  @endforeach
                </ul>
             </div>
          </div>
        @endforeach
       </div>
    </div>
 </x-educacional-layout>