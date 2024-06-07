<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Alunos/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Alunos/Transferencias/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Escola de Destino</th>
                        <th style="text-align:center;" scope="col">Turma de Destino</th>
                        <th style="text-align:center;" scope="col">Escola Anterior</th>
                        <th style="text-align:center;" scope="col">Turma Anterior</th>
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