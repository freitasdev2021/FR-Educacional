<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                @if(in_array(Auth::user()->tipo,[2,4]))
                <div class="col-auto">
                    <a href="{{route('Escolas/Salas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                @endif
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Escolas/Salas/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Sala</th>
                        <th style="text-align:center;" scope="col">Tamanho</th>
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>