<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            @if(in_array(Auth::user()->tipo,[2,2.5]))
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Pedagogos/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <hr>
            @endif
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Pedagogos/list')}}">
                    <thead>
                        <tr>
                            <th style="text-align:center;" scope="col">Nome</th>
                            @if(in_array(Auth::user()->tipo,[2,2.5]))<th style="text-align:center;" scope="col">Escola</th>@endif
                            <th style="text-align:center;" scope="col">Email</th>
                            <th style="text-align:center;" scope="col">Tipo de Contrato</th>
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