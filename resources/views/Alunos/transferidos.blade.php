<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Alunos/Transferidos/list')}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Escola de Origem</th>
                        <th style="text-align:center;" scope="col">Data da Transferência</th>
                        <th style="text-align:center;" scope="col">Justificativa</th>
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