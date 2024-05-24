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
                <div class="col-auto">
                    <a href="{{route('Diretores/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Auxiliares/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Nome</th>
                        <th style="text-align:center;" scope="col">Admissão</th>
                        <th style="text-align:center;" scope="col">Contrato</th>
                        <th style="text-align:center;" scope="col">Escola</th>
                        <th style="text-align:center;" scope="col">Endereço</th>
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