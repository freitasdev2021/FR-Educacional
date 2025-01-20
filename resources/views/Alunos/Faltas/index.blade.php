<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Alunos/Faltas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
            </div>
            <hr>
            <table class="table table-sm tabela" id="escolas" data-rota="{{route('Alunos/Faltas/list')}}">
                <thead>
                  <tr>
                    <th style="text-align:center;" scope="col">Aluno</th>
                    <th style="text-align:center;" scope="col">Escola</th>
                    <th style="text-align:center;" scope="col">Turma</th>
                    <th style="text-align:center;" scope="col">Aula</th>
                    <th style="text-align:center;" scope="col">Data</th>
                    <th style="text-align:center;" scope="col">Justificativa</th>
                    <th style="text-align:center;" scope="col">Opções</th>
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
            </table>
        </div>
    </div>
</x-educacional-layout>