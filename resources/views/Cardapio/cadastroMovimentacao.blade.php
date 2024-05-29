<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Movimentacoes/Save')}}" method="POST">
            @csrf
            @if(session('success'))
            <div class="col-sm-12 shadow p-2 bg-success text-white">
                <strong>{{session('success')}}</strong>
            </div>
            @elseif(session('error'))
            <div class="col-sm-12 shadow p-2 bg-danger text-white">
                <strong>{{session('error')}}</strong>
            </div>
            <br>
            @endif
            @if(isset($Registro->IDMerenda))
            <input type="hidden" value="{{$Registro->IDMerenda}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($Estoque)}}
               </pre> --}}
                <div class="row">
                    <div class="col-sm-4">
                        <label>Nome do Item</label>
                        <select name="IDEstoque" class="form-control">
                            <option value="">Selecione</option>
                            @foreach($Estoque as $e)
                            <option value="{{$e->IDEstoque}}">{{$e->Item}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Tipo de Movimentação</label>
                        <select name="TPMovimentacao" class="form-control">
                            <option value="1">Entrada</option>
                            <option value="0">Saída</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Quantidade</label>
                        <input type="text" name="Quantidade" class="form-control" value="{{isset($Registro) ? $Registro->Quantidade : ''}}">
                    </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Movimentacoes/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>